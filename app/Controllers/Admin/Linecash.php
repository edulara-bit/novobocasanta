<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LcContaModel;
use App\Models\LcTransacaoModel;
use App\Models\LcDepositoModel;
use App\Models\LcResgateModel;
use App\Models\ParceiroModel;
use App\Services\LinecashService;

class Linecash extends BaseController
{
    protected LinecashService $linecashService;
    protected LcContaModel $contaModel;
    protected LcTransacaoModel $transacaoModel;
    protected LcDepositoModel $depositoModel;
    protected LcResgateModel $resgateModel;
    protected ParceiroModel $parceiroModel;

    public function __construct()
    {
        $this->linecashService = new LinecashService();
        $this->contaModel = new LcContaModel();
        $this->transacaoModel = new LcTransacaoModel();
        $this->depositoModel = new LcDepositoModel();
        $this->resgateModel = new LcResgateModel();
        $this->parceiroModel = new ParceiroModel();
    }

    protected function checkAuth()
    {
        if (!session()->get('admin_logged')) {
            return redirect()->to(base_url('admin/login'))->with('error', 'Acesso restrito. Faça login.');
        }
        return null;
    }

    /**
     * Dashboard Geral do LineCash
     */
    public function index()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $metricas = $this->linecashService->getMetricasGerais();
        $ultimasTransacoes = $this->transacaoModel
            ->select('tb_lc_transacoes.*, co.lcc_nome as origem_nome, cd.lcc_nome as destino_nome')
            ->join('tb_lc_contas co', 'co.lcc_id = tb_lc_transacoes.lct_origem_id', 'left')
            ->join('tb_lc_contas cd', 'cd.lcc_id = tb_lc_transacoes.lct_destino_id', 'left')
            ->orderBy('lct_id', 'DESC')
            ->findAll(10);

        $depositosPendentes = $this->depositoModel->getDepositosComLojista('pendente');
        $resgatesPendentes = $this->resgateModel->getResgatesComLojista('pendente');

