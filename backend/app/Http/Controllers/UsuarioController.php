<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UsuarioController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => User::all()
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'nombre' => 'required|string|max:100',
                'correo' => 'required|email|unique:usuarios',
                'contrasena' => 'required|string',
                'rol' => 'required|in:cliente,empleado,cocinero,repartidor,admin'
            ]);

            $data['contrasena'] = bcrypt($data['contrasena']);

            $usuario = User::create($data);

            return response()->json(['success' => true, 'data' => $usuario], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            return response()->json(['success' => true, 'data' => User::findOrFail($id)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $usuario = User::findOrFail($id);

            $data = $request->validate([
                'nombre' => 'required|string|max:100',
                'correo' => 'required|email|unique:usuarios,correo,' . $id,
                'contrasena' => 'nullable|string',
                'rol' => 'required|in:cliente,empleado,cocinero,repartidor,admin'
            ]);

            if (!empty($data['contrasena'])) {
                $data['contrasena'] = bcrypt($data['contrasena']);
            } else {
                unset($data['contrasena']);
            }

            $usuario->update($data);

            return response()->json(['success' => true, 'data' => $usuario]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            User::findOrFail($id)->delete();
            return response()->json(['success' => true, 'message' => 'Usuario eliminado.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
