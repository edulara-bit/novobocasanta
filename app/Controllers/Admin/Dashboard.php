<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ParceiroModel;
use App\Models\ProdutoModel;
use Config\Database;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('admin_logged')) {
            return redirect()->to(base_url('admin/login'))->with('error', 'Por favor, faça login para acessar o painel.');
        }

        $produtoModel = new ProdutoModel();
        $parceiroModel = new ParceiroModel();
        $db = Database::connect();

        $totalOfertas = $produtoModel->countAllResults();
        $totalParceiros = $parceiroModel->countAllResults();
        $totalLinefast = $parceiroModel->where('linefast_ativo', 1)->countAllResults();
        $solicitacoesLgpd = $db->table('tb_lgpd_requests')->where('status', 'pendente')->countAllResults();

        $ultimasOfertas = $produtoModel->getOfertas(['ordem' => 'mais_recentes'], 6);

        return view('admin/dashboard', [
            'title'            => 'Dashboard Administrativo',
            'totalOfertas'     => $totalOfertas,
            'totalParceiros'   => $totalParceiros,
            'totalLinefast'    => $totalLinefast,
            'solicitacoesLgpd' => $solicitacoesLgpd,
            'ultimasOfertas'   => $ultimasOfertas,
        ]);
    }
}
