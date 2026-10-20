<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CategoriaModel;

class Categorias extends BaseController
{
    /**
     * Retorna subcategorias de uma categoria pai em JSON
     */
    public function subcategorias(int $parentId)
    {
        $categoriaModel = new CategoriaModel();
        $subcategorias = $categoriaModel->getSubcategorias($parentId);

        $resultado = [];
        foreach ($subcategorias as $sub) {
            $resultado[] = [
                'cat_id'            => (int)$sub->cat_id,
                'cat_titulo'        => (string)$sub->cat_titulo,
                'cat_url'           => (string)$sub->cat_url,
                'cat_categoria_pai' => (int)$sub->cat_categoria_pai,
            ];
        }

        return $this->response->setJSON($resultado);
    }
}
