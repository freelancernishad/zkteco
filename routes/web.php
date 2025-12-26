<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/zk/connect', [App\Http\Controllers\ZktecoController::class, 'connect']);
Route::get('/zk/users', [App\Http\Controllers\ZktecoController::class, 'getUsers']);
Route::get('/zk/attendance', [App\Http\Controllers\ZktecoController::class, 'getAttendance']);
Route::get('/zk/info', [App\Http\Controllers\ZktecoController::class, 'getDeviceInfo']);
Route::get('/zk/dashboard', [App\Http\Controllers\ZktecoController::class, 'index'])->name('zk.dashboard');
Route::get('/zk/users-manager', [App\Http\Controllers\ZktecoController::class, 'users'])->name('zk.users.manager');
Route::get('/zk/attendance-logs', [App\Http\Controllers\ZktecoController::class, 'logs'])->name('zk.logs');
Route::post('/zk/user/store', [App\Http\Controllers\ZktecoController::class, 'storeUser'])->name('zk.user.store');
Route::get('/zk/user/delete/{uid}', [App\Http\Controllers\ZktecoController::class, 'destroyUser'])->name('zk.user.delete'); // Using GET for simplicity in demo, normally DELETE
Route::get('/zk/clear-logs', [App\Http\Controllers\ZktecoController::class, 'clearLogs'])->name('zk.logs.clear');
Route::get('/zk/sync', [App\Http\Controllers\ZktecoController::class, 'forceSync'])->name('zk.force.sync');
Route::get('/zk/api/logs', [App\Http\Controllers\ZktecoController::class, 'getLogsJson'])->name('zk.api.logs');
