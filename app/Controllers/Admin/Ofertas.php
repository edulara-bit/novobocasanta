<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoriaModel;
use App\Models\CidadeModel;
use App\Models\ParceiroModel;
use App\Models\ProdutoModel;
use Config\Database;

class Ofertas extends BaseController
{
    protected ProdutoModel $produtoModel;
    protected ParceiroModel $parceiroModel;
    protected CategoriaModel $categoriaModel;
    protected CidadeModel $cidadeModel;

    public function __construct()
    {
        $this->produtoModel = new ProdutoModel();
        $this->parceiroModel = new ParceiroModel();
        $this->categoriaModel = new CategoriaModel();
        $this->cidadeModel = new CidadeModel();
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

        $parceiroId = (int)$this->request->getGet('parceiro');
        $params = [];
        if ($parceiroId > 0) {
            $params['parceiro_id'] = $parceiroId;
        }

        $ofertas = $this->produtoModel->getOfertas($params, 200);
        $parceiroFiltro = $parceiroId > 0 ? $this->parceiroModel->find($parceiroId) : null;

        return view('admin/ofertas/index', [
            'title'          => 'Gerenciamento de Ofertas & Produtos',
            'ofertas'        => $ofertas,
            'parceiroFiltro' => $parceiroFiltro,
        ]);
    }

