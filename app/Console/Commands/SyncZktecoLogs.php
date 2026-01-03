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

        $this->sync($ip,$port);
    }

    private function sync($ip, $port)
    {
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
            //$this->info('Users synced: ' . count($users));

            // 2. Sync Logs
            //$this->info('Fetching attendance logs...');
            // Connection is still open
            $attendance = $zk->getAttendance();
            Log::info($attendance);
            
            Log::info("ZK Sync: Fetched " . count($attendance) . " records from device.");
            
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
                    $webhookUrl = config('services.zk.webhook_url');
                    
                    if ($webhookUrl) {
                        Log::info("ZK Sync: Triggering webhook for User ID: " . $log['id'] . " to URL: " . $webhookUrl);
                        try {
                            $user = ZkUser::where('userid', $log['id'])->first();
                            
                            if ($user) {
                                Log::info("ZK Sync: Found user " . $user->name . " in database.");
                            } else {
                                Log::warn("ZK Sync: User ID " . $log['id'] . " not found in ZkUser table.");
                            }
                            
                            $response = Http::timeout(10)->post($webhookUrl, [
                                'event' => 'attendance.created',
                                'data' => [
                                    'user_id' => $log['id'],
                                    'timestamp' => $log['timestamp'],
                                    'state' => $log['state'],
                                    'type' => $log['type'],
                                    'device_uid' => $log['uid'],
                                    'user' => $user ? [
                                        'name' => $user->name,
                                        'cardno' => $user->cardno,
                                        'role' => $user->role,
                                        'uid' => $user->uid,
                                        'user_type' => $user->user_type ?? 'student'
                                    ] : null
                                ]
                            ]);

                            if ($response->successful()) {
                                Log::info("ZK Sync: Webhook sent successfully to " . $webhookUrl . " (Status: " . $response->status() . ")");
                            } else {
                                Log::error("ZK Sync: Webhook failed. URL: " . $webhookUrl . " | Status: " . $response->status() . " | Body: " . $response->body());
                            }
                        } catch (\Exception $e) {
                            Log::error("ZK Sync: Webhook Exception: " . $e->getMessage());
                            $this->error("Webhook Failed: " . $e->getMessage());
                        }
                    } else {
                        Log::debug("ZK Sync: ZK_WEBHOOK_URL is not configured in .env.");
                    }
                }
            }
            
            if ($newCount > 0) {
                $this->info("Sync: $newCount new records.");
            }
        } else {
                //$this->error('Connection Failed.');
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
