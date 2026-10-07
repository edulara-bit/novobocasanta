<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CategoriaModel;
use App\Models\CidadeModel;
use App\Models\ParceiroModel;
use App\Models\ProdutoModel;
use App\Services\SeoService;
use CodeIgniter\Exceptions\PageNotFoundException;

class Ofertas extends BaseController
{
    protected ProdutoModel $produtoModel;
    protected ParceiroModel $parceiroModel;
    protected CategoriaModel $categoriaModel;
    protected CidadeModel $cidadeModel;
    protected SeoService $seoService;

    public function __construct()
    {
        $this->produtoModel = new ProdutoModel();
        $this->parceiroModel = new ParceiroModel();
        $this->categoriaModel = new CategoriaModel();
        $this->cidadeModel = new CidadeModel();
        $this->seoService = new SeoService();
    }

    /**
     * Exibe a página de detalhes de uma oferta única
     */
    public function detalhes(string $cidadeSlug, string $categoriaSlug, string|int $slugOrId)
    {
        $cidade = $this->cidadeModel->getBySlug($cidadeSlug) ?? $this->cidadeModel->first();
        $oferta = $this->produtoModel->getDetalhesOferta($slugOrId);

        if (!$oferta) {
            throw PageNotFoundException::forPageNotFound("Oferta não encontrada.");
        }

        $parceiro = $this->parceiroModel->find($oferta->pro_parceiro);
        $relacionadas = $this->produtoModel->getRelacionadas($oferta, 4);
        $categoriasPrincipais = $this->categoriaModel->getCategoriasPrincipais();
        $todasCidades = $this->cidadeModel->getCidades();
        $principaisCidades = $this->cidadeModel->getPrincipaisCidades(4);

        // Busca Cartão Fidelidade vinculado à oferta ou parceiro
        $cartaoModel = new \App\Models\CartaoModel();
        $cartaoFidelidade = null;
        if (!empty($oferta->pro_cartao)) {
            $cartaoFidelidade = $cartaoModel->find($oferta->pro_cartao);
        }
        if (!$cartaoFidelidade && $parceiro) {
            $cartaoFidelidade = $cartaoModel->where('car_cliente', $parceiro->par_id)
                ->groupStart()
                    ->where('car_oferta', 0)
                    ->orWhere('car_oferta', (int)$oferta->pro_id)
                ->groupEnd()
                ->first();
        }

        // Schema.org JSON-LD estruturado
        $schemaJson = $this->seoService->generateProductSchema($oferta, $parceiro);

        $seo = $this->seoService->generateMeta([
            'title'       => $oferta->pro_titulo . ' em ' . ($cidade['cid_nome'] ?? 'Piracicaba'),
            'description' => substr(strip_tags((string)$oferta->pro_descricao), 0, 160),
            'image'       => $oferta->getImagemUrl(),
            'schema_json' => $schemaJson,
        ]);

        return view('ofertas/detalhes', [
            'oferta'            => $oferta,
            'parceiro'          => $parceiro,
            'cartaoFidelidade'  => $cartaoFidelidade,
            'relacionadas'      => $relacionadas,
            'cidadeAtual'       => $cidade,
            'todasCidades'      => $todasCidades,
            'principaisCidades' => $principaisCidades,
            'categoriasBar'     => $categoriasPrincipais,
            'seo'               => $seo,
        ]);
    }

    /**
     * Rota de compatibilidade direta por ID do CI3: /oferta/mostra/{id}
     */
    public function mostra(int $id)
    {
        $oferta = $this->produtoModel->getDetalhesOferta($id);
        if (!$oferta) {
            throw PageNotFoundException::forPageNotFound("Oferta não encontrada.");
        }

        $cidadeSlug = $oferta->cid_url ?? 'piracicaba';
        $categoriaSlug = $oferta->cat_url ?? 'ofertas';
        $slug = $oferta->pro_slug ?? 'oferta-' . $oferta->pro_id;

        return redirect()->to(base_url("{$cidadeSlug}/{$categoriaSlug}/{$slug}"), 301);
    }

    /**
     * Listagem por categoria
     */
    public function categoria(string $cidadeSlug, string $categoriaSlug)
    {
        $cidade = $this->cidadeModel->getBySlug($cidadeSlug) ?? $this->cidadeModel->first();
        $categoria = $this->categoriaModel->where('cat_url', $categoriaSlug)->first();

        if (!$categoria) {
            throw PageNotFoundException::forPageNotFound("Categoria não encontrada.");
        }

        $ordem = $this->request->getGet('ordem') ?? 'relevancia';

        $ofertas = $this->produtoModel->getOfertas([
            'cidade_id'    => (int)$cidade['cid_id'],
            'categoria_id' => (int)$categoria->cat_id,
            'ordem'        => $ordem,
        ], 24);

        $categoriasPrincipais = $this->categoriaModel->getCategoriasPrincipais();
        $todasCidades = $this->cidadeModel->getCidades();
        $principaisCidades = $this->cidadeModel->getPrincipaisCidades(4);

        $seo = $this->seoService->generateMeta([
            'title'       => "{$categoria->cat_titulo} em {$cidade['cid_nome']} - Melhores Ofertas",
            'description' => "Confira ofertas e descontos especiais na categoria {$categoria->cat_titulo} em {$cidade['cid_nome']} no Boca Santa Ofertas.",
        ]);

        return view('ofertas/categoria', [
            'ofertas'           => $ofertas,
            'categoria'         => $categoria,
            'cidadeAtual'       => $cidade,
            'todasCidades'      => $todasCidades,
            'principaisCidades' => $principaisCidades,
            'categoriasBar'     => array_slice($categoriasPrincipais, 0, 8),
            'categoriaAtiva'    => $categoriaSlug,
            'ordemAtual'        => $ordem,
            'seo'               => $seo,
        ]);
    }

    /**
     * Busca geral de ofertas
     */
    public function busca(string $cidadeSlug)
    {
        $cidade = $this->cidadeModel->getBySlug($cidadeSlug) ?? $this->cidadeModel->first();
        $termo = $this->request->getGet('q') ?? '';
        $destaque = !empty($this->request->getGet('destaque'));

        $ofertas = $this->produtoModel->getOfertas([
            'cidade_id' => (int)$cidade['cid_id'],
            'busca'     => $termo,
            'destaque'  => $destaque ? 1 : null,
        ], 32);

        $categoriasPrincipais = $this->categoriaModel->getCategoriasPrincipais();
        $todasCidades = $this->cidadeModel->getCidades();
        $principaisCidades = $this->cidadeModel->getPrincipaisCidades(4);

        $seo = $this->seoService->generateMeta([
            'title' => "Busca por '{$termo}' em {$cidade['cid_nome']} | Boca Santa",
        ]);

        return view('ofertas/categoria', [
            'ofertas'           => $ofertas,
            'categoria'         => (object)['cat_titulo' => $termo ? "Busca: {$termo}" : "Todas as Ofertas"],
            'cidadeAtual'       => $cidade,
            'todasCidades'      => $todasCidades,
            'principaisCidades' => $principaisCidades,
            'categoriasBar'     => array_slice($categoriasPrincipais, 0, 8),
            'categoriaAtiva'    => '',
            'buscaQuery'        => $termo,
            'seo'               => $seo,
        ]);
    }
}
