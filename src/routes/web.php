<?php

use App\Domains\Cliente\Controllers\ClienteController;
use App\Domains\Servico\Controllers\ServicoController;
use App\Domains\Contrato\Controllers\ContratoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Domain routes
    Route::resource('cliente', ClienteController::class);
    Route::resource('servico', ServicoController::class);
    Route::resource('contrato', ContratoController::class);
    Route::resource('regra-adicional', \App\Domains\Contrato\Controllers\RegraAdicionalController::class);
});

Route::get('/clientes/search', function (\Illuminate\Http\Request $request) {
    return \App\Domains\Cliente\Models\Cliente::query()
        ->when($request->search, function ($q, $search) {
            $q->where('nome', 'like', "%{$search}%")
                ->orWhere('cpf_cnpj', 'like', "%{$search}%");
        })
        ->limit(10)
        ->get();
})->middleware('auth');

// Route::resource('customers', CustomerController::class);
require __DIR__ . '/auth.php';
