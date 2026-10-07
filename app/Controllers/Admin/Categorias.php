<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoriaModel;

class Categorias extends BaseController
{
    protected CategoriaModel $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new CategoriaModel();
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

        $categorias = $this->categoriaModel->orderBy('cat_categoria_pai ASC, cat_titulo ASC')->findAll();

        return view('admin/categorias/index', [
            'title'      => 'Gerenciamento de Categorias',
            'categorias' => $categorias,
        ]);
    }

    public function criar()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        return view('admin/categorias/form', [
            'title'      => 'Nova Categoria',
            'categoria'  => null,
            'principais' => $this->categoriaModel->getCategoriasPrincipais(),
        ]);
    }

    public function salvar()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $titulo = trim((string)$this->request->getPost('cat_titulo'));
        if (empty($titulo)) {
            return redirect()->back()->withInput()->with('error', 'Informe o título da categoria.');
        }

        $this->categoriaModel->insert([
            'cat_titulo'        => $titulo,
            'cat_categoria_pai' => (int)$this->request->getPost('cat_categoria_pai'),
            'cat_classe'        => $this->request->getPost('cat_classe') ?: 'fa-tag',
            'cat_url'           => url_title(mb_strtolower($titulo), '-', true),
        ]);

        return redirect()->to(base_url('admin/categorias'))->with('success', 'Categoria criada com sucesso!');
    }

    public function editar(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $categoria = $this->categoriaModel->find($id);
        if (!$categoria) {
            return redirect()->to(base_url('admin/categorias'))->with('error', 'Categoria não encontrada.');
        }

        return view('admin/categorias/form', [
            'title'      => "Editar Categoria: {$categoria->cat_titulo}",
            'categoria'  => $categoria,
            'principais' => $this->categoriaModel->getCategoriasPrincipais(),
        ]);
    }

    public function atualizar(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $titulo = trim((string)$this->request->getPost('cat_titulo'));

        $this->categoriaModel->update($id, [
            'cat_titulo'        => $titulo,
            'cat_categoria_pai' => (int)$this->request->getPost('cat_categoria_pai'),
            'cat_classe'        => $this->request->getPost('cat_classe'),
            'cat_url'           => url_title(mb_strtolower($titulo), '-', true),
        ]);

        return redirect()->to(base_url('admin/categorias'))->with('success', 'Categoria atualizada com sucesso!');
    }

    public function excluir(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $this->categoriaModel->delete($id);
        return redirect()->to(base_url('admin/categorias'))->with('success', 'Categoria excluída com sucesso!');
    }
}
