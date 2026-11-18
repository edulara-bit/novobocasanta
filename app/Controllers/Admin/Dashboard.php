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
        
        $totalLinefast = 0;
        if ($db->fieldExists('linefast_ativo', 'tb_parceiros')) {
            $totalLinefast = $parceiroModel->where('linefast_ativo', 1)->countAllResults();
        }

        $solicitacoesLgpd = 0;
        if ($db->tableExists('tb_lgpd_requests')) {
            $solicitacoesLgpd = $db->table('tb_lgpd_requests')->where('status', 'pendente')->countAllResults();
        } elseif ($db->tableExists('tb_lgpd_solicitacoes')) {
            $solicitacoesLgpd = $db->table('tb_lgpd_solicitacoes')->where('status', 'pendente')->countAllResults();
        }

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

    /**
     * Visualizador de logs do CodeIgniter 4
     */
    public function logs()
    {
        if (!session()->get('admin_logged')) {
            return redirect()->to(base_url('admin/login'))->with('error', 'Por favor, faça login para acessar o painel.');
        }

        $logPath = WRITEPATH . 'logs/';
        $files = glob($logPath . 'log-*.log') ?: [];
        rsort($files);

        $selectedFile = $this->request->getGet('file');
        $fileNames = array_map('basename', $files);

        if (empty($selectedFile) || !in_array($selectedFile, $fileNames, true)) {
            $selectedFile = !empty($fileNames) ? $fileNames[0] : '';
        }

        $lines = [];
        if (!empty($selectedFile) && file_exists($logPath . $selectedFile)) {
            $content = file($logPath . $selectedFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            $lines = array_slice($content, -300);
            $lines = array_reverse($lines);
        }

        return view('admin/logs', [
            'title'        => 'Logs do Sistema (CodeIgniter 4)',
            'files'        => $fileNames,
            'selectedFile' => $selectedFile,
            'lines'        => $lines,
        ]);
    }
}