        return view('admin/linecash/index', [
            'title'               => 'Painel de Controle LineCash',
            'metricas'            => $metricas,
            'ultimasTransacoes'   => $ultimasTransacoes,
            'depositosPendentes'  => $depositosPendentes,
            'resgatesPendentes'   => $resgatesPendentes,
        ]);
    }

    /**
     * Gestão de Recargas / Depósitos de Lojistas
     */
    public function depositos()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $status = $this->request->getGet('status');
        $depositos = $this->depositoModel->getDepositosComLojista($status ?: null);
        $parceiros = $this->parceiroModel->orderBy('par_nome', 'ASC')->findAll();

        return view('admin/linecash/depositos', [
            'title'     => 'Depósitos e Recargas de Lojistas',
            'depositos' => $depositos,
            'status'    => $status,
            'parceiros' => $parceiros,
        ]);
    }

    /**
     * Aprovar Depósito de Lojista
     */
    public function aprovarDeposito(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $adminId = (int)(session()->get('admin_id') ?? 1);
        $res = $this->linecashService->aprovarDeposito($id, $adminId);

        if ($res['success']) {
            return redirect()->to(base_url('admin/linecash/depositos'))->with('success', $res['message']);
        }

        return redirect()->to(base_url('admin/linecash/depositos'))->with('error', $res['message']);
    }

    /**
     * Rejeitar Depósito de Lojista
     */
    public function rejeitarDeposito(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $adminId = (int)(session()->get('admin_id') ?? 1);
        $motivo = (string)($this->request->getPost('motivo') ?: 'Comprovante não identificado ou depósito inválido.');

        $res = $this->linecashService->rejeitarDeposito($id, $adminId, $motivo);

        if ($res['success']) {
            return redirect()->to(base_url('admin/linecash/depositos'))->with('success', $res['message']);
        }

        return redirect()->to(base_url('admin/linecash/depositos'))->with('error', $res['message']);
    }

    /**
     * Criar Recarga Manual pelo Admin
     */
    public function criarDepositoManual()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $parceiroId = (int)$this->request->getPost('parceiro_id');
        $valorReais = (float)str_replace(['.', ','], ['', '.'], (string)$this->request->getPost('valor'));
        $obs = (string)$this->request->getPost('observacao');

        if ($parceiroId <= 0 || $valorReais < 100.00) {
            return redirect()->to(base_url('admin/linecash/depositos'))->with('error', 'Selecione o lojista e informe um valor mínimo de R$ 100,00.');
        }

        $contaLojista = $this->linecashService->getOrCreateContaLojista($parceiroId);
        $res = $this->linecashService->solicitarDeposito($contaLojista['lcc_id'], $valorReais, null, $obs ?: 'Lançamento manual pelo Admin');

        if (!$res['success']) {
            return redirect()->to(base_url('admin/linecash/depositos'))->with('error', $res['message']);
        }

        // Se marcado para aprovação imediata
        if ($this->request->getPost('aprovar_imediato') === '1') {
            $adminId = (int)(session()->get('admin_id') ?? 1);
            $aprov = $this->linecashService->aprovarDeposito($res['deposito_id'], $adminId);
            return redirect()->to(base_url('admin/linecash/depositos'))->with('success', $aprov['message']);
        }

        return redirect()->to(base_url('admin/linecash/depositos'))->with('success', 'Recarga registrada na fila pendente com sucesso!');
    }

    /**
     * Fila de Resgates PIX (Lojista -> Real)
     */
    public function resgates()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $status = $this->request->getGet('status');
        $resgates = $this->resgateModel->getResgatesComLojista($status ?: null);

        return view('admin/linecash/resgates', [
            'title'    => 'Fila de Resgates PIX de Lojistas',
            'resgates' => $resgates,
            'status'   => $status,
        ]);
    }

    /**
     * Confirmar Pagamento do Resgate PIX
     */
    public function confirmarPagamentoResgate(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $adminId = (int)(session()->get('admin_id') ?? 1);
        $comprovante = (string)$this->request->getPost('comprovante_pix');
        $obs = (string)$this->request->getPost('observacao');

        $res = $this->linecashService->confirmarPagamentoResgate($id, $adminId, $comprovante ?: null, $obs ?: null);

        if ($res['success']) {
            return redirect()->to(base_url('admin/linecash/resgates'))->with('success', $res['message']);
        }

        return redirect()->to(base_url('admin/linecash/resgates'))->with('error', $res['message']);
    }

    /**
     * Cancelar Pedido de Resgate PIX
     */
    public function cancelarResgate(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $adminId = (int)(session()->get('admin_id') ?? 1);
        $motivo = (string)($this->request->getPost('motivo') ?: 'Chave PIX inválida ou solicitação cancelada pelo lojista.');

        $res = $this->linecashService->cancelarResgatePix($id, $adminId, $motivo);

        if ($res['success']) {
            return redirect()->to(base_url('admin/linecash/resgates'))->with('success', $res['message']);
        }

        return redirect()->to(base_url('admin/linecash/resgates'))->with('error', $res['message']);
    }

    /**
     * Livro-Razão / Extrato Geral de Transações
     */
    public function extrato()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $builder = $this->transacaoModel
            ->select('tb_lc_transacoes.*, co.lcc_nome as origem_nome, co.lcc_cpf_cnpj as origem_doc, co.lcc_tipo as origem_tipo,
                      cd.lcc_nome as destino_nome, cd.lcc_cpf_cnpj as destino_doc, cd.lcc_tipo as destino_tipo')
            ->join('tb_lc_contas co', 'co.lcc_id = tb_lc_transacoes.lct_origem_id', 'left')
            ->join('tb_lc_contas cd', 'cd.lcc_id = tb_lc_transacoes.lct_destino_id', 'left');

        $tipo = $this->request->getGet('tipo');
        if (!empty($tipo)) {
            $builder->where('lct_tipo', $tipo);
        }

        $transacoes = $builder->orderBy('lct_id', 'DESC')->findAll(100);

        return view('admin/linecash/extrato', [
            'title'      => 'Livro-Razão & Extrato Geral LineCash',
            'transacoes' => $transacoes,
            'tipo'       => $tipo,
        ]);
    }

    /**
     * Contas e Carteiras Cadastradas (Lojistas e Clientes)
     */
    public function contas()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $tipo = $this->request->getGet('tipo');
        $builder = $this->contaModel;

        if (!empty($tipo)) {
            $builder = $builder->where('lcc_tipo', $tipo);
        }

        $contas = $builder->orderBy('lcc_saldo', 'DESC')->findAll(200);

        return view('admin/linecash/contas', [
            'title'  => 'Contas e Carteiras LineCash',
            'contas' => $contas,
            'tipo'   => $tipo,
        ]);
    }
}
