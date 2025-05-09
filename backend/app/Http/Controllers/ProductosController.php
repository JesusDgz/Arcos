<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductosController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Producto::all()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'categoria' => 'required|string|max:100'
        ]);

        $producto = Producto::create($data);

        return response()->json(['success' => true, 'data' => $producto], 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Producto::findOrFail($id)]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $producto = Producto::findOrFail($id);
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'categoria' => 'required|string|max:100'
        ]);

        $producto->update($data);

        return response()->json(['success' => true, 'data' => $producto]);
    }

    public function destroy($id): JsonResponse
    {
        Producto::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Producto eliminado.']);
    }
}
