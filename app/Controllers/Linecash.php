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

    /**
     * Tela pública de cadastro do cliente no LineCash (com link de indicação do lojista)
     */
    public function cadastro()
    {
        $lojaParam = trim((string)($this->request->getGet('loja') ?? $this->request->getGet('lojista') ?? ''));
        $parceiroIndicador = null;

        if (!empty($lojaParam)) {
            if (is_numeric($lojaParam)) {
                $parceiroIndicador = $this->parceiroModel->find((int)$lojaParam);
            } else {
                $parceiroIndicador = $this->parceiroModel
                    ->groupStart()
                        ->where('par_slug', $lojaParam)
                        ->orWhere('par_apelido', $lojaParam)
                    ->groupEnd()
                    ->where('par_ativo', 'ativo')
                    ->first();
            }
        }

        return view('linecash/cadastro', [
            'title'             => 'Cadastre-se no LineCash | Cashback no Comércio Local',
            'parceiroIndicador' => $parceiroIndicador,
        ]);
    }

    /**
     * Processa o cadastro de um novo cliente no LineCash
     */
    public function cadastrarCliente()
    {
        $nome = trim((string)$this->request->getPost('nome'));
        $cpf = (string)$this->request->getPost('cpf');
        $whatsapp = trim((string)$this->request->getPost('whatsapp'));
        $email = trim((string)$this->request->getPost('email'));
        $parceiroId = (int)$this->request->getPost('parceiro_id');

        // Novos campos de endereço
        $cep = trim((string)$this->request->getPost('cep'));
        $endereco = trim((string)$this->request->getPost('endereco'));
        $numero = trim((string)$this->request->getPost('numero'));
        $complemento = trim((string)$this->request->getPost('complemento'));
        $bairro = trim((string)$this->request->getPost('bairro'));
        $cidade = trim((string)$this->request->getPost('cidade'));
        $estado = strtoupper(trim((string)$this->request->getPost('estado')));

        $cleanCpf = $this->linecashService->cleanDoc($cpf);
        $cleanWhats = preg_replace('/\D/', '', $whatsapp);

        if (mb_strlen($nome) < 3) {
            return redirect()->back()->withInput()->with('error', 'Por favor, informe seu nome completo.');
        }

        if (strlen($cleanCpf) !== 11) {
            return redirect()->back()->withInput()->with('error', 'Por favor, informe um CPF válido com 11 dígitos.');
        }

        if (strlen($cleanWhats) < 10) {
            return redirect()->back()->withInput()->with('error', 'Por favor, informe um WhatsApp válido com DDD.');
        }

        try {
            // Cria ou recupera conta no LineCash
            $conta = $this->linecashService->getOrCreateContaCliente($cleanCpf, $nome, $whatsapp);

            // Garante registro em tb_clientes com endereço completo
            $clienteModel = new \App\Models\ClienteModel();
            $cliente = $clienteModel->where('cli_cpf', $cleanCpf)->first();

            $dadosCliente = [
                'cli_cpf'         => $cleanCpf,
                'cli_nome'        => $nome,
                'cli_celular'     => $whatsapp,
                'cli_email'       => $email ?: null,
                'cli_cep'         => $cep ?: null,
                'cli_endereco'    => $endereco ?: null,
                'cli_numero'      => $numero ?: null,
                'cli_complemento' => $complemento ?: null,
                'cli_bairro'      => $bairro ?: null,
                'cli_cidade'      => $cidade ?: null,
                'cli_estado'      => $estado ?: null,
                'cli_parceiro'    => $parceiroId > 0 ? $parceiroId : null,
            ];

            if (!$cliente) {
                $dadosCliente['cli_data'] = date('Y-m-d H:i:s');
                $clienteModel->insert($dadosCliente);
            } else {
                $upd = [];
                if (empty($cliente['cli_nome'])) $upd['cli_nome'] = $nome;
                if (empty($cliente['cli_celular'])) $upd['cli_celular'] = $whatsapp;
                if (empty($cliente['cli_email']) && !empty($email)) $upd['cli_email'] = $email;
                if (empty($cliente['cli_parceiro']) && $parceiroId > 0) $upd['cli_parceiro'] = $parceiroId;
                if (!empty($cep)) $upd['cli_cep'] = $cep;
                if (!empty($endereco)) $upd['cli_endereco'] = $endereco;
                if (!empty($numero)) $upd['cli_numero'] = $numero;
                if (!empty($complemento)) $upd['cli_complemento'] = $complemento;
                if (!empty($bairro)) $upd['cli_bairro'] = $bairro;
                if (!empty($cidade)) $upd['cli_cidade'] = $cidade;
                if (!empty($estado)) $upd['cli_estado'] = $estado;
                if (!empty($upd)) {
                    $clienteModel->update($cliente['cli_id'], $upd);
                }
            }

            // Inicia sessão automática do consumidor
            session()->set('linecash_cliente_cpf', $cleanCpf);
            session()->set('linecash_cliente_id', $conta['lcc_id']);
            session()->set('linecash_cliente_nome', $nome);

            $msg = "Parabéns, {$nome}! Seu cadastro no LineCash foi realizado com sucesso. Sua carteira digital já está ativa para acumular e resgatar cashback!";
            return redirect()->to(base_url('linecash'))->with('success', $msg);

        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Erro ao processar cadastro: ' . $e->getMessage());
        }
    }
}
