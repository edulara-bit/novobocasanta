<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CategoriaModel;
use App\Models\CidadeModel;
use App\Models\ParceiroModel;
use App\Models\ProdutoModel;

class Sitemap extends BaseController
{
    public function index()
    {
        $produtoModel = new ProdutoModel();
        $categoriaModel = new CategoriaModel();
        $cidadeModel = new CidadeModel();
        $parceiroModel = new ParceiroModel();

        $cidades = $cidadeModel->getCidades();
        $categorias = $categoriaModel->getCategoriasPrincipais();
        $ofertas = $produtoModel->select('pro_id, pro_slug, cid_url, cat_url, pro_data')
            ->join('tb_cidades', 'tb_produtos.pro_cidade = tb_cidades.cid_id', 'left')
            ->join('tb_categorias', 'tb_produtos.pro_categoria = tb_categorias.cat_id', 'left')
            ->orderBy('pro_id', 'DESC')
            ->findAll(500);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Home das cidades
        foreach ($cidades as $c) {
            $xml .= '<url>';
            $xml .= '<loc>' . base_url($c['cid_url']) . '</loc>';
            $xml .= '<changefreq>daily</changefreq>';
            $xml .= '<priority>1.0</priority>';
            $xml .= '</url>';
        }

        // Categorias por cidade
        foreach ($cidades as $c) {
            foreach ($categorias as $cat) {
                $xml .= '<url>';
                $xml .= '<loc>' . base_url("{$c['cid_url']}/categoria/{$cat->cat_url}") . '</loc>';
                $xml .= '<changefreq>weekly</changefreq>';
                $xml .= '<priority>0.8</priority>';
                $xml .= '</url>';
            }
        }

        // Ofertas individuais
        foreach ($ofertas as $o) {
            $cid = $o->cid_url ?? 'piracicaba';
            $cat = $o->cat_url ?? 'ofertas';
            $slug = $o->pro_slug ?? 'oferta-' . $o->pro_id;

            $xml .= '<url>';
            $xml .= '<loc>' . base_url("{$cid}/{$cat}/{$slug}") . '</loc>';
            $xml .= '<lastmod>' . date('Y-m-d', strtotime($o->pro_data ?? 'now')) . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.9</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return $this->response
            ->setContentType('application/xml')
            ->setBody($xml);
    }
}
