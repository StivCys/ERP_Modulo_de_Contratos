<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Domains\Contrato\Models\Contrato;
use App\Domains\Contrato\Actions\UpdateContrato;
use App\Domains\Servico\Services\ServicoService;
use App\Domains\Contrato\Services\ContratoHistoricoService;

$servicoService = new ServicoService();
$historicoService = new ContratoHistoricoService();

$action = new UpdateContrato($servicoService, $historicoService);

// We simulate a request with some malicious or weird data
// We will mock Contrato::findOrFail maybe? Or just create one.

try {
    // $action->execute(["id" => 1, ...]);
} catch (\Throwable $e) {
    echo $e->getMessage(), "\n";
    echo $e->getFile(), ":", $e->getLine(), "\n";
}
