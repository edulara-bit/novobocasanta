<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table            = 'tb_clientes';
    protected $primaryKey       = 'cli_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = false;
    protected $allowedFields    = [
        'cli_cpf', 'cli_nome', 'cli_cep', 'cli_email', 'cli_celular',
        'cli_sexo', 'cli_nascimento', 'cli_profissao', 'cli_empresa',
        'cli_parceiro', 'cli_data', 'cli_senha'
    ];
}
