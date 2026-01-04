<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('zk/dashboard');
});

Route::get('/zk/connect', [App\Http\Controllers\ZktecoController::class, 'connect'])->name('zk.connect');
Route::get('/zk/users', [App\Http\Controllers\ZktecoController::class, 'getUsers']);
Route::get('/zk/attendance', [App\Http\Controllers\ZktecoController::class, 'getAttendance']);
Route::get('/zk/info', [App\Http\Controllers\ZktecoController::class, 'getDeviceInfo'])->name('zk.info');
Route::get('/zk/dashboard', [App\Http\Controllers\ZktecoController::class, 'index'])->name('zk.dashboard');
Route::get('/zk/users-manager', [App\Http\Controllers\ZktecoController::class, 'users'])->name('zk.users.manager');
Route::get('/zk/attendance-logs', [App\Http\Controllers\ZktecoController::class, 'logs'])->name('zk.logs');
Route::post('/zk/user/store', [App\Http\Controllers\ZktecoController::class, 'storeUser'])->name('zk.user.store');
Route::post('/zk/user/update', [App\Http\Controllers\ZktecoController::class, 'updateUser'])->name('zk.user.update');
Route::get('/zk/user/delete/{uid}', [App\Http\Controllers\ZktecoController::class, 'destroyUser'])->name('zk.user.delete'); // Using GET for simplicity in demo, normally DELETE
Route::get('/zk/clear-logs', [App\Http\Controllers\ZktecoController::class, 'clearLogs'])->name('zk.logs.clear');
Route::get('/zk/sync', [App\Http\Controllers\ZktecoController::class, 'forceSync'])->name('zk.force.sync');
Route::get('/zk/api/logs', [App\Http\Controllers\ZktecoController::class, 'getLogsJson'])->name('zk.api.logs');
Route::get('/zk/students', [App\Http\Controllers\ZktecoController::class, 'students'])->name('zk.students');
Route::get('/zk/students/sync/{id}', [App\Http\Controllers\ZktecoController::class, 'syncStudent'])->name('zk.students.sync');
Route::post('/zk/students/sync-class', [App\Http\Controllers\ZktecoController::class, 'syncClass'])->name('zk.students.sync.class');
Route::get('/zk/staffs', [App\Http\Controllers\ZktecoController::class, 'staffs'])->name('zk.staffs');
Route::get('/zk/staffs/sync/{id}', [App\Http\Controllers\ZktecoController::class, 'syncStaff'])->name('zk.staffs.sync');
Route::get('/zk/user/fingerprint/{uid}', [App\Http\Controllers\ZktecoController::class, 'checkFingerprint'])->name('zk.user.fingerprint');
Route::get('/zk/api/students', [App\Http\Controllers\ZktecoController::class, 'getStudentsJson'])->name('zk.api.students');
Route::get('/zk/api/staffs', [App\Http\Controllers\ZktecoController::class, 'getStaffsJson'])->name('zk.api.staffs');



Route::post('checkwebhook',function (Request $request){
    $attendancedata  = $request->all();
    \Illuminate\Support\Facades\Log::info($attendancedata);
});