<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\DetallePedidoController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\UsuarioController;


// Rutas API tipo resource
Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('clientes', ClientesController::class);
Route::apiResource('productos', ProductosController::class);
Route::apiResource('pedidos', PedidosController::class);
Route::apiResource('detalle-pedidos', DetallePedidoController::class);
Route::apiResource('reportes', ReportesController::class);
Route::get('/hola', function () {
    return response()->json([
        'message' => 'Bienvenido a la API de Laravel',
        'status' => 'success'
    ]);
});



Route::post('/login', [AuthController::class, 'login']);