    public function criar()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        return view('admin/ofertas/form', [
            'title'         => 'Cadastrar Nova Oferta',
            'oferta'        => null,
            'parceiros'     => $this->parceiroModel->orderBy('par_nome', 'ASC')->findAll(),
            'categorias'    => $this->categoriaModel->getCategoriasPrincipais(),
            'subcategorias' => [],
            'catPaiId'      => 0,
            'subCatId'      => 0,
            'cidades'       => $this->cidadeModel->getCidades(),
        ]);
    }

    public function salvar()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $titulo = trim((string)$this->request->getPost('pro_titulo'));
        $parceiroId = (int)$this->request->getPost('pro_parceiro');

        if (empty($titulo) || $parceiroId <= 0) {
            return redirect()->back()->withInput()->with('error', 'Informe o título da oferta e selecione o parceiro.');
        }

        $catPaiId = (int)$this->request->getPost('pro_categoria_pai');
        $subCatId = (int)$this->request->getPost('pro_categoria');

        if ($subCatId > 0) {
            $proCategoria = $subCatId;
            $proCategoriaPai = $catPaiId > 0 ? $catPaiId : (int)($this->categoriaModel->find($subCatId)->cat_categoria_pai ?? 0);
        } else {
            $proCategoria = $catPaiId;
            $proCategoriaPai = $catPaiId;
        }

        $cidadeId = (int)$this->request->getPost('pro_cidade');
        $slug = url_title(mb_strtolower($titulo), '-', true);
        $catObj = $this->categoriaModel->find($proCategoria);
        $catUrl = $catObj ? $catObj->cat_url : 'ofertas';
        $cidObj = $this->cidadeModel->find($cidadeId);
        $cidUrl = $cidObj ? $cidObj['cid_url'] : 'piracicaba';
        $proSlug2 = "{$cidUrl}/{$catUrl}/{$slug}";

        $data = [
            'pro_titulo'          => $titulo,
            'pro_parceiro'        => $parceiroId,
            'pro_categoria'       => $proCategoria,
            'pro_categoria_pai'   => $proCategoriaPai,
            'pro_cidade'          => $cidadeId,
            'pro_preco'           => (float)str_replace(['.', ','], ['', '.'], (string)$this->request->getPost('pro_preco')),
            'pro_precodesconto'   => (float)str_replace(['.', ','], ['', '.'], (string)$this->request->getPost('pro_precodesconto')),
            'pro_apartirde'       => $this->request->getPost('pro_apartirde') ? 1 : 0,
            'pro_destaque'        => $this->request->getPost('pro_destaque') ? 1 : 0,
            'pro_melhor_preco'    => $this->request->getPost('pro_melhor_preco') ? 1 : 0,
            'pro_descricao'       => $this->request->getPost('pro_descricao'),
            'pro_caracteristicas' => $this->request->getPost('pro_caracteristicas'),
            'pro_slug'            => $slug,
            'pro_slug2'           => $proSlug2,
            'pro_data'            => date('Y-m-d H:i:s'),
        ];

        $this->produtoModel->insert($data);
        $novoId = $this->produtoModel->getInsertID();

        // Upload de foto se enviada
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $uploadPath = ROOTPATH . 'public/upimg/produtos/' . $parceiroId;
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $novoNome = $novoId . '_' . $foto->getRandomName();
            $foto->move($uploadPath, $novoNome);

            $db = Database::connect();
            $db->table('tb_fotos_produtos')->insert([
                'fot_produto'  => $novoId,
                'fot_parceiro' => $parceiroId,
                'fot_imagem'   => $novoNome,
                'fot_thumb'    => $novoNome,
                'fot_ordem'    => 1,
            ]);
        }

        return redirect()->to(base_url('admin/ofertas'))->with('success', 'Oferta cadastrada com sucesso!');
    }

    public function editar(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $oferta = $this->produtoModel->find($id);
        if (!$oferta) {
            return redirect()->to(base_url('admin/ofertas'))->with('error', 'Oferta não encontrada.');
        }

        $catPaiId = (int)($oferta->pro_categoria_pai ?? 0);
        $subCatId = (int)($oferta->pro_categoria ?? 0);

        if ($catPaiId === 0 && $subCatId > 0) {
            $catObj = $this->categoriaModel->find($subCatId);
            if ($catObj && (int)$catObj->cat_categoria_pai > 0) {
                $catPaiId = (int)$catObj->cat_categoria_pai;
            } elseif ($catObj) {
                $catPaiId = (int)$catObj->cat_id;
                $subCatId = 0;
            }
        }

        $subcategorias = $catPaiId > 0 ? $this->categoriaModel->getSubcategorias($catPaiId) : [];

        return view('admin/ofertas/form', [
            'title'         => "Editar Oferta #{$id}",
            'oferta'        => $oferta,
            'parceiros'     => $this->parceiroModel->orderBy('par_nome', 'ASC')->findAll(),
            'categorias'    => $this->categoriaModel->getCategoriasPrincipais(),
            'subcategorias' => $subcategorias,
            'catPaiId'      => $catPaiId,
            'subCatId'      => $subCatId,
            'cidades'       => $this->cidadeModel->getCidades(),
        ]);
    }

    public function atualizar(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $titulo = trim((string)$this->request->getPost('pro_titulo'));
        $parceiroId = (int)$this->request->getPost('pro_parceiro');

        $catPaiId = (int)$this->request->getPost('pro_categoria_pai');
        $subCatId = (int)$this->request->getPost('pro_categoria');

        if ($subCatId > 0) {
            $proCategoria = $subCatId;
            $proCategoriaPai = $catPaiId > 0 ? $catPaiId : (int)($this->categoriaModel->find($subCatId)->cat_categoria_pai ?? 0);
        } else {
            $proCategoria = $catPaiId;
            $proCategoriaPai = $catPaiId;
        }

        $cidadeId = (int)$this->request->getPost('pro_cidade');
        $slug = url_title(mb_strtolower($titulo), '-', true);
        $catObj = $this->categoriaModel->find($proCategoria);
        $catUrl = $catObj ? $catObj->cat_url : 'ofertas';
        $cidObj = $this->cidadeModel->find($cidadeId);
        $cidUrl = $cidObj ? $cidObj['cid_url'] : 'piracicaba';
        $proSlug2 = "{$cidUrl}/{$catUrl}/{$slug}";

        $data = [
            'pro_titulo'          => $titulo,
            'pro_parceiro'        => $parceiroId,
            'pro_categoria'       => $proCategoria,
            'pro_categoria_pai'   => $proCategoriaPai,
            'pro_cidade'          => $cidadeId,
            'pro_preco'           => (float)str_replace(['.', ','], ['', '.'], (string)$this->request->getPost('pro_preco')),
            'pro_precodesconto'   => (float)str_replace(['.', ','], ['', '.'], (string)$this->request->getPost('pro_precodesconto')),
            'pro_apartirde'       => $this->request->getPost('pro_apartirde') ? 1 : 0,
            'pro_destaque'        => $this->request->getPost('pro_destaque') ? 1 : 0,
            'pro_melhor_preco'    => $this->request->getPost('pro_melhor_preco') ? 1 : 0,
            'pro_descricao'       => $this->request->getPost('pro_descricao'),
            'pro_caracteristicas' => $this->request->getPost('pro_caracteristicas'),
            'pro_slug'            => $slug,
            'pro_slug2'           => $proSlug2,
        ];

        $this->produtoModel->update($id, $data);

        // Upload de foto se enviada
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $uploadPath = ROOTPATH . 'public/upimg/produtos/' . $parceiroId;
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $novoNome = $id . '_' . $foto->getRandomName();
            $foto->move($uploadPath, $novoNome);

            $db = Database::connect();
            $db->table('tb_fotos_produtos')->insert([
                'fot_produto'  => $id,
                'fot_parceiro' => $parceiroId,
                'fot_imagem'   => $novoNome,
                'fot_thumb'    => $novoNome,
                'fot_ordem'    => 1,
            ]);
        }

        return redirect()->to(base_url('admin/ofertas'))->with('success', 'Oferta atualizada com sucesso!');
    }

    public function excluir(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $this->produtoModel->delete($id);
        $db = Database::connect();
        $db->table('tb_fotos_produtos')->where('fot_produto', $id)->delete();

        return redirect()->to(base_url('admin/ofertas'))->with('success', 'Oferta excluída com sucesso!');
    }
}
