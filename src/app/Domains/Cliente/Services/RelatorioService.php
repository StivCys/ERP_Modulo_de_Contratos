<?php
// App/Domains/Cliente/Services/RelatorioService.php
namespace App\Domains\Cliente\Services;

use App\Models\RelatoriosGerado;
use App\Shared\Jobs\GenerateCsvReport;
use App\Domains\Cliente\Reports\RelatorioClientesAtivos;

class RelatorioService
{
    public function gerarClientesAtivos(int $userId, int $mes, int $ano, ?string $vendedorId = null): RelatoriosGerado
    {
        // 1. Cria registro de tracking
        $relatorio = RelatoriosGerado::create([
            'user_id' => $userId,
            'nome' => "Clientes Ativos - {$mes}/{$ano}",
            'report_class' => RelatorioClientesAtivos::class,
            'parameters' => ['mes' => $mes, 'ano' => $ano, 'vendedorId' => $vendedorId],
            'status' => 'pending',
        ]);

        // 2. Dispatch do job com ID do registro
        GenerateCsvReport::dispatch(
            $relatorio->id,
            RelatorioClientesAtivos::class,
            ['mes' => $mes, 'ano' => $ano, 'vendedorId' => $vendedorId]
        )->onQueue('relatorios')
            ->delay(now()->addSeconds(5)); // opcional: dá tempo do usuário ver "em processamento"

        return $relatorio;
    }
}