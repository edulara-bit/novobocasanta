<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CidadeModel;
use App\Models\EstadoModel;

class Cidades extends BaseController
{
    protected CidadeModel $cidadeModel;
    protected EstadoModel $estadoModel;

    public function __construct()
    {
        $this->cidadeModel = new CidadeModel();
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

        $cidades = $this->cidadeModel
            ->select('tb_cidades.*, tb_estado.est_nome')
            ->join('tb_estado', 'tb_cidades.cid_estado = tb_estado.est_id', 'left')
            ->orderBy('cid_nome', 'ASC')
            ->findAll();

        return view('admin/cidades/index', [
            'title'   => 'Gerenciamento de Cidades Atendidas',
            'cidades' => $cidades,
        ]);
    }

    public function criar()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        return view('admin/cidades/form', [
            'title'   => 'Cadastrar Nova Cidade',
            'cidade'  => null,
            'estados' => $this->estadoModel->findAll(),
        ]);
    }

    public function salvar()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $nome = trim((string)$this->request->getPost('cid_nome'));
        $url = trim((string)$this->request->getPost('cid_url')) ?: url_title(mb_strtolower($nome), '-', true);

        if (empty($nome)) {
            return redirect()->back()->withInput()->with('error', 'Informe o nome da cidade.');
        }

        $this->cidadeModel->insert([
            'cid_nome'   => $nome,
            'cid_estado' => (int)$this->request->getPost('cid_estado'),
            'cid_url'    => $url,
        ]);

        return redirect()->to(base_url('admin/cidades'))->with('success', 'Cidade cadastrada com sucesso!');
    }

    public function editar(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $cidade = $this->cidadeModel->find($id);
        if (!$cidade) {
            return redirect()->to(base_url('admin/cidades'))->with('error', 'Cidade não encontrada.');
        }

        return view('admin/cidades/form', [
            'title'   => "Editar Cidade: {$cidade['cid_nome']}",
            'cidade'  => $cidade,
            'estados' => $this->estadoModel->findAll(),
        ]);
    }

    public function atualizar(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $nome = trim((string)$this->request->getPost('cid_nome'));
        $url = trim((string)$this->request->getPost('cid_url')) ?: url_title(mb_strtolower($nome), '-', true);

        $this->cidadeModel->update($id, [
            'cid_nome'   => $nome,
            'cid_estado' => (int)$this->request->getPost('cid_estado'),
            'cid_url'    => $url,
        ]);

        return redirect()->to(base_url('admin/cidades'))->with('success', 'Cidade atualizada com sucesso!');
    }

    public function excluir(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $this->cidadeModel->delete($id);
        return redirect()->to(base_url('admin/cidades'))->with('success', 'Cidade excluída com sucesso!');
    }
}
