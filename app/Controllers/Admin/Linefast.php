<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ParceiroModel;
use App\Services\LinefastService;

class Linefast extends BaseController
{
    protected ParceiroModel $parceiroModel;
    protected LinefastService $linefastService;

    public function __construct()
    {
        $this->parceiroModel = new ParceiroModel();
        $this->linefastService = new LinefastService();
    }

    public function index()
    {
        if (!session()->get('admin_logged')) {
            return redirect()->to(base_url('admin/login'))->with('error', 'Por favor, faça login para acessar.');
        }

        $builder = $this->parceiroModel;
        if ($this->parceiroModel->db->fieldExists('linefast_ativo', 'tb_parceiros')) {
            $builder = $builder->orderBy('linefast_ativo', 'DESC');
        }
        $parceiros = $builder->orderBy('par_nome', 'ASC')->findAll(100);

        return view('admin/linefast/index', [
            'title'     => 'Gerenciamento da Integração Linefast',
            'parceiros' => $parceiros,
        ]);
    }

    public function sync(int $parceiroId)
    {
        $result = $this->linefastService->syncPartnerCatalog($parceiroId);
        if ($result['success']) {
            return redirect()->to(base_url('admin/linefast'))->with('success', $result['message']);
        }

        return redirect()->to(base_url('admin/linefast'))->with('error', $result['message']);
    }

    public function toggleTest(int $parceiroId)
    {
        if (!session()->get('admin_logged')) {
            return redirect()->to(base_url('admin/login'))->with('error', 'Por favor, faça login para acessar.');
        }

        $parceiro = $this->parceiroModel->find($parceiroId);
        if (!$parceiro) {
            return redirect()->to(base_url('admin/linefast'))->with('error', 'Parceiro não encontrado.');
        }

        $current = (int)($parceiro->linefast_is_test ?? 0);
        $newStatus = $current === 1 ? 0 : 1;

        $this->parceiroModel->update($parceiroId, [
            'linefast_is_test' => $newStatus,
        ]);

        $statusText = $newStatus === 1 ? 'marcado como Parceiro de Testes (ocultado do Boca Santa)' : 'marcado como Parceiro de Produção (visível no Boca Santa)';
        return redirect()->to(base_url('admin/linefast'))->with('success', "Parceiro \"{$parceiro->getNome()}\" {$statusText}.");
    }
}
