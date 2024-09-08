<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedidos;
use App\Models\Cuentas;
use Illuminate\Support\Facades\Validator;

class PedidosController extends Controller
{
    public function index()
    {
        $pedidos = Pedidos::all();

        if($pedidos->isEmpty()){
            $mensaje = ['status' => 'success', 'message' => 'no hay pedidos registrados'];
        }else{
            $mensaje = ['status' => 'success', 'data' => $pedidos];
        }
        return response()->json($mensaje, 200);
    }

    public function store(Request $request)
    {
        // Validar los datos del pedido
        $validator = Validator::make($request->all(), [
            'idCuenta' => 'exists:cuentas,id', // Verifica que el idCuenta exista en la tabla de cuentas
            'producto' => 'required|string|max:255',
            'cantidad' => 'required|integer|min:1',
            'valor' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'error en la validacion de datos','errors' => $validator->errors()], 400);
        }

        // Obtener la información de la cuenta para el pedido
        $cuenta = Cuentas::find($request->idCuenta);
        if (!$cuenta) {
            return response()->json(['status' => 'error', 'message' => 'Cuenta no encontrada'. $request->idCuenta], 404);
        }

        // Crear el pedido
        $pedido = Pedidos::create([
            'idCuenta' => $cuenta->id, 
            'producto' => $request->producto,
            'cantidad' => $request->cantidad,
            'valor' => $request->valor,
            'total' => $request->total,
            'estado' => 'CREADO',
        ]);

        if(!$pedido){
            return response()->json(['status' => 'error', 'message' => 'error al crear EL pedido','errors' => $validator->errors()], 500);
        }

        //datos para enviar a websocket
        $data = [
            'cuenta' => $cuenta, 
            'pedido' => $pedido   
        ];

        return response()->json(['status' => 'success', 'message' => 'Pedido creado ', 'data' => $data], 200);
    }

    public function show($id)
    {
        $pedidos = Pedidos::find($id);

        if (!$pedidos) {
            return response()->json(['status' => 'error', 'message' => 'Pedido no encontrado'], 404);
        }
        $cuenta = Cuentas::find($pedidos->idCuenta);

        $data = [
            'cuenta' => $cuenta,
            'Pedido' => $pedidos
        ];
        return response()->json(['status' => 'success', 'data' => $data], 200);
    }

    public function update(Request $request, $id)
    {
        $pedido = Pedidos::find($id);

        if (!$pedido) {
            return response()->json(['status' => 'error', 'message' => 'Pedido no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'idCuenta' => 'exists:cuentas,id',
            'producto' => 'sometimes|required|string|max:255',
            'cantidad' => 'sometimes|required|integer|min:1',
            'valor' => 'sometimes|required|numeric|min:0',
            'total' => 'sometimes|required|numeric|min:0',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'error en la validacion de datos','errors' => $validator->errors()], 400);
        }

        $cuenta = Cuentas::find($request->idCuenta);
        if (!$cuenta) {
            return response()->json(['status' => 'error', 'message' => 'Cuenta no encontrada'. $request->idCuenta], 404);
        }
        
        $pedidoedit = $pedido->update($request->all());
        if(!$pedidoedit){
            return response()->json(['status' => 'error', 'message' => 'error al actualizar el pedido'], 400);
        }

        return response()->json(['status' => 'success', 'message' => 'Pedido Actualizado','data' => $pedido], 200);
    }

    public function cancelar_pedido($id)
    {
        $pedido = Pedidos::find($id);

        if (!$pedido) {
            return response()->json(['status' => 'error', 'message' => 'Pedido no encontrado'], 404);
        }

        if ($pedido->estado == 'CANCELADO') {
            return response()->json(['status' => 'success', 'message' => 'El pedido ya fue cancelado'], 200);
        }

        $pedidoedit = $pedido->update([
            'estado' => 'CANCELADO'
        ]);

        if(!$pedidoedit){
            return response()->json(['status' => 'error', 'message' => 'error al actualizar el  ERSTADO del Pedido'], 400);
        }

        $cuenta = Cuentas::find($pedido->idCuenta);

        $data = [
            'cuenta' => $cuenta,
            'Pedido' => $pedido
        ];
        return response()->json(['status' => 'success', 'message' => 'El pedido fue cancelado','data' => $data], 200);
    }

    public function destroy($id){
        $pedidos = Pedidos::find($id);
        if (!$pedidos) {
            return response()->json(['status' => 'error', 'message' => 'Pedido no encontrado'], 404);
        }

        $pedidos->delete();

        return response()->json(['status' => 'success', 'message' => 'Pedido Elminado'], 200);
    }
}
