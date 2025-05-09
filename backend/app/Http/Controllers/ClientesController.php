<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Reporte;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientesController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            return response()->json(['success' => true, 'data' => Cliente::all()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'usuario_id' => 'required|integer',
                'direccion' => 'required|string'
            ]);

            $cliente = Cliente::create($data);

            return response()->json(['success' => true, 'data' => $cliente], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            return response()->json(['success' => true, 'data' => Cliente::findOrFail($id)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $cliente = Cliente::findOrFail($id);
            $data = $request->validate([
                'usuario_id' => 'required|integer',
                'direccion' => 'required|string'
            ]);
            $cliente->update($data);

            return response()->json(['success' => true, 'data' => $cliente]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            Cliente::findOrFail($id)->delete();
            return response()->json(['success' => true, 'message' => 'Cliente eliminado.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}

// Repite misma estructura para Producto, Pedido, DetallePedido, Reporte...

// Si lo deseas, continúo generando cada uno con su respectivo contenido ahora.
