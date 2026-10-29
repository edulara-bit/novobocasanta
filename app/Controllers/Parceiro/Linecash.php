<?php

declare(strict_types=1);

namespace App\Controllers\Parceiro;

use App\Controllers\BaseController;
use App\Models\LcContaModel;
use App\Models\LcTransacaoModel;
use App\Models\LcDepositoModel;
use App\Models\LcResgateModel;
use App\Models\LcConfigLojistaModel;
use App\Models\LcAssinaturaLojistaModel;
use App\Models\ParceiroModel;
use App\Services\LinecashService;
use App\Services\LinecashWhatsappService;
use App\Services\MercadoPagoLinecashService;

class Linecash extends BaseController
{
    protected LinecashService $linecashService;
    protected LinecashWhatsappService $whatsappService;
    protected MercadoPagoLinecashService $mpService;
    protected LcContaModel $contaModel;
    protected LcTransacaoModel $transacaoModel;
    protected LcDepositoModel $depositoModel;
    protected LcResgateModel $resgateModel;
    protected LcConfigLojistaModel $configModel;
    protected LcAssinaturaLojistaModel $assinaturaModel;
    protected ParceiroModel $parceiroModel;

    public function __construct()
    {
        $this->linecashService = new LinecashService();
        $this->whatsappService = new LinecashWhatsappService();
        $this->mpService = new MercadoPagoLinecashService();
        $this->contaModel = new LcContaModel();
        $this->transacaoModel = new LcTransacaoModel();
        $this->depositoModel = new LcDepositoModel();
        $this->resgateModel = new LcResgateModel();
        $this->configModel = new LcConfigLojistaModel();
        $this->assinaturaModel = new LcAssinaturaLojistaModel();
        $this->parceiroModel = new ParceiroModel();
    }

    protected function getParceiroLogado()
    {
        $id = (int)session()->get('parceiro_id');
        if ($id <= 0) {
            return null;
        }
        return $this->parceiroModel->find($id);
    }

