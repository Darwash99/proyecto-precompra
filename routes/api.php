<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\PedidosController;


/*Route::get('/cuentas', function () {
    return 'listar cuentas';
});*/
Route::get('/cuentas', [CuentaController::class, 'index']);
Route::post('/cuenta', [CuentaController::class, 'store']);
Route::get('/cuentas/{id}',  [CuentaController::class, 'show']);
Route::put('/cuenta/{id}', [CuentaController::class, 'update']);
Route::delete('/cuenta/{id}', [CuentaController::class, 'destroy']);

Route::get('/pedidos', [PedidosController::class, 'index']);
Route::post('/pedidos', [PedidosController::class, 'store']);
Route::get('/pedidos/{id}',  [PedidosController::class, 'show']);
Route::put('/pedidos/{id}', [PedidosController::class, 'update']);
Route::get('/pedidos/cancelar/{id}', [PedidosController::class, 'cancelar_pedido']);
Route::delete('/pedidos/{id}', [PedidosController::class, 'destroy']);
