<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CategoriaModel;
use App\Models\CidadeModel;
use App\Models\ParceiroModel;
use App\Models\ProdutoModel;
use App\Services\SeoService;
use CodeIgniter\Exceptions\PageNotFoundException;

class Parceiros extends BaseController
{
    protected ParceiroModel $parceiroModel;
    protected ProdutoModel $produtoModel;
    protected CidadeModel $cidadeModel;
    protected CategoriaModel $categoriaModel;
    protected SeoService $seoService;

    public function __construct()
    {
        $this->parceiroModel = new ParceiroModel();
        $this->produtoModel = new ProdutoModel();
        $this->cidadeModel = new CidadeModel();
        $this->categoriaModel = new CategoriaModel();
        $this->seoService = new SeoService();
    }

    public function index(string $cidadeSlug = 'piracicaba')
    {
        $cidade = $this->cidadeModel->getBySlug($cidadeSlug) ?? $this->cidadeModel->first();
        $parceiros = $this->parceiroModel->getParceiros(['cidade_id' => (int)$cidade['cid_id']], 30);
        $todasCidades = $this->cidadeModel->getCidades();
        $principaisCidades = $this->cidadeModel->getPrincipaisCidades(4);
        $categoriasPrincipais = $this->categoriaModel->getCategoriasPrincipais();

        $seo = $this->seoService->generateMeta([
            'title' => "Empresas e Parceiros em {$cidade['cid_nome']} - SP",
            'description' => "Guia de estabelecimentos, lojas e prestadores de serviços parceiros do Boca Santa Ofertas em {$cidade['cid_nome']}.",
        ]);

        return view('parceiros/index', [
            'parceiros'         => $parceiros,
            'cidadeAtual'       => $cidade,
            'todasCidades'      => $todasCidades,
            'principaisCidades' => $principaisCidades,
            'categoriasBar'     => array_slice($categoriasPrincipais, 0, 8),
            'seo'               => $seo,
        ]);
    }

    public function detalhes(string $cidadeSlug, string|int $slugOrId)
    {
        $cidade = $this->cidadeModel->getBySlug($cidadeSlug) ?? $this->cidadeModel->first();
        
        // Trata prefixo "parceiro-123" ou ID direto
        $cleanParam = (string)$slugOrId;
        $parceiro = null;

        if (preg_match('/^parceiro-(\d+)$/', $cleanParam, $matches)) {
            $parceiro = $this->parceiroModel->find((int)$matches[1]);
        } elseif (is_numeric($cleanParam)) {
            $parceiro = $this->parceiroModel->find((int)$cleanParam);
        } else {
            $parceiro = $this->parceiroModel->where('par_apelido', $cleanParam)->first();
            if (!$parceiro) {
                $parceiro = $this->parceiroModel->like('par_nome', str_replace('-', ' ', $cleanParam))->first();
            }
        }

        if (!$parceiro) {
            throw PageNotFoundException::forPageNotFound("Parceiro não encontrado.");
        }

        $ofertas = $this->produtoModel->getOfertas([
            'parceiro_id' => $parceiro->par_id,
        ], 30);

        $todasCidades = $this->cidadeModel->getCidades();
        $principaisCidades = $this->cidadeModel->getPrincipaisCidades(4);
        $categoriasPrincipais = $this->categoriaModel->getCategoriasPrincipais();

        $seo = $this->seoService->generateMeta([
            'title'       => "{$parceiro->getNome()} em {$cidade['cid_nome']} - Ofertas e Informações",
            'description' => "Conheça {$parceiro->getNome()}, veja o endereço, telefone, ofertas e produtos disponíveis no Boca Santa Ofertas.",
            'image'       => $parceiro->getLogoUrl(),
        ]);

        return view('parceiros/detalhes', [
            'parceiro'          => $parceiro,
            'ofertas'           => $ofertas,
            'cidadeAtual'       => $cidade,
            'todasCidades'      => $todasCidades,
            'principaisCidades' => $principaisCidades,
            'categoriasBar'     => array_slice($categoriasPrincipais, 0, 8),
            'seo'               => $seo,
        ]);
    }
}
