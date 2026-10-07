<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class RedirecionaModel extends Model
{
    protected $table            = 'tb_redireciona';
    protected $primaryKey       = 'red_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = false;
    protected $allowedFields    = [
        'red_titulo', 'red_link_antigo', 'red_link_novo'
    ];
}