    /**
     * Tela Principal do PDV LineCash do Lojista
     */
    public function index()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) {
            return redirect()->to(base_url('parceiro/login'))->with('error', 'Sessão expirada. Faça login novamente.');
        }

        $conta = $this->linecashService->getOrCreateContaLojista($parceiro->par_id);
        $config = $this->configModel->find($conta['lcc_id']);
        if (!$config) {
            $this->configModel->ignore(true)->insert([
                'lcf_lojista_id'        => $conta['lcc_id'],
                'lcf_percentual_padrao' => 1.00,
            ]);
            $config = $this->configModel->find($conta['lcc_id']);
        }

        $assinatura = $this->assinaturaModel->where('lca_lojista_id', $conta['lcc_id'])->first();

        $ultimasTransacoes = $this->transacaoModel->getExtratoByConta($conta['lcc_id'], 20);
        $depositos = $this->depositoModel->where('lcd_lojista_id', $conta['lcc_id'])->orderBy('lcd_id', 'DESC')->findAll(10);
        $resgates = $this->resgateModel->where('lcr_lojista_id', $conta['lcc_id'])->orderBy('lcr_id', 'DESC')->findAll(10);

        return view('parceiro/linecash/index', [
            'title'             => 'LineCash PDV - Caixa e Fidelidade',
            'parceiro'          => $parceiro,
            'conta'             => $conta,
            'config'            => $config,
            'assinatura'        => $assinatura,
            'ultimasTransacoes' => $ultimasTransacoes,
            'depositos'         => $depositos,
            'resgates'          => $resgates,
        ]);
    }

    /**
     * Consulta rápida de CPF via AJAX (retorna dados e saldo do cliente)
     */
    public function consultarCpf(string $cpf)
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) {
            return $this->response->setJSON(['found' => false, 'message' => 'Não autorizado'])->setStatusCode(401);
        }

        $cleanCpf = $this->linecashService->cleanDoc($cpf);
        $contaCliente = $this->contaModel->findByDoc($cleanCpf);

        if ($contaCliente) {
            return $this->response->setJSON([
                'found'    => true,
                'nome'     => $contaCliente['lcc_nome'],
                'whatsapp' => $contaCliente['lcc_whatsapp'],
                'saldo'    => (float)$contaCliente['lcc_saldo'],
                'saldo_formatado' => number_format((float)$contaCliente['lcc_saldo'], 2, ',', '.') . ' LC',
            ]);
        }

        return $this->response->setJSON([
            'found'   => false,
            'message' => 'Cliente ainda não cadastrado. Será criado automaticamente no primeiro cashback!',
        ]);
    }

    /**
     * Processa lançamento de Cashback no PDV
     */
    public function lancarCashback()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) {
            return redirect()->to(base_url('parceiro/login'))->with('error', 'Sessão expirada.');
        }

        $conta = $this->linecashService->getOrCreateContaLojista($parceiro->par_id);

        $cpf = (string)$this->request->getPost('cpf');
        $valorCompra = (float)str_replace(['.', ','], ['', '.'], (string)$this->request->getPost('valor_compra'));
        $percentual = (float)str_replace(',', '.', (string)$this->request->getPost('percentual'));
        $nome = trim((string)$this->request->getPost('nome'));
        $whatsapp = trim((string)$this->request->getPost('whatsapp'));

        // Cesta / Zona de Calor (até 3 produtos)
        $produtos = [];
        for ($i = 1; $i <= 3; $i++) {
            $cod = trim((string)$this->request->getPost("prod_codigo_{$i}"));
            $desc = trim((string)$this->request->getPost("prod_desc_{$i}"));
            if (!empty($cod)) {
                $produtos[] = ['codigo' => $cod, 'descricao' => $desc];
            }
        }

        $res = $this->linecashService->lancarCashbackPdv(
            $conta['lcc_id'],
            $cpf,
            $valorCompra,
            $percentual,
            $produtos,
            $nome ?: null,
            $whatsapp ?: null
        );

        if ($res['success']) {
            // Busca dados da vitrine LineFast do lojista
            $config = $this->configModel->find($conta['lcc_id']);
            $clienteWhats = $res['cliente']['lcc_whatsapp'] ?? $whatsapp;
            $clienteNome = $res['cliente']['lcc_nome'] ?? $nome;

            $whatsMsg = LinecashWhatsappService::gerarMensagemCashback(
                $clienteNome,
                $parceiro->getNome() ?: 'Loja Parceira',
                $valorCompra,
                (float)$res['valor_lc'],
                (float)$res['novo_saldo_cliente'],
                $config['lcf_produto_destaque_titulo'] ?? null,
                $config['lcf_produto_destaque_url'] ?? null,
                $config['lcf_mensagem_customizada'] ?? null
            );

            $whatsLink = !empty($clienteWhats) ? LinecashWhatsappService::gerarLinkDireto($clienteWhats, $whatsMsg) : '';

            return redirect()->to(base_url('parceiro/linecash'))
                ->with('success', $res['message'])
                ->with('whatsapp_link', $whatsLink)
                ->with('whatsapp_cliente', $clienteWhats)
                ->with('whatsapp_msg', $whatsMsg);
        }

        return redirect()->to(base_url('parceiro/linecash'))->with('error', $res['message']);
    }

    /**
     * Processa Recebimento em LineCash (Cliente paga a compra)
     */
    public function receberLinecash()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) {
            return redirect()->to(base_url('parceiro/login'))->with('error', 'Sessão expirada.');
        }

        $conta = $this->linecashService->getOrCreateContaLojista($parceiro->par_id);

        $cpf = (string)$this->request->getPost('cpf_pagamento');
        $valorLc = (float)str_replace(['.', ','], ['', '.'], (string)$this->request->getPost('valor_lc'));

        $res = $this->linecashService->pagarComLinecash($conta['lcc_id'], $cpf, $valorLc);

        if ($res['success']) {
            $cleanCpf = $this->linecashService->cleanDoc($cpf);
            $contaCliente = $this->contaModel->findByDoc($cleanCpf);

            if ($contaCliente && !empty($contaCliente['lcc_whatsapp'])) {
                $whatsMsg = LinecashWhatsappService::gerarMensagemPagamento(
                    $contaCliente['lcc_nome'],
                    $parceiro->getNome() ?: 'Loja Parceira',
                    $valorLc,
                    (float)$contaCliente['lcc_saldo']
                );
                $whatsLink = LinecashWhatsappService::gerarLinkDireto($contaCliente['lcc_whatsapp'], $whatsMsg);
                return redirect()->to(base_url('parceiro/linecash'))
                    ->with('success', $res['message'])
                    ->with('whatsapp_link', $whatsLink)
                    ->with('whatsapp_cliente', $contaCliente['lcc_whatsapp']);
            }

            return redirect()->to(base_url('parceiro/linecash'))->with('success', $res['message']);
        }

        return redirect()->to(base_url('parceiro/linecash'))->with('error', $res['message']);
    }

    /**
     * Solicitar Recarga / Depósito
     */
    public function solicitarRecarga()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) {
            return redirect()->to(base_url('parceiro/login'))->with('error', 'Sessão expirada.');
        }

        $conta = $this->linecashService->getOrCreateContaLojista($parceiro->par_id);

        $valorReais = (float)str_replace(['.', ','], ['', '.'], (string)$this->request->getPost('valor_recarga'));
        $comprovanteUrl = trim((string)$this->request->getPost('comprovante_url'));
        $obs = trim((string)$this->request->getPost('observacao'));

        $res = $this->linecashService->solicitarDeposito($conta['lcc_id'], $valorReais, $comprovanteUrl ?: null, $obs ?: null);

        if ($res['success']) {
            return redirect()->to(base_url('parceiro/linecash'))->with('success', $res['message']);
        }

        return redirect()->to(base_url('parceiro/linecash'))->with('error', $res['message']);
    }

    /**
     * Solicitar Resgate PIX (LC -> R$)
     */
    public function solicitarResgate()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) {
            return redirect()->to(base_url('parceiro/login'))->with('error', 'Sessão expirada.');
        }

        $conta = $this->linecashService->getOrCreateContaLojista($parceiro->par_id);

        $valorLc = (float)str_replace(['.', ','], ['', '.'], (string)$this->request->getPost('valor_resgate'));
        $chavePix = trim((string)$this->request->getPost('chave_pix'));
        $tipoChave = trim((string)$this->request->getPost('tipo_chave'));

        $res = $this->linecashService->solicitarResgatePix($conta['lcc_id'], $valorLc, $chavePix, $tipoChave ?: null);

        if ($res['success']) {
            return redirect()->to(base_url('parceiro/linecash'))->with('success', $res['message']);
        }

        return redirect()->to(base_url('parceiro/linecash'))->with('error', $res['message']);
    }

    /**
     * Gerar link de assinatura Mercado Pago (R$ 20,00/mês)
     */
    public function assinarMercadoPago()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) {
            return redirect()->to(base_url('parceiro/login'))->with('error', 'Sessão expirada.');
        }

        $conta = $this->linecashService->getOrCreateContaLojista($parceiro->par_id);
        $email = (string)($parceiro->par_email ?: 'lojista' . $parceiro->par_id . '@bocasanta.com.br');
        $nome = (string)($parceiro->getNome() ?: 'Lojista #' . $parceiro->par_id);

        $res = $this->mpService->gerarLinkAssinatura($conta['lcc_id'], $email, $nome);

        if ($res['success'] && !empty($res['checkout_url'])) {
            return redirect()->to($res['checkout_url']);
        }

        return redirect()->to(base_url('parceiro/linecash'))->with('info', $res['message'] ?? 'Aguardando confirmação do plano.');
    }

    /**
     * Salvar Configurações do Lojista (Percentual padrão e vitrine LineFast)
     */
    public function salvarConfig()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) {
            return redirect()->to(base_url('parceiro/login'))->with('error', 'Sessão expirada.');
        }

        $conta = $this->linecashService->getOrCreateContaLojista($parceiro->par_id);

        $percentual = (float)str_replace(',', '.', (string)$this->request->getPost('percentual_padrao'));
        $tituloDestaque = trim((string)$this->request->getPost('produto_destaque_titulo'));
        $urlDestaque = trim((string)$this->request->getPost('produto_destaque_url'));
        $msgCustom = trim((string)$this->request->getPost('mensagem_customizada'));

        $this->configModel->save([
            'lcf_lojista_id'             => $conta['lcc_id'],
            'lcf_percentual_padrao'      => max(1.0, $percentual),
            'lcf_produto_destaque_titulo'=> $tituloDestaque ?: null,
            'lcf_produto_destaque_url'   => $urlDestaque ?: null,
            'lcf_mensagem_customizada'   => $msgCustom ?: null,
        ]);

        return redirect()->to(base_url('parceiro/linecash'))->with('success', 'Configurações de PDV e Vitrine LineFast salvas com sucesso!');
    }
}
