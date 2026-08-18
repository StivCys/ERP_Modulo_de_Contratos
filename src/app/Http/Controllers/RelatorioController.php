<?php
namespace App\Http\Controllers;

use App\Models\RelatoriosGerado;
use App\Shared\Jobs\GenerateCsvReport;
use App\Domains\Cliente\Reports\RelatorioClientesAtivos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class RelatorioController extends Controller
{
    public function index(Request $request)
    {
        $relatorios = RelatoriosGerado::where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'nome' => $r->nome,
                'status' => $r->status,
                'created_at' => $r->created_at->diffForHumans(),
                'download_url' => $r->status === 'completed' ? route('cliente.relatorio.download', $r->id) : null,
                'error' => $r->error_message,
            ]);

        return Inertia::render('Relatorios/Index', compact('relatorios'));
    }
    public function gerarCsv(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
        ]);

        $relatorio = RelatoriosGerado::create([
            'user_id' => Auth::id(),
            'nome' => 'Clientes Ativos - ' . now()->format('d/m/Y H:i'),
            'report_class' => RelatorioClientesAtivos::class,
            'parameters' => [
                'filters' => $validated,
                'user_id' => Auth::id(),
            ],
            'status' => 'pending',
        ]);

        GenerateCsvReport::dispatch(
            $relatorio->id,
            RelatorioClientesAtivos::class,
            ['filters' => $validated, 'user_id' => Auth::id()]
        )->onQueue('relatorios');

        // ✅ Passa o ID para o frontend via session/props
        return back()->with([
            'success' => 'Relatório iniciado! Acompanhe o status abaixo.',
            'relatorio_id' => $relatorio->id,
        ]);
    }

    public function status(Request $request, string $id)
    {
        $relatorio = RelatoriosGerado::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json([
            'status' => $relatorio->status,
            'download_url' => $relatorio->status === 'completed'
                ? route('cliente.relatorio.download', $relatorio->id)
                : null,
            'error' => $relatorio->error_message,
        ]);
    }

    public function download(Request $request, string $id)
    {
        $relatorio = RelatoriosGerado::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        abort_if($relatorio->status !== 'completed' || !$relatorio->path, 404);

        // Agora o download é 1 linha. O Laravel cuida do stream e headers.
        return response()->download(
            Storage::disk('relatorios')->path($relatorio->path),
            $relatorio->filename,
            ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }

    public function destroyMultiple(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer',
        ]);

        $relatorios = RelatoriosGerado::where('user_id', auth()->id())
            ->whereIn('id', $validated['ids'])
            ->get();

        $disk = Storage::disk('relatorios');

        foreach ($relatorios as $r) {
            if ($r->path && $disk->exists($r->path)) {
                $disk->delete($r->path); // Funciona 100% das vezes agora
            }
        }

        $deletedCount = RelatoriosGerado::whereIn('id', $relatorios->pluck('id'))->delete();

        return back()->with('success', "{$deletedCount} relatório(s) excluído(s).");
    }
}