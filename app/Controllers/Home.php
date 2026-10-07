<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\BannerModel;
use App\Models\CategoriaModel;
use App\Models\CidadeModel;
use App\Models\ProdutoModel;
use App\Services\SeoService;

class Home extends BaseController
{
    protected ProdutoModel $produtoModel;
    protected CategoriaModel $categoriaModel;
    protected CidadeModel $cidadeModel;
    protected BannerModel $bannerModel;
    protected SeoService $seoService;

    public function __construct()
    {
        $this->produtoModel = new ProdutoModel();
        $this->categoriaModel = new CategoriaModel();
        $this->cidadeModel = new CidadeModel();
        $this->bannerModel = new BannerModel();
        $this->seoService = new SeoService();
    }

    public function index(string $cidadeSlug = 'piracicaba')
    {
        $cidade = $this->cidadeModel->getBySlug($cidadeSlug) ?? $this->cidadeModel->first();
        if (!$cidade) {
            $cidade = ['cid_id' => 1, 'cid_nome' => 'Piracicaba', 'cid_url' => 'piracicaba'];
        }

        $cidadeId = (int)$cidade['cid_id'];

        // Ofertas em Destaque
        $ofertasDestaque = $this->produtoModel->getOfertas([
            'cidade_id' => $cidadeId,
            'destaque'  => 1,
        ], 8);

        // Produtos integrados do Linefast
        $produtosLinefast = $this->produtoModel->getOfertas([
            'cidade_id'        => $cidadeId,
            'somente_linefast' => true,
        ], 4);

        if (empty($produtosLinefast)) {
            $produtosLinefast = $this->produtoModel->getOfertas([
                'cidade_id'        => $cidadeId,
                'somente_desconto' => true,
            ], 4);
        }

        // Mais ofertas recentes
        $ultimasOfertas = $this->produtoModel->getOfertas([
            'cidade_id' => $cidadeId,
            'ordem'     => 'mais_recentes',
        ], 8);

        $categoriasPrincipais = $this->categoriaModel->getCategoriasPrincipais();
        $todasCidades = $this->cidadeModel->getCidades();
        $principaisCidades = $this->cidadeModel->getPrincipaisCidades(4);
        $banners = $this->bannerModel->getBannersAtivos();

        $seo = $this->seoService->generateMeta([
            'title'       => "Ofertas e Descontos em {$cidade['cid_nome']} - SP",
            'description' => "Confira as melhores ofertas, promoções exclusivas e cupons de desconto em {$cidade['cid_nome']} no Boca Santa Ofertas.",
        ]);

        return view('home/index', [
            'cidadeAtual'          => $cidade,
            'todasCidades'         => $todasCidades,
            'principaisCidades'    => $principaisCidades,
            'categoriasBar'        => $categoriasPrincipais,
            'categoriasPrincipais' => $categoriasPrincipais,
            'ofertasDestaque'      => $ofertasDestaque,
            'produtosLinefast'     => $produtosLinefast,
            'ultimasOfertas'       => $ultimasOfertas,
            'banners'              => $banners,
            'seo'                  => $seo,
        ]);
    }
}
