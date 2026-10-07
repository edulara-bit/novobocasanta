<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ConteudoModel extends Model
{
    protected $table            = 'tb_conteudo';
    protected $primaryKey       = 'con_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = false;
    protected $allowedFields    = [
        'con_titulo', 'con_keywords', 'con_description', 'con_conteudo',
        'con_galeria', 'con_ordem', 'con_tipopagina', 'con_lista', 'con_banner', 'con_slug'
    ];
}
