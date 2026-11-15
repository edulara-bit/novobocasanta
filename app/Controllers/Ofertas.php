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
        if (is_string($slugOrId)) {
            $slugOrId = trim(str_replace('.html', '', $slugOrId));
        }

        $cidade = $this->cidadeModel->getBySlug($cidadeSlug) ?? $this->cidadeModel->first();
        $oferta = $this->produtoModel->getDetalhesOferta($slugOrId);

        if (!$oferta) {
            // 1. Verifica se era uma subcategoria legada no formato /{cidade}/{cat_pai}/{subcategoria}
            $subcat = $this->categoriaModel->where('cat_url', (string)$slugOrId)->first();
            if ($subcat) {
                return $this->categoria($cidadeSlug, (string)$slugOrId);
            }

            // 2. Verifica se consta no histórico tb_slug (oferta antiga excluída) e redireciona para a categoria
            $slugHist = $this->produtoModel->getSlugHistorico((string)$slugOrId);
            if ($slugHist && !empty($slugHist['slu_categoria'])) {
                $cidHist = !empty($slugHist['slu_cidade']) ? $slugHist['slu_cidade'] : ($cidade['cid_url'] ?? 'piracicaba');
                return redirect()->to(base_url("{$cidHist}/categoria/{$slugHist['slu_categoria']}"), 301);
            }

            // 3. Se a categoria na URL existe, redireciona para a listagem da categoria em vez de 404 seco
            $catUrlValida = $this->categoriaModel->where('cat_url', $categoriaSlug)->first();
            if ($catUrlValida) {
                return redirect()->to(base_url(($cidade['cid_url'] ?? 'piracicaba') . '/categoria/' . $catUrlValida->cat_url), 301);
            }

            throw PageNotFoundException::forPageNotFound("Oferta não encontrada.");
        }

        $parceiro = $this->parceiroModel->find($oferta->pro_parceiro);
        $fotos = $this->produtoModel->getFotosOferta((int)$oferta->pro_id, (int)$oferta->pro_parceiro);
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

        // Categoria Pai (se a oferta estiver vinculada a uma subcategoria)
        $categoriaPai = null;
        if (!empty($oferta->pro_categoria_pai) && (int)$oferta->pro_categoria_pai !== (int)$oferta->pro_categoria) {
            $categoriaPai = $this->categoriaModel->find((int)$oferta->pro_categoria_pai);
        }

        return view('ofertas/detalhes', [
            'oferta'            => $oferta,
            'parceiro'          => $parceiro,
            'fotos'             => $fotos,
            'categoriaPai'      => $categoriaPai,
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
     * Rota de compatibilidade direta por ID ou Slug do CI3: /oferta/mostra/{id} ou /{cidade}/{cat}/{parceiro}/{id}/{slug}.html
     */
    public function mostra(int|string $id)
    {
        if (is_string($id)) {
            $id = trim(str_replace('.html', '', $id));
        }

        $oferta = $this->produtoModel->getDetalhesOferta($id);
        if (!$oferta) {
            $slugHist = $this->produtoModel->getSlugHistorico((string)$id);
            if ($slugHist && !empty($slugHist['slu_categoria'])) {
                $cidHist = !empty($slugHist['slu_cidade']) ? $slugHist['slu_cidade'] : 'piracicaba';
                return redirect()->to(base_url("{$cidHist}/categoria/{$slugHist['slu_categoria']}"), 301);
            }
            throw PageNotFoundException::forPageNotFound("Oferta não encontrada.");
        }

        $cidadeSlug = $oferta->cid_url ?? 'piracicaba';
        $categoriaSlug = $oferta->cat_url ?? 'ofertas';
        $slug = $oferta->pro_slug ?? 'oferta-' . $oferta->pro_id;

        return redirect()->to(base_url("{$cidadeSlug}/{$categoriaSlug}/{$slug}"), 301);
    }

    /**
     * Resolve rotas legadas de 2 segmentos: /{cidade}/{oferta_slug}, /{cidade}/{categoria_slug} ou /{cidade}/{parceiro_slug}
     */
    public function resolverSegmentoDuplo(string $seg1, string $seg2)
    {
        $seg2Limpo = trim(str_replace('.html', '', $seg2));

        if ($seg1 === 'categoria') {
            return redirect()->to(base_url("piracicaba/categoria/{$seg2Limpo}"), 301);
        }
        if ($seg1 === 'parceiro' || $seg1 === 'parceiros') {
            return redirect()->to(base_url("piracicaba/parceiro/{$seg2Limpo}"), 301);
        }

        // 1. Verifica se $seg2 é uma categoria (ex: /piracicaba/acougue)
        $categoria = $this->categoriaModel->where('cat_url', $seg2Limpo)->first();
        if ($categoria) {
            return $this->categoria($seg1, $seg2Limpo);
        }

        // 2. Verifica se $seg2 é uma oferta ativa (ex: /piracicaba/panceta-pururuca-...)
        $oferta = $this->produtoModel->getDetalhesOferta($seg2Limpo);
        if ($oferta) {
            return redirect()->to($oferta->getUrl($seg1), 301);
        }

        // 3. Verifica se $seg2 é um parceiro (ex: /piracicaba/casadecarnesrubia)
        $parceiro = $this->parceiroModel->where('par_apelido', $seg2Limpo)->first();
        if ($parceiro) {
            return redirect()->to(base_url("{$seg1}/parceiro/{$seg2Limpo}"), 301);
        }

        // 4. Verifica se era uma oferta antiga na tb_slug
        $slugHist = $this->produtoModel->getSlugHistorico($seg2Limpo);
        if ($slugHist && !empty($slugHist['slu_categoria'])) {
            $cidHist = !empty($slugHist['slu_cidade']) ? $slugHist['slu_cidade'] : $seg1;
            return redirect()->to(base_url("{$cidHist}/categoria/{$slugHist['slu_categoria']}"), 301);
        }

        throw PageNotFoundException::forPageNotFound("Página não encontrada.");
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
            'ordem'     => $destaque ? 'random' : ($this->request->getGet('ordem') ?? 'relevancia'),
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
