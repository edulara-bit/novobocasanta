<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class LcConfigLojistaModel extends Model
{
    protected $table            = 'tb_lc_config_lojista';
    protected $primaryKey       = 'lcf_lojista_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;

    protected $allowedFields    = [
        'lcf_lojista_id',
        'lcf_percentual_padrao',
        'lcf_produto_destaque_titulo',
        'lcf_produto_destaque_url',
        'lcf_mensagem_customizada',
        'lcf_atualizado_em',
    ];
}
