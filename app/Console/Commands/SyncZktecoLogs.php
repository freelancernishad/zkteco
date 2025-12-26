<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use MehediJaman\LaravelZkteco\LaravelZkteco;
use App\Models\Attendance;
use App\Models\ZkUser;
use Carbon\Carbon;

class SyncZktecoLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zk:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync ZKTeco Attendance Logs to Database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ip = env('ZK_DEVICE_IP', '192.168.0.201'); 
        $port = env('ZK_DEVICE_PORT', 4370);

        $this->info("Connecting to ZKTeco Device ($ip)...");

        try {
            $zk = new LaravelZkteco($ip, $port);
            
            if ($zk->connect()) {
            // 1. Sync Users
            $this->info('Fetching users...');
            $zk->disableDevice();
            $users = $zk->getUser();
            $zk->enableDevice();

            foreach ($users as $u) {
                ZkUser::updateOrCreate(
                    ['userid' => $u['userid']], // Key: Badge ID
                    [
                        'uid' => $u['uid'],
                        'name' => $u['name'],
                        'role' => $u['role'],
                        'password' => $u['password'],
                        'cardno' => $u['cardno']
                    ]
                );
            }
            $this->info('Users synced: ' . count($users));

            // 2. Sync Logs
            $this->info('Fetching attendance logs...');
            // Connection is still open
            $attendance = $zk->getAttendance();
            
            Log::info("ZK Sync: Fetched " . count($attendance) . " records from device.");
            if (count($attendance) > 0) {
                Log::info("ZK Sync: First Record Sample: " . json_encode($attendance[0]));
            } else {
                Log::warning("ZK Sync: No records found on device.");
            }

            $newCount = 0;
            foreach ($attendance as $log) {
                // Check if exists
                $exists = Attendance::where('uid', $log['uid'])
                                    ->where('user_id', $log['id'])
                                    ->where('timestamp', $log['timestamp'])
                                    ->exists();
                
                if (!$exists) {
                    $newLog = Attendance::create([
                        'uid' => $log['uid'],
                        'user_id' => $log['id'],
                        'state' => $log['state'],
                        'timestamp' => $log['timestamp'],
                        'type' => $log['type']
                    ]);
                    $newCount++;

                    // Trigger Webhook if configured
                    $webhookUrl = env('ZK_WEBHOOK_URL');
                    if ($webhookUrl) {
                        try {
                            $user = ZkUser::where('userid', $log['id'])->first();
                            
                            Http::timeout(5)->post($webhookUrl, [
                                'event' => 'attendance.created',
                                'data' => [
                                    'user_id' => $log['id'], // User Badge ID
                                    'timestamp' => $log['timestamp'],
                                    'state' => $log['state'], // 1=Out, 0/Other=In usually
                                    'type' => $log['type'],
                                    'device_uid' => $log['uid'],
                                    'user' => $user ? [
                                        'name' => $user->name,
                                        'cardno' => $user->cardno,
                                        'role' => $user->role,
                                        'uid' => $user->uid
                                    ] : null
                                ]
                            ]);
                        } catch (\Exception $e) {
                            // Log error but don't stop sync
                            $this->error("Webhook Failed: " . $e->getMessage());
                        }
                    }
                }
            }
            
            $this->info("Sync Complete! $newCount new records added.");
        } else {
                $this->error('Connection Failed. Check IP or Network.');
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
