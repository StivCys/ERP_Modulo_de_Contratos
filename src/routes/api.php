<?php

use App\Domains\Cliente\Api\ClienteApiController;
use App\Domains\Contrato\Api\ContratoApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Auth: obter token via Sanctum
Route::post('/tokens/create', function (Request $request) {
    $request->validate([
        'email'       => 'required|email',
        'password'    => 'required',
        'device_name' => 'required|string',
    ]);

    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Credenciais inválidas.'], 401);
    }

    $token = $user->createToken($request->device_name)->plainTextToken;

    return response()->json(['token' => $token]);
});

// Revogar token atual
Route::middleware('auth:sanctum')->delete('/tokens/revoke', function (Request $request) {
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Token revogado.']);
});

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => response()->json($request->user()));

    Route::apiResource('clientes', ClienteApiController::class);
    Route::apiResource('contratos', ContratoApiController::class);
});
