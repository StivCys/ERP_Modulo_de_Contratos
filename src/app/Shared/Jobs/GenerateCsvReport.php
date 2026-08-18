<?php
// app/Shared/Jobs/GenerateCsvReport.php
namespace App\Shared\Jobs;

use App\Models\RelatoriosGerado;
use App\Shared\Contracts\CsvReportInterface;
use App\Shared\Services\CsvGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateCsvReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; // 2 minutos por segurança

    public function __construct(
        public string $relatorioId, // UUID do registro na tabela
        public string $reportClass,
        public array $parameters = []
    ) {
    }

    public function handle(CsvGeneratorService $generator): void
    {
        $relatorio = RelatoriosGerado::findOrFail($this->relatorioId);

        try {
            $relatorio->update(['status' => 'processing']);

            $report = app()->make($this->reportClass, $this->parameters);

            if (!$report instanceof CsvReportInterface) {
                throw new \InvalidArgumentException(
                    "Classe {$this->reportClass} deve implementar CsvReportInterface"
                );
            }

            // Gera o arquivo via service (facilita testes e reuso)
            // Dentro do handle()
            $relativePath = $generator->generate(
                report: $report,
                userFolder: (string) $relatorio->user_id // 👈 Agora só o ID
            );

            $relatorio->update([
                'status' => 'completed',
                'path' => $relativePath,
                'filename' => basename($relativePath),
                'completed_at' => now(),
            ]);

            Log::info("Relatório gerado com sucesso", [
                'relatorio_id' => $relatorio->id,
                'path' => $relativePath
            ]);

        } catch (\Throwable $e) {
            $relatorio->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error("Falha ao gerar relatório", [
                'relatorio_id' => $relatorio->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // para o Laravel lidar com retry/fail conforme config
        }
    }

    public function failed(\Throwable $e): void
    {
        // Hook opcional para notificação, métricas, etc.
        Log::critical("Job falhou definitivamente", [
            'job' => self::class,
            'relatorio_id' => $this->relatorioId ?? 'unknown',
            'error' => $e->getMessage(),
        ]);
    }
}