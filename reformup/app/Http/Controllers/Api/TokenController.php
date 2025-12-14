<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function generar(Request $request)
    {
        $user = $request->user();

        // Limpio tokens antiguos de este tipo (opcional pero recomendable)
        $user->tokens()->where('name', 'ReformUp-APK')->delete();

        $token = $user->createToken('ReformUp-APK', ['profesional'])->plainTextToken;

        return back()->with('api_token', $token);
    }

    public function revocar(Request $request)
    {
        $user = $request->user();

        $user->tokens()->where('name', 'ReformUp-APK')->delete();

        return back()->with('success', 'Token revocado.');
    }
}
