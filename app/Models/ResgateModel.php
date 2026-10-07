<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ResgateModel extends Model
{
    protected $table            = 'tb_resgate';
    protected $primaryKey       = 'res_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = false;
    protected $allowedFields    = [
        'res_codigo', 'res_usuario', 'res_cartao', 'res_premio',
        'res_parceiro', 'res_pontos', 'res_data', 'res_retirado',
        'res_retirado_data', 'res_bocapoint'
    ];
}
