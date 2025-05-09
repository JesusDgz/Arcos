<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Reporte;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReportesController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Reporte::all()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tipo' => 'required|string|max:50',
            'datos' => 'required|array',
            'admin_id' => 'required|integer'
        ]);

        $data['datos'] = json_encode($data['datos']);

        $reporte = Reporte::create($data);

        return response()->json(['success' => true, 'data' => $reporte], 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Reporte::findOrFail($id)]);
    }

    public function destroy($id): JsonResponse
    {
        Reporte::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Reporte eliminado.']);
    }
}

