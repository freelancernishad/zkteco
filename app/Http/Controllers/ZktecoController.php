<?php

namespace App\Http\Controllers;

use MehediJaman\LaravelZkteco\LaravelZkteco;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\ZkUser;
use Illuminate\Support\Facades\Artisan; // Allow manual sync triggering

class ZktecoController extends Controller
{
    /**
     * Connect to the ZKTeco device.
     */
    public function connect()
    {
        // IP Address of the ZKTeco device (CHANGE THIS TO YOUR DEVICE'S IP)
        $ip = '192.168.0.201';
        // Default UDP Port
        $port = 4370;

        $zk = new LaravelZkteco($ip, $port);

        if ($zk->connect()) {
            return response()->json(['message' => 'Connection Successful!', 'status' => true]);
        }

        return response()->json(['message' => 'Connection Failed. Please check IP and Network.', 'status' => false], 500);
    }

    /**
     * Get list of users from the device.
     */
    public function getUsers()
    {
        $ip = '192.168.0.201'; // CHANGE THIS
        $port = 4370;

        $zk = new LaravelZkteco($ip, $port);

        if ($zk->connect()) {
            $zk->disableDevice();
            $users = $zk->getUser();
            $zk->enableDevice();

            return response()->json($users);
        }

        return response()->json(['message' => 'Connection Failed.'], 500);
    }

    /**
     * Get attendance logs from the device.
     */
    public function getAttendance()
    {
        $ip = '192.168.0.201'; // CHANGE THIS
        $port = 4370;

        $zk = new LaravelZkteco($ip, $port);

        if ($zk->connect()) {
            $zk->disableDevice();
            $attendance = $zk->getAttendance();
            $zk->enableDevice();

            return response()->json($attendance);
        }

        return response()->json(['message' => 'Connection Failed.'], 500);
    }

    public function getDeviceInfo()
    {
        $ip = '192.168.0.201'; 
        $port = 4370;

        $zk = new LaravelZkteco($ip, $port);

        if ($zk->connect()) {
            return response()->json([
                'version' => $zk->version(),
                'device_name' => $zk->deviceName(),
                'serial_number' => $zk->serialNumber(),
                'platform' => $zk->platform(),
                'os_version' => $zk->osVersion(),
                'work_code' => $zk->workCode(),
                'ssr' => $zk->ssr(),
                'pin_width' => $zk->pinWidth(),
                'device_time' => $zk->getTime() // Added check
            ]);
        }

        return response()->json(['message' => 'Connection Failed.'], 500);
    }
    

    public function index()
    {
        // 1. Dashboard Overview
        $ip = '192.168.0.201'; 
        $port = 4370;
        $zk = new LaravelZkteco($ip, $port);
        
        // Fetch Users Live for Count
        $users = [];
        if ($zk->connect()) {
             $zk->disableDevice();
             $users = $zk->getUser();
             $zk->enableDevice();
        }
        
        // Count Today's Logs from DB
        $todayLogsCount = Attendance::whereDate('timestamp', date('Y-m-d'))->count();

        return view('zk.dashboard', compact('users', 'todayLogsCount'));
    }

    public function users()
    {
        // 2. Users Management Page
        $ip = '192.168.0.201'; 
        $port = 4370;
        $zk = new LaravelZkteco($ip, $port);
        $users = [];
        
        if ($zk->connect()) {
             $zk->disableDevice();
             $users = $zk->getUser();
             $zk->enableDevice();
        }

        return view('zk.users', compact('users'));
    }

    public function logs(Request $request)
    {
        // 3. Logs Page with Filters
        $ip = '192.168.0.201'; 
        $port = 4370;
        
        // Fetch users from DB (Persistent)
        $users = ZkUser::all();

        $query = Attendance::query();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = $request->start_date . ' 00:00:00';
            $end = $request->end_date . ' 23:59:59';
            $query->whereBetween('timestamp', [$start, $end]);
        } elseif ($request->filter == 'yesterday') {
            $query->whereDate('timestamp', date('Y-m-d', strtotime('-1 day')));
        } elseif ($request->filter == 'today' || !$request->has('filter')) {
            $query->whereDate('timestamp', date('Y-m-d'));
        }

        $attendance = $query->with('user')->orderBy('timestamp', 'desc')->get();

        return view('zk.logs', compact('attendance', 'users'));
    }

    public function forceSync()
    {
        Artisan::call('zk:sync');
        return redirect()->back()->with('success', 'Manual Sync Completed Successfully!');
    }

    public function storeUser(Request $request)
    {
        $ip = '192.168.0.201'; 
        $port = 4370;
        $zk = new LaravelZkteco($ip, $port);

        if ($zk->connect()) {
            $zk->disableDevice();
            
            // uid (int), userid (string), name (string), password (string), role (int), cardno (int)
            // Note: UID is auto-increment internal ID usually, but library asks for it manually sometimes.
            // Using a random UID for demo, ideally find max UID + 1
            $uid = rand(1, 60000); 
            $userid = $request->userid;
            $name = $request->name;
            $role = (int) $request->role;
            $cardno = $request->cardno ?? 0;
            $password = $request->password ?? ''; // K50 supports password

            $zk->setUser($uid, $userid, $name, $password, $role, $cardno);
            $zk->enableDevice();
            
            return redirect()->route('zk.users.manager')->with('success', 'User Added to Device Successfully!');
        }
        
        return redirect()->back()->with('error', 'Device Connection Failed');
    }

    public function destroyUser($uid)
    {
        $ip = '192.168.0.201'; 
        $port = 4370;
        $zk = new LaravelZkteco($ip, $port);

        if ($zk->connect()) {
            $zk->disableDevice();
            $zk->removeUser($uid);
            $zk->enableDevice();
            return redirect()->route('zk.users.manager')->with('success', 'User Deleted from Device!');
        }

        return redirect()->back()->with('error', 'Device Connection Failed');
    }

    public function getLogsJson(Request $request)
    {
        $query = Attendance::query();

        // Filter: Date Range or "Today" default
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = $request->start_date . ' 00:00:00';
            $end = $request->end_date . ' 23:59:59';
            $query->whereBetween('timestamp', [$start, $end]);
        } elseif ($request->filter == 'yesterday') {
            $query->whereDate('timestamp', date('Y-m-d', strtotime('-1 day')));
        } elseif ($request->filter == 'today' || !$request->has('filter')) {
            $query->whereDate('timestamp', date('Y-m-d'));
        }

        $attendance = $query->with('user')->orderBy('timestamp', 'desc')->get(); // Eloquent Collection
        
        return response()->json([
            'data' => $attendance,
            'count' => $attendance->count()
        ]);
    }

    public function clearLogs()
    {
        $ip = '192.168.0.201'; 
        $port = 4370;
        $zk = new LaravelZkteco($ip, $port);

        if ($zk->connect()) {
            $zk->disableDevice();
            $zk->clearAttendance();
            $zk->enableDevice();
            return redirect()->route('zk.dashboard')->with('success', 'All Attendance Logs Cleared Successfully!');
        }

        return redirect()->back()->with('error', 'Device Connection Failed');
    }
}
