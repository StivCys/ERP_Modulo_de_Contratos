<?php
// app/Domains/Cliente/Reports/RelatorioClientesAtivos.php
namespace App\Domains\Cliente\Reports;

use App\Shared\Contracts\CsvReportInterface;
use App\Domains\Cliente\Models\Cliente;
use Illuminate\Support\Carbon;

class RelatorioClientesAtivos implements CsvReportInterface
{
    public function __construct(
        public array $filters = [],
        public ?int $user_id = null
    ) {
    }

    public function headers(): array
    {
        return ['ID', 'Nome', 'Email', 'CPF/CNPJ', 'Status', 'Data Cadastro', 'Última Atualização'];
    }

    public function data(): iterable
    {
        $query = Cliente::query()
            ->select('id', 'nome', 'email', 'cpf_cnpj', 'ativo', 'created_at', 'updated_at')
            ->where('ativo', 'sim');

        // Aplica filtros
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('cpf_cnpj', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['date_start'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_start']);
        }

        if (!empty($this->filters['date_end'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_end']);
        }

        // Stream seguro para memória
        return $query->cursor()->map(fn($cliente) => [
            $cliente->id,
            $cliente->nome,
            $cliente->email,
            $cliente->cpf_cnpj,
            $cliente->ativo === 'sim' ? 'Sim' : 'Não',
            $cliente->created_at?->format('d/m/Y H:i'),
            $cliente->updated_at?->format('d/m/Y H:i'),
        ]);
    }

    public function filename(): string
    {
        return 'clientes_ativos_' . Carbon::now()->format('Y-m-d_His') . '.csv';
    }
}