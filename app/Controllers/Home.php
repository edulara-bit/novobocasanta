<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\BannerModel;
use App\Models\CategoriaModel;
use App\Models\CidadeModel;
use App\Models\ParceiroModel;
use App\Models\ProdutoModel;
use App\Services\SeoService;

class Home extends BaseController
{
    protected ProdutoModel $produtoModel;
    protected CategoriaModel $categoriaModel;
    protected CidadeModel $cidadeModel;
    protected ParceiroModel $parceiroModel;
    protected BannerModel $bannerModel;
    protected SeoService $seoService;

    public function __construct()
    {
        $this->produtoModel = new ProdutoModel();
        $this->categoriaModel = new CategoriaModel();
        $this->cidadeModel = new CidadeModel();
        $this->parceiroModel = new ParceiroModel();
        $this->bannerModel = new BannerModel();
        $this->seoService = new SeoService();
    }

    public function index(string $cidadeSlug = 'piracicaba')
    {
        // Se acessado diretamente via domínio linecash.com.br, direciona para o portal do LineCash
        $host = (string)($this->request->getServer('HTTP_HOST') ?? '');
        if (str_contains($host, 'linecash')) {
            return redirect()->to(base_url('linecash'));
        }

        $cidade = $this->cidadeModel->getBySlug($cidadeSlug);

        // Compatibilidade com links antigos de 1 segmento indexados no Google (ex: /internet-via-radio-digital-piracicaba, /casadecarnesrubia)
        if (!$cidade && $cidadeSlug !== 'piracicaba') {
            $slugLimpo = trim(str_replace('.html', '', $cidadeSlug));

            // 1. Verifica se é uma oferta ativa (pelo pro_slug, pro_slug2 ou tb_slug)
            $oferta = $this->produtoModel->getDetalhesOferta($slugLimpo);
            if ($oferta) {
                return redirect()->to($oferta->getUrl(), 301);
            }

            // 2. Verifica se é rota legada de fidelidade (ex: /fidelidade-parceiro)
            if (str_starts_with($slugLimpo, 'fidelidade-')) {
                return redirect()->to(base_url('linecash'), 301);
            }

            // 3. Verifica se é apelido de parceiro (ex: /casadecarnesrubia)
            $parceiro = $this->parceiroModel->select('tb_parceiros.*, tb_cidades.cid_url')
                ->join('tb_cidades', 'tb_parceiros.par_cidade = tb_cidades.cid_id', 'left')
                ->where('tb_parceiros.par_apelido', $slugLimpo)
                ->first();
            if ($parceiro) {
                return redirect()->to($parceiro->getUrl($parceiro->cid_url ?? 'piracicaba'), 301);
            }

            // 4. Verifica se é slug de categoria (ex: /acougue)
            $categoria = $this->categoriaModel->where('cat_url', $slugLimpo)->first();
            if ($categoria) {
                return redirect()->to(base_url('piracicaba/categoria/' . $categoria->cat_url), 301);
            }

            // 5. Verifica se era uma oferta antiga na tb_slug já removida -> redireciona para a categoria correspondente
            $slugHist = $this->produtoModel->getSlugHistorico($slugLimpo);
            if ($slugHist && !empty($slugHist['slu_categoria'])) {
                $cidHist = !empty($slugHist['slu_cidade']) ? $slugHist['slu_cidade'] : 'piracicaba';
                return redirect()->to(base_url("{$cidHist}/categoria/{$slugHist['slu_categoria']}"), 301);
            }
        }

        if (!$cidade) {
            $cidade = $this->cidadeModel->first() ?? ['cid_id' => 1, 'cid_nome' => 'Piracicaba', 'cid_url' => 'piracicaba'];
        }

        $cidadeId = (int)$cidade['cid_id'];

        // Ofertas do Dia / Em Destaque (Ordem Randômica Real entre as ofertas ativas da cidade)
        $ofertasDestaque = $this->produtoModel->getOfertas([
            'cidade_id' => $cidadeId,
            'ordem'     => 'random',
        ], 8);

        // Produtos integrados do Linefast (excluindo parceiros de testes)
        $produtosLinefast = $this->produtoModel->getOfertas([
            'cidade_id'        => $cidadeId,
            'somente_linefast' => true,
            'ordem'            => 'mais_recentes',
        ], 4);

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
