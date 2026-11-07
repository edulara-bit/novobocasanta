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
use App\Models\LcParceiroProdutoModel;
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
    protected LcParceiroProdutoModel $parceiroProdutoModel;
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
        $this->parceiroProdutoModel = new LcParceiroProdutoModel();
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
        $contaId = (int)$conta['lcc_id'];
        $config = $this->configModel->find($contaId);
        if (!$config) {
            $this->configModel->ignore(true)->insert([
                'lcf_lojista_id'        => $contaId,
                'lcf_percentual_padrao' => 1.00,
            ]);
            $config = $this->configModel->find($contaId);
        }

        $assinatura = $this->assinaturaModel->where('lca_lojista_id', $contaId)->first();

        $ultimasTransacoes = $this->transacaoModel->getExtratoByConta($contaId, 20);
        $depositos = $this->depositoModel->where('lcd_lojista_id', $contaId)->orderBy('lcd_id', 'DESC')->findAll(10);
        $resgates = $this->resgateModel->where('lcr_lojista_id', $contaId)->orderBy('lcr_id', 'DESC')->findAll(10);

        // Busca ofertas ativas do parceiro para atalho no PDV
        $produtoModel = new \App\Models\ProdutoModel();
        $minhasOfertas = $produtoModel->where('pro_parceiro', $parceiro->par_id)->orderBy('pro_id', 'DESC')->findAll(30);

        // Busca produtos cadastrados para a Zona de Calor / Cesta LineCash
        $meusProdutosCesta = $this->parceiroProdutoModel->getByParceiro($parceiro->par_id);

        return view('parceiro/linecash/index', [
            'title'             => 'LineCash PDV - Caixa e Fidelidade',
            'parceiro'          => $parceiro,
            'conta'             => $conta,
            'config'            => $config,
            'assinatura'        => $assinatura,
            'ultimasTransacoes' => $ultimasTransacoes,
            'depositos'         => $depositos,
            'resgates'          => $resgates,
            'minhasOfertas'     => $minhasOfertas,
            'meusProdutosCesta' => $meusProdutosCesta,
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

        $clienteModel = new \App\Models\ClienteModel();
        $cliente = $clienteModel->where('cli_cpf', $cleanCpf)->first();

        if ($contaCliente || $cliente) {
            $nome = $contaCliente['lcc_nome'] ?? $cliente['cli_nome'] ?? '';
            $whatsapp = $contaCliente['lcc_whatsapp'] ?? $cliente['cli_celular'] ?? '';
            $saldo = (float)($contaCliente['lcc_saldo'] ?? 0);

            return $this->response->setJSON([
                'found'           => true,
                'nome'            => $nome,
                'whatsapp'        => $whatsapp,
                'saldo'           => $saldo,
                'saldo_formatado' => number_format($saldo, 2, ',', '.') . ' LC',
                'cep'             => $cliente['cli_cep'] ?? '',
                'endereco'        => $cliente['cli_endereco'] ?? '',
                'numero'          => $cliente['cli_numero'] ?? '',
                'complemento'     => $cliente['cli_complemento'] ?? '',
                'bairro'          => $cliente['cli_bairro'] ?? '',
                'cidade'          => $cliente['cli_cidade'] ?? '',
                'estado'          => $cliente['cli_estado'] ?? '',
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
        $contaId = (int)$conta['lcc_id'];

        $cpf = (string)$this->request->getPost('cpf');
        $valorCompra = $this->parseValorMonetario((string)$this->request->getPost('valor_compra'));
        $percentual = (float)str_replace(',', '.', (string)$this->request->getPost('percentual'));
        $nome = trim((string)$this->request->getPost('nome'));
        $whatsapp = trim((string)$this->request->getPost('whatsapp'));

        // Novos campos de endereço preenchidos na tela do caixa (se houver)
        $cep = trim((string)$this->request->getPost('cep'));
        $endereco = trim((string)$this->request->getPost('endereco'));
        $numero = trim((string)$this->request->getPost('numero'));
        $complemento = trim((string)$this->request->getPost('complemento'));
        $bairro = trim((string)$this->request->getPost('bairro'));
        $cidade = trim((string)$this->request->getPost('cidade'));
        $estado = strtoupper(trim((string)$this->request->getPost('estado')));
        $linkOfertaAvulso = trim((string)$this->request->getPost('link_oferta_avulso'));
        $tituloOfertaAvulso = trim((string)$this->request->getPost('titulo_oferta_avulso'));

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
            $contaId,
            $cpf,
            $valorCompra,
            $percentual,
            $produtos,
            $nome ?: null,
            $whatsapp ?: null
        );

        if ($res['success']) {
            $cleanCpf = $this->linecashService->cleanDoc($cpf);

            // Se foram informados dados de endereço ou complementares, sincroniza com tb_clientes
            $clienteModel = new \App\Models\ClienteModel();
            $cliente = $clienteModel->where('cli_cpf', $cleanCpf)->first();

            $dadosCli = [
                'cli_cpf'      => $cleanCpf,
                'cli_nome'     => $nome ?: ($cliente['cli_nome'] ?? ''),
                'cli_celular'  => $whatsapp ?: ($cliente['cli_celular'] ?? ''),
                'cli_parceiro' => $parceiro->par_id,
            ];
            if (!empty($cep)) $dadosCli['cli_cep'] = $cep;
            if (!empty($endereco)) $dadosCli['cli_endereco'] = $endereco;
            if (!empty($numero)) $dadosCli['cli_numero'] = $numero;
            if (!empty($complemento)) $dadosCli['cli_complemento'] = $complemento;
            if (!empty($bairro)) $dadosCli['cli_bairro'] = $bairro;
            if (!empty($cidade)) $dadosCli['cli_cidade'] = $cidade;
            if (!empty($estado)) $dadosCli['cli_estado'] = $estado;

            if (!$cliente) {
                $dadosCli['cli_data'] = date('Y-m-d H:i:s');
                $clienteModel->insert($dadosCli);
            } else {
                $clienteModel->update($cliente['cli_id'], $dadosCli);
            }

            // Busca dados da vitrine LineFast do lojista
            $config = $this->configModel->find($contaId);
            $clienteWhats = $res['cliente']['lcc_whatsapp'] ?? $whatsapp;
            $clienteNome = $res['cliente']['lcc_nome'] ?? $nome;

            $prodTitulo = !empty($tituloOfertaAvulso) ? $tituloOfertaAvulso : ($config['lcf_produto_destaque_titulo'] ?? null);
            $prodUrl = !empty($linkOfertaAvulso) ? $linkOfertaAvulso : ($config['lcf_produto_destaque_url'] ?? null);

            $whatsMsg = LinecashWhatsappService::gerarMensagemCashback(
                $clienteNome,
                $parceiro->getNome() ?: 'Loja Parceira',
                $valorCompra,
                (float)$res['valor_lc'],
                (float)$res['novo_saldo_cliente'],
                $prodTitulo,
                $prodUrl,
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
        $contaId = (int)$conta['lcc_id'];

        $cpf = (string)$this->request->getPost('cpf_pagamento');
        $valorLc = (float)str_replace(['.', ','], ['', '.'], (string)$this->request->getPost('valor_lc'));

        $res = $this->linecashService->pagarComLinecash($contaId, $cpf, $valorLc);

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
        $contaId = (int)$conta['lcc_id'];

        $valorReais = (float)str_replace(['.', ','], ['', '.'], (string)$this->request->getPost('valor_recarga'));
        $comprovanteUrl = trim((string)$this->request->getPost('comprovante_url'));
        $obs = trim((string)$this->request->getPost('observacao'));

        $res = $this->linecashService->solicitarDeposito($contaId, $valorReais, $comprovanteUrl ?: null, $obs ?: null);

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
        $contaId = (int)$conta['lcc_id'];

        $valorLc = (float)str_replace(['.', ','], ['', '.'], (string)$this->request->getPost('valor_resgate'));
        $chavePix = trim((string)$this->request->getPost('chave_pix'));
        $tipoChave = trim((string)$this->request->getPost('tipo_chave'));

        $res = $this->linecashService->solicitarResgatePix($contaId, $valorLc, $chavePix, $tipoChave ?: null);

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
        $contaId = (int)$conta['lcc_id'];
        $email = (string)($parceiro->par_email ?: 'lojista' . $parceiro->par_id . '@bocasanta.com.br');
        $nome = (string)($parceiro->getNome() ?: 'Lojista #' . $parceiro->par_id);

        $res = $this->mpService->gerarLinkAssinatura($contaId, $email, $nome);

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
        $contaId = (int)$conta['lcc_id'];

        $percentual = (float)str_replace(',', '.', (string)$this->request->getPost('percentual_padrao'));
        $tituloDestaque = trim((string)$this->request->getPost('produto_destaque_titulo'));
        $urlDestaque = trim((string)$this->request->getPost('produto_destaque_url'));
        $msgCustom = trim((string)$this->request->getPost('mensagem_customizada'));

        $this->configModel->save([
            'lcf_lojista_id'             => $contaId,
            'lcf_percentual_padrao'      => max(1.0, $percentual),
            'lcf_produto_destaque_titulo'=> $tituloDestaque ?: null,
            'lcf_produto_destaque_url'   => $urlDestaque ?: null,
            'lcf_mensagem_customizada'   => $msgCustom ?: null,
        ]);

        return redirect()->to(base_url('parceiro/linecash'))->with('success', 'Configurações de PDV e Vitrine LineFast salvas com sucesso!');
    }

    /**
     * Lista os produtos da cesta/zona de calor cadastrados pelo parceiro (JSON)
     */
    public function listarProdutos()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) {
            return $this->response->setJSON(['success' => false, 'message' => 'Não autorizado'])->setStatusCode(401);
        }

        $produtos = $this->parceiroProdutoModel->getByParceiro($parceiro->par_id);
        return $this->response->setJSON(['success' => true, 'produtos' => $produtos]);
    }

    /**
     * Salva ou atualiza um produto da cesta/zona de calor (JSON)
     */
    public function salvarProduto()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) {
            return $this->response->setJSON(['success' => false, 'message' => 'Não autorizado'])->setStatusCode(401);
        }

        $id = (int)$this->request->getPost('id');
        $codigo = trim((string)$this->request->getPost('codigo'));
        $descricao = trim((string)$this->request->getPost('descricao'));

        if (empty($codigo) || empty($descricao)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Informe o código e o nome do produto.']);
        }

        // Verifica se já existe outro produto com o mesmo código para este parceiro
        $existente = $this->parceiroProdutoModel->where('lpp_parceiro_id', $parceiro->par_id)
            ->where('lpp_codigo', $codigo)
            ->where('lpp_ativo', 1)
            ->first();

        if ($existente && (int)$existente['lpp_id'] !== $id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "O código '{$codigo}' já está cadastrado para o produto '{$existente['lpp_descricao']}'.",
            ]);
        }

        $data = [
            'lpp_parceiro_id' => $parceiro->par_id,
            'lpp_codigo'      => $codigo,
            'lpp_descricao'   => $descricao,
            'lpp_ativo'       => 1,
        ];

        if ($id > 0) {
            $data['lpp_id'] = $id;
            $this->parceiroProdutoModel->save($data);
            $msg = 'Produto atualizado com sucesso!';
        } else {
            $this->parceiroProdutoModel->insert($data);
            $msg = 'Produto cadastrado com sucesso!';
        }

        $produtosAtualizados = $this->parceiroProdutoModel->getByParceiro($parceiro->par_id);

        return $this->response->setJSON([
            'success'  => true,
            'message'  => $msg,
            'produtos' => $produtosAtualizados,
        ]);
    }

    /**
     * Exclui (desativa) um produto da cesta/zona de calor (JSON)
     */
    public function excluirProduto(int $id)
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) {
            return $this->response->setJSON(['success' => false, 'message' => 'Não autorizado'])->setStatusCode(401);
        }

        $prod = $this->parceiroProdutoModel->where('lpp_id', $id)
            ->where('lpp_parceiro_id', $parceiro->par_id)
            ->first();

        if (!$prod) {
            return $this->response->setJSON(['success' => false, 'message' => 'Produto não encontrado.']);
        }

        $this->parceiroProdutoModel->delete($id);
        $produtosAtualizados = $this->parceiroProdutoModel->getByParceiro($parceiro->par_id);

        return $this->response->setJSON([
            'success'  => true,
            'message'  => 'Produto removido com sucesso!',
            'produtos' => $produtosAtualizados,
        ]);
    }

    /**
     * Converte entrada monetária em float de forma segura
     */
    private function parseValorMonetario(string $valor): float
    {
        $valor = trim($valor);
        if ($valor === '') {
            return 0.0;
        }
        $valor = preg_replace('/[^\d.,]/', '', $valor);
        if (str_contains($valor, ',')) {
            // Formato brasileiro: 1.250,50 ou 300,00
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        } elseif (preg_match('/^\d+\.\d{1,2}$/', $valor)) {
            // Formato com ponto decimal: 300.00
            // Mantém como float
        } elseif (str_contains($valor, '.')) {
            // Caso ponto de milhar sem vírgula: 1.500
            $valor = str_replace('.', '', $valor);
        }
        return (float)$valor;
    }
}
