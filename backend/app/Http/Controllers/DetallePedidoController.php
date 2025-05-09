<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pedido;
use Illuminate\Http\Request;
use App\Models\DetallePedido;
use Illuminate\Http\JsonResponse;

class DetallePedidoController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => DetallePedido::all()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pedido_id' => 'required|integer',
            'producto_id' => 'required|integer',
            'cantidad' => 'required|integer|min:1',
            'subtotal' => 'required|numeric|min:0'
        ]);

        $detalle = DetallePedido::create($data);

        return response()->json(['success' => true, 'data' => $detalle], 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => DetallePedido::findOrFail($id)]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $detalle = DetallePedido::findOrFail($id);
        $data = $request->validate([
            'pedido_id' => 'required|integer',
            'producto_id' => 'required|integer',
            'cantidad' => 'required|integer|min:1',
            'subtotal' => 'required|numeric|min:0'
        ]);

        $detalle->update($data);

        return response()->json(['success' => true, 'data' => $detalle]);
    }

    public function destroy($id): JsonResponse
    {
        DetallePedido::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Detalle de pedido eliminado.']);
    }
}
