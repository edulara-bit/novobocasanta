<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\Categoria;
use CodeIgniter\Model;

class CategoriaModel extends Model
{
    protected $table            = 'tb_categorias';
    protected $primaryKey       = 'cat_id';
    protected $useAutoIncrement = true;
    protected $returnType       = Categoria::class;
    protected $protectFields    = false;
    protected $allowedFields    = [
        'cat_titulo', 'cat_categoria_pai', 'cat_classe', 'cat_filho', 'cat_neto', 'cat_url'
    ];

    /**
     * Retorna todas as categorias principais (pais) válidas
     */
    public function getCategoriasPrincipais(): array
    {
        return $this->where('cat_categoria_pai', 0)
            ->where('cat_titulo IS NOT NULL')
            ->where('cat_titulo !=', '')
            ->where('cat_url IS NOT NULL')
            ->where('cat_url !=', '')
            ->orderBy('cat_titulo', 'ASC')
            ->findAll();
    }

    /**
     * Retorna subcategorias de uma categoria pai
     */
    public function getSubcategorias(int $parentId): array
    {
        return $this->where('cat_categoria_pai', $parentId)
            ->where('cat_titulo IS NOT NULL')
            ->where('cat_titulo !=', '')
            ->where('cat_url IS NOT NULL')
            ->where('cat_url !=', '')
            ->orderBy('cat_titulo', 'ASC')
            ->findAll();
    }
}
