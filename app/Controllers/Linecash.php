<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LcContaModel;
use App\Models\LcTransacaoModel;
use App\Models\ParceiroModel;
use App\Models\ProdutoModel;
use App\Services\LinecashService;

class Linecash extends BaseController
{
    protected LinecashService $linecashService;
    protected LcContaModel $contaModel;
    protected LcTransacaoModel $transacaoModel;
    protected ParceiroModel $parceiroModel;
    protected ProdutoModel $produtoModel;

    public function __construct()
    {
        $this->linecashService = new LinecashService();
        $this->contaModel = new LcContaModel();
        $this->transacaoModel = new LcTransacaoModel();
        $this->parceiroModel = new ParceiroModel();
        $this->produtoModel = new ProdutoModel();
    }

    /**
     * Portal do Cliente LineCash
     */
    public function index()
    {
        $clienteCpf = session()->get('linecash_cliente_cpf');
        $conta = null;
        $extrato = [];

        if (!empty($clienteCpf)) {
            $conta = $this->contaModel->findByDoc($clienteCpf);
            if ($conta) {
                $extrato = $this->transacaoModel->getExtratoByConta((int)$conta['lcc_id'], 20);
            }
        }

        // Lojas parceiras ativas
        $lojas = $this->parceiroModel
            ->where('par_ativo', 'ativo')
            ->orderBy('par_nome', 'ASC')
            ->findAll(12);

        // Ofertas em destaque para gastar ou ganhar cashback
        $ofertas = $this->produtoModel->getOfertas([], 8);

        return view('linecash/index', [
            'title'     => 'LineCash - A Moeda de Cashback & Fidelidade | Boca Santa',
            'conta'     => $conta,
            'extrato'   => $extrato,
            'lojas'     => $lojas,
            'ofertas'   => $ofertas,
        ]);
    }

    /**
     * Consulta de saldo por CPF
     */
    public function consultar()
    {
        $cpf = (string)$this->request->getPost('cpf');
        $cleanCpf = $this->linecashService->cleanDoc($cpf);

        if (strlen($cleanCpf) < 11) {
            return redirect()->to(base_url('linecash'))->with('error', 'Por favor, informe um CPF válido.');
        }

        $conta = $this->contaModel->findByDoc($cleanCpf);

        if (!$conta) {
            return redirect()->to(base_url('linecash'))
                ->with('info', "Nenhum saldo encontrado para o CPF informado ({$cpf}). Ao fazer sua primeira compra em qualquer loja participante, seus LineCash serão creditados automaticamente!");
        }

        session()->set('linecash_cliente_cpf', $cleanCpf);
        session()->set('linecash_cliente_id', $conta['lcc_id']);
        session()->set('linecash_cliente_nome', $conta['lcc_nome']);

        return redirect()->to(base_url('linecash'))->with('success', 'Carteira consultada com sucesso!');
    }

    /**
     * Encerra a consulta de saldo
     */
    public function sair()
    {
        session()->remove(['linecash_cliente_cpf', 'linecash_cliente_id', 'linecash_cliente_nome']);
        return redirect()->to(base_url('linecash'))->with('info', 'Consulta encerrada.');
    }

    /**
     * Catálogo de Lojas Participantes
     */
    public function lojas()
    {
        $lojas = $this->parceiroModel
            ->where('par_ativo', 'ativo')
            ->orderBy('par_nome', 'ASC')
            ->findAll(50);

        return view('linecash/lojas', [
            'title' => 'Lojas Participantes LineCash | Boca Santa',
            'lojas' => $lojas,
        ]);
    }
}
