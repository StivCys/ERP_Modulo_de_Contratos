<?php
namespace App\Shared\Services;

use App\Shared\Contracts\CsvReportInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CsvGeneratorService
{
    public function generate(CsvReportInterface $report, string $userFolder): string
    {
        $disk = Storage::disk('relatorios');

        if (!$disk->exists($userFolder)) {
            $disk->makeDirectory($userFolder);
        }

        $filename = Str::uuid() . '_' . $report->filename();
        $relativePath = "{$userFolder}/{$filename}";
        $absolutePath = $disk->path($relativePath);

        $handle = fopen($absolutePath, 'w');
        if (!$handle) {
            throw new \RuntimeException("Não foi possível criar o arquivo em {$absolutePath}");
        }

        try {
            fwrite($handle, "\xEF\xBB\xBF"); // BOM UTF-8
            fputcsv($handle, $report->headers());

            foreach ($report->data() as $row) {
                fputcsv($handle, is_array($row) ? $row : [$row]);
            }

            return $relativePath; // Ex: "2/550e8400..._clientes_ativos_2026.csv"

        } finally {
            fclose($handle);
        }
    }
}