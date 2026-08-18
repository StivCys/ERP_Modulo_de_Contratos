<?php
// app/Models/RelatoriosGerado.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelatoriosGerado extends Model
{
    // ✅ Campos permitidos para mass assignment
    protected $fillable = [
        'user_id',
        'nome',
        'filename',
        'report_class',
        'parameters',
        'status',
        'path',
        'error_message',
        'completed_at',
    ];

    // ✅ Casts para conversão automática de tipos
    protected $casts = [
        'parameters' => 'array',
        'completed_at' => 'datetime',
    ];

    // ✅ Relacionamento
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ✅ Attribute para URL de download
    public function getDownloadUrlAttribute(): ?string
    {
        if ($this->status !== 'completed' || !$this->path) {
            return null;
        }
        return route('cliente.relatorio.download', $this->id);
    }
}