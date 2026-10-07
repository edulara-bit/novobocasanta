<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RedirecionaModel;

class Redirecionamentos extends BaseController
{
    protected RedirecionaModel $redirecionaModel;

    public function __construct()
    {
        $this->redirecionaModel = new RedirecionaModel();
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

        $redirecionamentos = $this->redirecionaModel->orderBy('red_id', 'DESC')->findAll();

        return view('admin/redirecionamentos/index', [
            'title'             => 'Gerenciamento de Redirecionamentos SEO (301/302)',
            'redirecionamentos' => $redirecionamentos,
        ]);
    }

    public function criar()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        return view('admin/redirecionamentos/form', [
            'title' => 'Novo Redirecionamento',
            'red'   => null,
        ]);
    }

    public function salvar()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $de   = trim((string)$this->request->getPost('red_link_antigo'));
        $para = trim((string)$this->request->getPost('red_link_novo'));

        if (empty($de) || empty($para)) {
            return redirect()->back()->withInput()->with('error', 'Informe a URL de origem e o destino.');
        }

        $this->redirecionaModel->insert([
            'red_titulo'      => $this->request->getPost('red_titulo') ?: "Redirecionamento {$de}",
            'red_link_antigo' => ltrim($de, '/'),
            'red_link_novo'   => ltrim($para, '/'),
        ]);

        return redirect()->to(base_url('admin/redirecionamentos'))->with('success', 'Redirecionamento cadastrado com sucesso!');
    }

    public function editar(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $red = $this->redirecionaModel->find($id);
        if (!$red) {
            return redirect()->to(base_url('admin/redirecionamentos'))->with('error', 'Redirecionamento não encontrado.');
        }

        return view('admin/redirecionamentos/form', [
            'title' => "Editar Redirecionamento #{$id}",
            'red'   => $red,
        ]);
    }

    public function atualizar(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $de   = trim((string)$this->request->getPost('red_link_antigo'));
        $para = trim((string)$this->request->getPost('red_link_novo'));

        $this->redirecionaModel->update($id, [
            'red_titulo'      => $this->request->getPost('red_titulo'),
            'red_link_antigo' => ltrim($de, '/'),
            'red_link_novo'   => ltrim($para, '/'),
        ]);

        return redirect()->to(base_url('admin/redirecionamentos'))->with('success', 'Redirecionamento atualizado com sucesso!');
    }

    public function excluir(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $this->redirecionaModel->delete($id);
        return redirect()->to(base_url('admin/redirecionamentos'))->with('success', 'Redirecionamento excluído com sucesso!');
    }
}
