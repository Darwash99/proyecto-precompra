<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuentas;
use Illuminate\Support\Facades\Validator;

class CuentaController extends Controller
{
    public function index()
    {
        $cuentas = Cuentas::all();

        if($cuentas->isEmpty()){
            $mensaje = ['status' => 'success', 'message' => 'no hay cuentas registradas'];
        }else{
            $mensaje = ['status' => 'success', 'data' => $cuentas];
        }
        return response()->json($mensaje, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:cuentas,email',
            'telefono' => 'required|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'error en la validacion de datos','errors' => $validator->errors()], 400);
        }

        $cuenta = Cuentas::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'telefono' => $request->telefono,
        ]);

        if(!$cuenta){
            return response()->json(['status' => 'error', 'message' => 'error al crear la cuenta','errors' => $validator->errors()], 500);
        }


        return response()->json(['status' => 'success', 'message' => 'Cuenta creada','data' => $cuenta], 200);
    }

    public function show($id)
    {
        $cuenta = Cuentas::find($id);

        if (!$cuenta) {
            return response()->json(['status' => 'error', 'message' => 'Cuenta no encontrada'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $cuenta], 200);
    }

    public function update(Request $request, $id)
    {
        $cuenta = Cuentas::find($id);
        
        if (!$cuenta) {
            return response()->json(['status' => 'error', 'message' => 'Cuenta no encontrada'], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:cuentas,email',
            'telefono' => 'sometimes|required|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors'=> $validator->errors(), 'message' => 'error en validacion de campos'], 400);
        }

        $cuentaedit = $cuenta->update($request->all());
        if(!$cuentaedit){
            return response()->json(['status' => 'error', 'message' => 'error al actualizar la cuenta'], 400);
        }
        return response()->json(['status' => 'success', 'message' => 'Cuenta Actualizada','data' => $cuenta], 200);
    }

    public function destroy($id){
        $cuenta = Cuentas::find($id);
        if (!$cuenta) {
            return response()->json(['status' => 'error', 'message' => 'Cuenta no encontrada'], 404);
        }

        $cuenta->delete();

        return response()->json(['status' => 'success', 'message' => 'Cuenta Elminada'], 200);
    }

}
