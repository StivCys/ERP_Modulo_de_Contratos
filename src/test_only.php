<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Domains\Contrato\Models\Contrato;
$contrato = Contrato::first();
var_dump(get_class($contrato));
$only = $contrato->only(['cliente_id', 'data_inicio', 'data_fim', 'status']);
if (is_object($only)) {
    var_dump(get_class($only));
} else {
    var_dump(gettype($only));
}
