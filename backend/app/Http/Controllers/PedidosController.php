<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PedidosController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Pedido::all()]);
    }

    public function store(Request $request): JsonResponse
{
    $data = $request->validate([
        'estado' => 'required|in:pendiente,en_preparacion,listo,en_camino,entregado',
        'total' => 'required|numeric',
        'cliente_id' => 'required|integer',
        'empleado_id' => 'nullable|integer',
        'cocinero_id' => 'nullable|integer',
        'repartidor_id' => 'nullable|integer',
        'mesa' => 'nullable|string|max:50',
    ]);

    $pedido = Pedido::create($data);

    return response()->json(['success' => true, 'data' => $pedido], 201);
}

    public function show($id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Pedido::findOrFail($id)]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $pedido = Pedido::findOrFail($id);
        $data = $request->validate([
            'estado' => 'required|in:pendiente,en_preparacion,listo,en_camino,entregado',
            'total' => 'required|numeric',
            'cliente_id' => 'required|integer',
            'empleado_id' => 'nullable|integer',
            'cocinero_id' => 'nullable|integer',
            'repartidor_id' => 'nullable|integer',
            'mesa' => 'nullable|string|max:50',
        ]);

        $pedido->update($data);

        return response()->json(['success' => true, 'data' => $pedido]);
    }

    public function destroy($id): JsonResponse
    {
        Pedido::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Pedido eliminado.']);
    }
}
