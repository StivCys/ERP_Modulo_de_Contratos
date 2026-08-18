<?php

use App\Domains\Cliente\Controllers\ClienteController;
use App\Domains\Servico\Controllers\ServicoController;
use App\Domains\Contrato\Controllers\ContratoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\RelatorioController;
use App\Domains\User\Controllers\UserController;
use App\Domains\Role\Controllers\RoleController;
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $months = collect(range(11, 0))->map(function ($i) {
        return now()->subMonths($i)->format('Y-m');
    });

    $clientesPorMes = \App\Domains\Cliente\Models\Cliente::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mes, count(*) as total')
        ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
        ->groupBy('mes')
        ->pluck('total', 'mes');

    $contratosPorMes = \App\Domains\Contrato\Models\Contrato::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mes, count(*) as total')
        ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
        ->groupBy('mes')
        ->get()
        ->keyBy('mes');

    $labels = $months->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->translatedFormat('M/Y'))->values();

    $data = [
        'labels' => $labels,
        'clientes' => $months->map(fn($m) => $clientesPorMes[$m] ?? 0)->values(),
        'contratos_total' => $months->map(fn($m) => $contratosPorMes[$m]['total'] ?? 0)->values(),
        'stats' => [
            'clientes_ativos' => \App\Domains\Cliente\Models\Cliente::where('ativo', 'sim')->count(),
            'contratos_ativos' => \App\Domains\Contrato\Models\Contrato::where('status', 'ativo')->count(),
            'valor_total_contratos' => \App\Domains\Contrato\Models\Contrato::where('status', 'ativo')->get()->sum('valor_total'),
        ]
    ];

    return Inertia::render('Dashboard', ['chartData' => $data]);
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
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
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

// Novas rotas para relatório CSV
Route::post('/cliente/relatorio/csv', [RelatorioController::class, 'gerarCsv'])
    ->name('cliente.relatorio.csv')
    ->middleware('auth');

Route::get('/cliente/relatorio/{id}/status', [RelatorioController::class, 'status'])
    ->name('cliente.relatorio.status')
    ->middleware('auth');

Route::get('/cliente/relatorio/{id}/download', [RelatorioController::class, 'download'])
    ->name('cliente.relatorio.download')
    ->middleware('auth');

Route::get('/meus-relatorios', [RelatorioController::class, 'index'])
    ->name('relatorios.index')
    ->middleware('auth');

Route::delete('/relatorios', [RelatorioController::class, 'destroyMultiple'])
    ->name('relatorios.destroy-multiple')
    ->middleware('auth');

// Route::resource('customers', CustomerController::class);
require __DIR__ . '/auth.php';
