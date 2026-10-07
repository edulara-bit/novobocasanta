<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EstadoModel;

class Estados extends BaseController
{
    protected EstadoModel $estadoModel;

    public function __construct()
    {
        $this->estadoModel = new EstadoModel();
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

        $estados = $this->estadoModel->orderBy('est_nome', 'ASC')->findAll();

        return view('admin/estados/index', [
            'title'   => 'Gerenciamento de Estados (UF)',
            'estados' => $estados,
        ]);
    }

    public function criar()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        return view('admin/estados/form', [
            'title'  => 'Cadastrar Novo Estado',
            'estado' => null,
        ]);
    }

    public function salvar()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $nome = trim((string)$this->request->getPost('est_nome'));
        if (empty($nome)) {
            return redirect()->back()->withInput()->with('error', 'Informe o nome do estado.');
        }

        $this->estadoModel->insert(['est_nome' => $nome]);
        return redirect()->to(base_url('admin/estados'))->with('success', 'Estado cadastrado com sucesso!');
    }

    public function editar(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $estado = $this->estadoModel->find($id);
        if (!$estado) {
            return redirect()->to(base_url('admin/estados'))->with('error', 'Estado não encontrado.');
        }

        return view('admin/estados/form', [
            'title'  => "Editar Estado: {$estado['est_nome']}",
            'estado' => $estado,
        ]);
    }

    public function atualizar(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $nome = trim((string)$this->request->getPost('est_nome'));
        $this->estadoModel->update($id, ['est_nome' => $nome]);
        return redirect()->to(base_url('admin/estados'))->with('success', 'Estado atualizado com sucesso!');
    }

    public function excluir(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $this->estadoModel->delete($id);
        return redirect()->to(base_url('admin/estados'))->with('success', 'Estado excluído com sucesso!');
    }
}
