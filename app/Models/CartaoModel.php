<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class CartaoModel extends Model
{
    protected $table            = 'tb_cartoes';
    protected $primaryKey       = 'car_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = false;
    protected $allowedFields    = [
        'car_cliente', 'car_data', 'car_percentagem', 'car_validade',
        'car_regras', 'car_nome', 'car_pontos', 'car_minimo', 'car_point', 'car_oferta'
    ];
}
