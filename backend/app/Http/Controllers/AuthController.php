<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    private $jwt_key;

    public function __construct()
    {
        $this->jwt_key = env('JWT_SECRET', 'clave_secreta_jwt');
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'correo' => 'required|email',
            'contrasena' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $usuario = User::where('correo', $request->correo)->first();

        if (!$usuario || !Hash::check($request->contrasena, $usuario->contrasena)) {
            return response()->json(['success' => false, 'message' => 'Credenciales incorrectas'], 401);
        }

        $payload = [
            'sub' => $usuario->id,
            'nombre' => $usuario->nombre,
            'rol' => $usuario->rol,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24), // 1 día
        ];

        $token = JWT::encode($payload, $this->jwt_key, 'HS256');

        $usuario->token = $token;
        $usuario->save();

        return response()->json([
            'success' => true,
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'correo' => $usuario->correo,
                'rol' => $usuario->rol,
            ]
        ]);
    }
}
