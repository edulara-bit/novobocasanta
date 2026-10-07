<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class AdministradorModel extends Model
{
    protected $table            = 'tb_administradores';
    protected $primaryKey       = 'adm_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = false;
    protected $allowedFields    = [
        'adm_login', 'adm_senha', 'adm_nome', 'adm_email', 'adm_nivel_acesso', 'adm_status'
    ];
}
