<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class EstadoModel extends Model
{
    protected $table            = 'tb_estado';
    protected $primaryKey       = 'est_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = false;
    protected $allowedFields    = [
        'est_nome'
    ];
}
