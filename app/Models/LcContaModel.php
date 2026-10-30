<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class LcContaModel extends Model
{
    protected $table            = 'tb_lc_contas';
    protected $primaryKey       = 'lcc_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;

    protected $allowedFields    = [
        'lcc_tipo',
        'lcc_referencia_id',
        'lcc_cpf_cnpj',
        'lcc_nome',
        'lcc_whatsapp',
        'lcc_saldo',
        'lcc_saldo_bloqueado',
        'lcc_ultimo_movimento',
        'lcc_ativo',
        'lcc_criado_em',
    ];

    /**
     * Busca conta pelo CPF/CNPJ (limpo, apenas dígitos)
     */
    public function findByDoc(string $doc): ?array
    {
        $cleanDoc = preg_replace('/\D/', '', $doc);
        return $this->where('lcc_cpf_cnpj', $cleanDoc)->first();
    }

    /**
     * Busca conta vinculada a um parceiro
     */
    public function findByParceiroId(int|string $parId): ?array
    {
        $id = (int)$parId;
        return $this->where('lcc_tipo', 'lojista')
                    ->where('lcc_referencia_id', $id)
                    ->first();
    }
}
