<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CategoriaModel;
use App\Models\CidadeModel;
use App\Models\ClienteModel;
use App\Models\LancamentoModel;
use App\Services\SeoService;
use Config\Database;

class Fidelidade extends BaseController
{
    protected CidadeModel $cidadeModel;
    protected CategoriaModel $categoriaModel;
    protected ClienteModel $clienteModel;
    protected LancamentoModel $lancamentoModel;
    protected SeoService $seoService;

    public function __construct()
    {
        $this->cidadeModel = new CidadeModel();
        $this->categoriaModel = new CategoriaModel();
        $this->clienteModel = new ClienteModel();
        $this->lancamentoModel = new LancamentoModel();
        $this->seoService = new SeoService();
    }

    public function index()
    {
        $cidade = $this->cidadeModel->first() ?? ['cid_id' => 1, 'cid_nome' => 'Piracicaba', 'cid_url' => 'piracicaba'];
        $todasCidades = $this->cidadeModel->getCidades();
        $principaisCidades = $this->cidadeModel->getPrincipaisCidades(4);
        $categoriasPrincipais = $this->categoriaModel->getCategoriasPrincipais();

        $rawCpf = (string)($this->request->getGet('cpf') ?? $this->request->getPost('cpf') ?? '');
        $cpfLimpo = preg_replace('/\D/', '', $rawCpf);

        $cliente = null;
        $lancamentos = [];
        $saldoPontos = 0;
        $totalCompras = 0.0;
        $totalCashback = 0.0;
        $resumoPorParceiro = [];
        $buscou = false;

        if (!empty($cpfLimpo)) {
            $buscou = true;
            $cliente = $this->clienteModel->where('cli_cpf', $cpfLimpo)->first();

            $db = Database::connect();
            $query = $db->table('tb_lancamentos as l')
                ->select('l.*, p.par_id, p.par_nome, p.par_foto, c.car_nome, c.car_percentagem, c.car_pontos')
                ->join('tb_parceiros as p', 'p.par_id = l.lan_parceiro', 'left')
                ->join('tb_cartoes as c', 'c.car_id = l.lan_cartao', 'left')
                ->where('l.lan_cpf', $cpfLimpo)
                ->orderBy('l.lan_data', 'DESC')
                ->get();

            $lancamentos = $query->getResultArray();

            foreach ($lancamentos as $lan) {
                $valor = (float)($lan['lan_valor'] ?? 0);
                $percent = (float)($lan['car_percentagem'] ?? 0);
                $cashbackCalculado = $percent > 0 ? ($valor * ($percent / 100)) : 0.0;

                $totalCompras += $valor;
                $totalCashback += $cashbackCalculado;
                $saldoPontos++;

                $parcId = (int)($lan['lan_parceiro'] ?? 0);
                $parcNome = $lan['par_nome'] ?? 'Parceiro Geral';

                if (!isset($resumoPorParceiro[$parcId])) {
                    $resumoPorParceiro[$parcId] = [
                        'nome'         => $parcNome,
                        'pontos'       => 0,
                        'total'        => 0.0,
                        'cashback'     => 0.0,
                        'campanha'     => $lan['car_nome'] ?? 'Fidelidade',
                    ];
                }
                $resumoPorParceiro[$parcId]['pontos']++;
                $resumoPorParceiro[$parcId]['total'] += $valor;
                $resumoPorParceiro[$parcId]['cashback'] += $cashbackCalculado;
            }
        }

        $seo = $this->seoService->generateMeta([
            'title'       => 'Clube Fidelidade & Cashback - Boca Santa Ofertas',
            'description' => 'Consulte seus pontos, carimbos e saldo de cashback acumulados em compras nas lojas e parceiros credenciados do Boca Santa.',
        ]);

        return view('fidelidade/index', [
            'cidadeAtual'          => $cidade,
            'todasCidades'         => $todasCidades,
            'principaisCidades'    => $principaisCidades,
            'categoriasBar'        => array_slice($categoriasPrincipais, 0, 8),
            'seo'                  => $seo,
            'cpfBusca'             => $rawCpf,
            'cpfLimpo'             => $cpfLimpo,
            'cliente'              => $cliente,
            'lancamentos'          => $lancamentos,
            'saldoPontos'          => $saldoPontos,
            'totalCompras'         => $totalCompras,
            'totalCashback'        => $totalCashback,
            'resumoPorParceiro'    => $resumoPorParceiro,
            'buscou'               => $buscou,
        ]);
    }

    public function consultar()
    {
        $cpf = preg_replace('/\D/', '', (string)$this->request->getPost('cpf'));
        if (empty($cpf)) {
            return redirect()->to(base_url('fidelidade'))->with('error', 'Por favor, digite seu CPF.');
        }

        return redirect()->to(base_url('fidelidade?cpf=' . $cpf));
    }
}
