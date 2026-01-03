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
        // IP Address of the ZKTeco device
        $ip = env('ZK_DEVICE_IP', '192.168.0.201');
        $port = env('ZK_DEVICE_PORT', 4370);

        // Don't connect here, let the frontend do it via AJAX
        $status = null; 
        $deviceInfo = null;
        
        return view('zk.connect', compact('ip', 'port', 'status', 'deviceInfo'));
    }

    /**
     * Get list of users from the device.
     */
    public function getUsers()
    {
        $ip = env('ZK_DEVICE_IP', '192.168.0.201'); 
        $port = env('ZK_DEVICE_PORT', 4370);

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
        $ip = env('ZK_DEVICE_IP', '192.168.0.201'); 
        $port = env('ZK_DEVICE_PORT', 4370);

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
        $ip = env('ZK_DEVICE_IP', '192.168.0.201'); 
        $port = env('ZK_DEVICE_PORT', 4370);

        // 1. Manual Diagnostic Check
        $fp = @fsockopen($ip, $port, $errno, $errstr, 2); // 2 second timeout
        if (!$fp) {
            $e_msg = "Connection Failed: ";
            if ($errno == 10060 || str_contains($errstr, 'timed out')) {
                $e_msg .= "Device Unreachable (Timeout). Check if device is ON and network is connected.";
            } elseif ($errno == 10061 || str_contains($errstr, 'refused')) {
                $e_msg .= "Connection Refused. Device IP is reachable but Port $port is closed/mismatch.";
            } elseif (str_contains($errstr, 'host')) {
                $e_msg .= "Host Unreachable. Check IP Address configuration.";
            } else {
                $e_msg .= "$errstr ($errno)";
            }
            return response()->json(['message' => $e_msg], 500);
        }
        fclose($fp);

        // 2. Library Connection
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
                'device_time' => $zk->getTime()
            ]);
        }

        return response()->json(['message' => 'Connected to Socket but Protocol Handshake Failed.'], 500);
    }
    

    public function index()
{
    // 1. Dashboard Overview
    $ip = env('ZK_DEVICE_IP', '192.168.0.201'); 
    $port = env('ZK_DEVICE_PORT', 4370);
    $zk = new LaravelZkteco($ip, $port);
    
    // Auto-Sync Users on Page Load (Keeping DB consistent with Machine)
    try {
        if ($zk->connect()) {
            $deviceUsers = $zk->getUser(); // Get all users from machine
            $deviceUserIds = [];

            foreach ($deviceUsers as $user) {
                $deviceUserIds[] = (string)$user['userid'];
                
                // Update or create in local DB
                ZkUser::updateOrCreate(
                    ['userid' => (string)$user['userid']],
                    [
                        'uid' => $user['uid'],
                        'name' => $user['name'],
                        'role' => $user['role'],
                        'password' => $user['password'] ?? '',
                        'cardno' => $user['cardno'] ?? 0,
                    ]
                );
            }

            // DELETE users from local DB that are NO LONGER on the device
            ZkUser::whereNotIn('userid', $deviceUserIds)->delete();
            
            \Illuminate\Support\Facades\Log::info("Dashboard: Synchronized " . count($deviceUserIds) . " users from device.");
        }
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error("Dashboard Sync Error: " . $e->getMessage());
    }
    
    // Fetch Device Users count from DB
    $usersCount = ZkUser::count();
    
    // Fetch Total Students from API (Cached for performance)
    $totalStudents = \Illuminate\Support\Facades\Cache::remember('zk_total_students', 600, function () {
        try {
            $response = \Illuminate\Support\Facades\Http::get('https://tmscedu.com/api/all/students');
            if ($response->successful()) {
                return count($response->json()['data'] ?? []);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Dashboard: Student count API failed: " . $e->getMessage());
        }
        return 0;
    });
    
    // Count Today's Logs from DB
    $todayLogsCount = Attendance::whereDate('timestamp', date('Y-m-d'))->count();
    
    // Recent 5 Logs
    $recentLogs = Attendance::with('user')->orderBy('timestamp', 'desc')->take(5)->get();

    return view('zk.dashboard', compact('usersCount', 'totalStudents', 'todayLogsCount', 'recentLogs'));
}


    public function users()
    {
        // 2. Users Management Page
        $users = ZkUser::all();
        return view('zk.users', compact('users'));
    }

    public function logs(Request $request)
    {
        // 3. Logs Page with Filters
        $ip = env('ZK_DEVICE_IP', '192.168.0.201'); 
        $port = env('ZK_DEVICE_PORT', 4370);
        
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
        $output = Artisan::output();
        
        // Simple check for success keyword or lack of error
        // The command outputs "Sync Complete!" on success.
        
        if (str_contains($output, 'Connection Failed') || str_contains($output, 'Error')) {
             return redirect()->back()->with('error', $output);
        }

        return redirect()->back()->with('success', $output);
    }

    public function storeUser(Request $request)
    {
        $ip = env('ZK_DEVICE_IP', '192.168.0.201'); 
        $port = env('ZK_DEVICE_PORT', 4370);
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

    public function updateUser(Request $request)
    {
        $ip = env('ZK_DEVICE_IP', '192.168.0.201'); 
        $port = env('ZK_DEVICE_PORT', 4370);
        $zk = new LaravelZkteco($ip, $port);

        $success = false;
        $message = 'Device Connection Failed';

        if ($zk->connect()) {
            $zk->disableDevice();
            
            $user = ZkUser::where('uid', $request->uid)->first();
            if ($user) {
                // Using the raw sdk setUser method to update the name
                // setUser(uid, userid, name, password, role, cardno)
                $zk->setUser(
                    (int) $user->uid, 
                    (string) $user->userid, 
                    (string) $request->name, 
                    (string) ($user->password ?? ''), 
                    (int) $user->role, 
                    (int) ($user->cardno ?? 0)
                );
                
                $user->update(['name' => $request->name]);
                $success = true;
                $message = 'User Name Updated Successfully!';
            } else {
                $message = 'User record not found.';
            }
            
            $zk->enableDevice();
        }

        return response()->json([
            'status' => $success ? 'success' : 'error',
            'message' => $message,
            'name' => $request->name
        ]);
    }

    public function destroyUser($uid)
    {
        $ip = env('ZK_DEVICE_IP', '192.168.0.201'); 
        $port = env('ZK_DEVICE_PORT', 4370);
        $zk = new LaravelZkteco($ip, $port);

        $success = false;
        $message = 'Device Connection Failed';

        if ($zk->connect()) {
            $zk->disableDevice();
            $zk->removeUser($uid);
            $zk->enableDevice();
            
            // Also delete from local database
            ZkUser::where('uid', $uid)->delete();
            
            $success = true;
            $message = 'User Deleted from Device and Database!';
        }

        if (request()->ajax()) {
            return response()->json([
                'status' => $success ? 'success' : 'error',
                'message' => $message
            ]);
        }

        if ($success) {
            return redirect()->route('zk.users.manager')->with('success', $message);
        }

        return redirect()->back()->with('error', $message);
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
        $ip = env('ZK_DEVICE_IP', '192.168.0.201'); 
        $port = env('ZK_DEVICE_PORT', 4370);
        $zk = new LaravelZkteco($ip, $port);

        if ($zk->connect()) {
            $zk->disableDevice();
            $zk->clearAttendance();
            $zk->enableDevice();
            return redirect()->route('zk.dashboard')->with('success', 'All Attendance Logs Cleared Successfully!');
        }

        return redirect()->back()->with('error', 'Device Connection Failed');
    }

    /**
     * Display a listing of students from external API.
     */
    public function students(Request $request)
    {
        try {
            $response = \Illuminate\Support\Facades\Http::get('https://tmscedu.com/api/all/students');
            if (!$response->successful()) {
                return view('zk.students')->with('error', 'Failed to fetch student data.');
            }
            $allData = $response->json();
            $students = $allData['data'] ?? [];
            $classes = collect($students)->pluck('StudentClass')->unique()->sort()->values()->all();
            
            return view('zk.students', compact('classes'));
        } catch (\Exception $e) {
            return view('zk.students')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function getStudentsJson()
    {
        try {
            $response = \Illuminate\Support\Facades\Http::get('https://tmscedu.com/api/all/students');
            if (!$response->successful()) {
                return response()->json(['status' => 'error', 'message' => 'API Failure'], 500);
            }

            $allData = $response->json();
            $students = $allData['data'] ?? [];
            $existingUserIds = ZkUser::pluck('userid')->toArray();

            return response()->json([
                'status' => 'success',
                'students' => $students,
                'existingUserIds' => $existingUserIds
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Sync a single student to the device and local DB.
     */
    public function syncStudent($id)
    {
        try {
            \Illuminate\Support\Facades\Log::info("ZK Sync: Individual sync requested for student ID: " . $id);
            
            $response = \Illuminate\Support\Facades\Http::get('https://tmscedu.com/api/all/students');
            if (!$response->successful()) {
                \Illuminate\Support\Facades\Log::error("ZK Sync: API request failed (Individual). Status: " . $response->status());
                return redirect()->back()->with('error', 'API Failure');
            }

            $students = $response->json()['data'] ?? [];
            $student = collect($students)->firstWhere('id', $id);
            
            if (!$student) {
                \Illuminate\Support\Facades\Log::error("ZK Sync: Student ID " . $id . " not found in API response.");
                return redirect()->back()->with('error', 'Student not found in API');
            }

            $displayName = ($student['StudentNameEn'] ?? $student['StudentName']) . ' (' . $student['StudentID'] . ')';
            \Illuminate\Support\Facades\Log::info("ZK Sync: Prepared name: " . $displayName);

            $ip = env('ZK_DEVICE_IP', '192.168.0.201'); 
            $port = env('ZK_DEVICE_PORT', 4370);
            $zk = new LaravelZkteco($ip, $port);

            if ($zk->connect()) {
                \Illuminate\Support\Facades\Log::info("ZK Sync: Connected to device " . $ip);
                $zk->disableDevice();
                
                // Use a random UID to avoid conflicts, but keep student ID as userid (Badge No)
                $uid = rand(1000, 65000); 
                $userid = (string)$student['id'];
                
                \Illuminate\Support\Facades\Log::info("ZK Sync: Attempting setUser with UID: {$uid}, UserID: {$userid}, Name: {$displayName}");
                
                // uid (int), userid (string), name, password, role, cardno
                $result = $zk->setUser($uid, $userid, $displayName, '', 0, 0);
                \Illuminate\Support\Facades\Log::info("ZK Sync: Device setUser result: " . ($result ? 'Success' : 'Failed'));
                $zk->enableDevice();

                // Sync to local ZkUser DB
                ZkUser::updateOrCreate(
                    ['userid' => $userid],
                    [
                        'uid' => $uid,
                        'name' => $displayName,
                        'role' => 0,
                        'cardno' => 0,
                    ]
                );

                if (request()->ajax()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => "Student {$displayName} created successfully!",
                        'userid' => $userid
                    ]);
                }

                return redirect()->back()->with('success', "Student {$displayName} created as user successfully (Badge ID: {$userid})!");
            }

            if (request()->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Device Connection Failed'], 500);
            }

            \Illuminate\Support\Facades\Log::error("ZK Sync: Device connection failed for IP: " . $ip);
            return redirect()->back()->with('error', 'Device Connection Failed');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("ZK Sync: Exception in syncStudent: " . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', "Error: " . $e->getMessage());
        }
    }

    /**
     * Sync an entire class to the device and local DB.
     */
    public function syncClass(Request $request)
    {
        $class = $request->class;
        if (!$class) return redirect()->back()->with('error', 'No class selected');

        try {
            \Illuminate\Support\Facades\Log::info("ZK Sync: Bulk sync requested for class: " . $class);
            
            $response = \Illuminate\Support\Facades\Http::get('https://tmscedu.com/api/all/students');
            if (!$response->successful()) {
                \Illuminate\Support\Facades\Log::error("ZK Sync: API request failed (Bulk). Status: " . $response->status());
                return redirect()->back()->with('error', 'API Failure');
            }

            $allStudents = $response->json()['data'] ?? [];
            $classStudents = collect($allStudents)->where('StudentClass', $class);

            if ($classStudents->isEmpty()) {
                \Illuminate\Support\Facades\Log::warn("ZK Sync: No students found for class: " . $class);
                return redirect()->back()->with('error', 'No students found in this class');
            }

            $ip = env('ZK_DEVICE_IP', '192.168.0.201'); 
            $port = env('ZK_DEVICE_PORT', 4370);
            $zk = new LaravelZkteco($ip, $port);

            if ($zk->connect()) {
                \Illuminate\Support\Facades\Log::info("ZK Sync: Connected to device " . $ip . " for bulk sync.");
                $zk->disableDevice();
                $count = 0;
                $errors = 0;
                foreach ($classStudents as $student) {
                    $displayName = ($student['StudentNameEn'] ?? $student['StudentName']) . ' (' . $student['StudentID'] . ')';
                    
                    // Use a random UID to avoid conflicts, but keep student ID as userid (Badge No)
                    $uid = rand(1000, 65000); 
                    $userid = (string)$student['id'];
                    
                    $result = $zk->setUser($uid, $userid, $displayName, '', 0, 0);
                    
                    if ($result) {
                        ZkUser::updateOrCreate(
                            ['userid' => $userid],
                            [
                                'uid' => $uid,
                                'name' => $displayName,
                                'role' => 0,
                                'cardno' => 0,
                            ]
                        );
                        $count++;
                    } else {
                        \Illuminate\Support\Facades\Log::error("ZK Sync: Failed to set user for Student ID: " . $student['id'] . " Name: " . $displayName);
                        $errors++;
                    }
                }
                $zk->enableDevice();

                if ($errors > 0) {
                    return redirect()->back()->with('warning', "Sync completed with issues. {$count} success, {$errors} failures.");
                }
                return redirect()->back()->with('success', "Bulk Sync Complete! {$count} students from class '{$class}' added to device.");
            }

            \Illuminate\Support\Facades\Log::error("ZK Sync: Device connection failed (Bulk) for IP: " . $ip);
            return redirect()->back()->with('error', 'Device Connection Failed');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("ZK Sync: Exception in syncClass: " . $e->getMessage());
            return redirect()->back()->with('error', "Error: " . $e->getMessage());
        }
    }

    /**
     * Check if a user has fingerprint enrolled on the device.
     */
    public function checkFingerprint($uid)
    {
        $ip = env('ZK_DEVICE_IP', '192.168.0.201'); 
        $port = env('ZK_DEVICE_PORT', 4370);
        $zk = new LaravelZkteco($ip, $port);

        if ($zk->connect()) {
            $zk->disableDevice();
            // getFingerprint returns an array of fingerprint data if found, or empty array if not.
            $fingerprints = $zk->getFingerprint($uid);
            $zk->enableDevice();

            if (!empty($fingerprints)) {
                $status = 'added';
                $message = 'Fingerprint Added';
            } else {
                $status = 'not_added';
                $message = 'Not Added';
            }

            // Update local database
            ZkUser::where('uid', $uid)->update(['fingerprint_status' => $status]);

            return response()->json(['status' => $status, 'message' => $message]);
        }

        return response()->json(['status' => 'error', 'message' => 'Device Connection Failed'], 500);
    }
}


