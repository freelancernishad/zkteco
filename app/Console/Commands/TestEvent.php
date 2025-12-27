<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\AttendanceSynced;
use App\Models\Attendance;
use App\Models\User;

class TestEvent extends Command
{
    protected $signature = 'zk:test-event';
    protected $description = 'Dispatch a mock AttendanceSynced event for testing';

    public function handle()
    {
        $this->info('Dispatching mock attendance event...');

        // Mock a few logs
        $mockLogs = [];
        
        // Try to get a real user if exists, else mock name
        $user = User::first();
        $userName = $user ? $user->name : 'Nishad Hossain';
        $userId = $user ? $user->id : 123;

        $log = new Attendance();
        $log->user_id = $userId;
        $log->timestamp = now()->toDateTimeString();
        $log->state = 0; // Check In
        $log->setRelation('user', $user ?: new User(['name' => $userName]));
        
        $mockLogs[] = $log;

        event(new AttendanceSynced($mockLogs));

        $this->success('Mock event dispatched for ' . $userName);
    }
}
