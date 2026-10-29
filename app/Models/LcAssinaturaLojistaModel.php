<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class LcAssinaturaLojistaModel extends Model
{
    protected $table            = 'tb_lc_assinaturas_lojistas';
    protected $primaryKey       = 'lca_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;

    protected $allowedFields    = [
        'lca_lojista_id',
        'lca_status',
        'lca_mp_preapproval_id',
        'lca_valor_mensal',
        'lca_vencimento_dia',
        'lca_ultimo_pagamento',
        'lca_criado_em',
    ];
}
