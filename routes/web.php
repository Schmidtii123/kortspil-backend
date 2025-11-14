<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
        $tables = DB::select('SHOW TABLES');
        return response()->json([
            'status' => 'success',
            'message' => 'Database connected',
            'host' => config('database.connections.mysql.host'),
            'database' => config('database.connections.mysql.database'),
            'tables_count' => count($tables),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'host' => config('database.connections.mysql.host'),
        ], 500);
    }
});
