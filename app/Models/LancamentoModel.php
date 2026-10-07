<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class LancamentoModel extends Model
{
    protected $table            = 'tb_lancamentos';
    protected $primaryKey       = 'lan_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = false;
    protected $allowedFields    = [
        'lan_cpf', 'lan_data', 'lan_cartao', 'lan_parceiro',
        'lan_valor', 'lan_nf', 'lan_oferta', 'lan_descricao', 'lan_bocapoint'
    ];
}
