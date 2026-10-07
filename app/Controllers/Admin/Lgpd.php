<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ConteudoModel;
use App\Services\LgpdService;
use Config\Database;

class Lgpd extends BaseController
{
    protected LgpdService $lgpdService;
    protected ConteudoModel $conteudoModel;
    protected $db;

    public function __construct()
    {
        $this->lgpdService = new LgpdService();
        $this->conteudoModel = new ConteudoModel();
        $this->db = Database::connect();
    }

    protected function checkAuth()
    {
        if (!session()->get('admin_logged')) {
            return redirect()->to(base_url('admin/login'))->with('error', 'Acesso restrito. Faça login.');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $solicitacoes = $this->db->table('tb_lgpd_requests')
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();

        $consentimentos = $this->db->table('tb_lgpd_consents')
            ->orderBy('created_at', 'DESC')
            ->limit(50)
            ->get()->getResultArray();

        $paginas = $this->conteudoModel
            ->whereIn('con_slug', ['politica-privacidade', 'termos-de-uso'])
            ->findAll();

        return view('admin/lgpd/index', [
            'title'          => 'Central de Governança LGPD & Políticas',
            'solicitacoes'   => $solicitacoes,
            'consentimentos' => $consentimentos,
            'paginas'        => $paginas,
        ]);
    }

    public function salvarPagina()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $slug     = trim((string)$this->request->getPost('con_slug'));
        $titulo   = trim((string)$this->request->getPost('con_titulo'));
        $conteudo = (string)$this->request->getPost('con_conteudo');

        $existente = $this->conteudoModel->where('con_slug', $slug)->first();
        if ($existente) {
            $this->conteudoModel->update($existente['con_id'], [
                'con_titulo'   => $titulo,
                'con_conteudo' => $conteudo,
            ]);
        } else {
            $this->conteudoModel->insert([
                'con_slug'        => $slug,
                'con_titulo'      => $titulo,
                'con_conteudo'    => $conteudo,
                'con_tipopagina'  => 'institucional',
            ]);
        }

        return redirect()->to(base_url('admin/lgpd'))->with('success', "Página '{$titulo}' atualizada com sucesso!");
    }

    public function exportar(int $userId)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $dados = $this->lgpdService->exportUserData($userId);

        return $this->response
            ->setHeader('Content-Disposition', 'attachment; filename="lgpd_export_user_' . $userId . '.json"')
            ->setJSON($dados);
    }

    public function anonimizar(int $userId)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $this->lgpdService->anonymizeUser($userId);
        return redirect()->to(base_url('admin/lgpd'))->with('success', 'Dados do titular anonimizados com sucesso.');
    }

    public function concluir(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $this->db->table('tb_lgpd_requests')->where('id', $id)->update([
            'status'     => 'concluido',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('admin/lgpd'))->with('success', 'Solicitação marcada como concluída.');
    }
}
