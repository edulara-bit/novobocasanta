<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdministradorModel;

class Administradores extends BaseController
{
    protected AdministradorModel $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdministradorModel();
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

        $administradores = $this->adminModel->orderBy('adm_nome', 'ASC')->findAll();

        return view('admin/administradores/index', [
            'title'           => 'Gerenciamento de Administradores',
            'administradores' => $administradores,
        ]);
    }

    public function criar()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        return view('admin/administradores/form', [
            'title' => 'Novo Administrador',
            'admin' => null,
        ]);
    }

    public function salvar()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $login = trim((string)$this->request->getPost('adm_login'));
        $senha = trim((string)$this->request->getPost('adm_senha'));
        $nome  = trim((string)$this->request->getPost('adm_nome'));

        if (empty($login) || empty($senha)) {
            return redirect()->back()->withInput()->with('error', 'Informe o login e a senha.');
        }

        $this->adminModel->insert([
            'adm_login'        => $login,
            'adm_nome'         => $nome ?: $login,
            'adm_email'        => $this->request->getPost('adm_email'),
            'adm_senha'        => md5($senha),
            'adm_nivel_acesso' => (int)$this->request->getPost('adm_nivel_acesso') ?: 1,
            'adm_status'       => $this->request->getPost('adm_status') ?: 'ativo',
        ]);

        return redirect()->to(base_url('admin/administradores'))->with('success', 'Administrador cadastrado com sucesso!');
    }

    public function editar(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $admin = $this->adminModel->find($id);
        if (!$admin) {
            return redirect()->to(base_url('admin/administradores'))->with('error', 'Administrador não encontrado.');
        }

        return view('admin/administradores/form', [
            'title' => "Editar Administrador: {$admin['adm_nome']}",
            'admin' => $admin,
        ]);
    }

    public function atualizar(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $login = trim((string)$this->request->getPost('adm_login'));
        $nome  = trim((string)$this->request->getPost('adm_nome'));

        $data = [
            'adm_login'        => $login,
            'adm_nome'         => $nome ?: $login,
            'adm_email'        => $this->request->getPost('adm_email'),
            'adm_nivel_acesso' => (int)$this->request->getPost('adm_nivel_acesso') ?: 1,
            'adm_status'       => $this->request->getPost('adm_status') ?: 'ativo',
        ];

        if ($this->request->getPost('adm_senha')) {
            $data['adm_senha'] = md5((string)$this->request->getPost('adm_senha'));
        }

        $this->adminModel->update($id, $data);
        return redirect()->to(base_url('admin/administradores'))->with('success', 'Administrador atualizado com sucesso!');
    }

    public function excluir(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        if ($id === (int)session()->get('admin_id')) {
            return redirect()->to(base_url('admin/administradores'))->with('error', 'Você não pode excluir sua própria conta.');
        }

        $this->adminModel->delete($id);
        return redirect()->to(base_url('admin/administradores'))->with('success', 'Administrador excluído com sucesso!');
    }
}
