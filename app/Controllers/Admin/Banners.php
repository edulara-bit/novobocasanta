<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BannerModel;
use App\Models\ParceiroModel;

class Banners extends BaseController
{
    protected BannerModel $bannerModel;
    protected ParceiroModel $parceiroModel;

    public function __construct()
    {
        $this->bannerModel = new BannerModel();
        $this->parceiroModel = new ParceiroModel();
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

        $banners = $this->bannerModel->orderBy('ban_ordem ASC, ban_id DESC')->findAll();

        return view('admin/banners/index', [
            'title'   => 'Gerenciamento de Banners Publicitários (Hero Carrossel)',
            'banners' => $banners,
        ]);
    }

    public function criar()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        return view('admin/banners/form', [
            'title'     => 'Novo Banner Publicitário',
            'banner'    => null,
            'parceiros' => $this->parceiroModel->orderBy('par_nome', 'ASC')->findAll(),
        ]);
    }

    public function salvar()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $titulo = trim((string)$this->request->getPost('ban_titulo'));
        if (empty($titulo)) {
            return redirect()->back()->withInput()->with('error', 'Informe o título principal do banner.');
        }

        $data = [
            'ban_titulo'        => $titulo,
            'ban_badge'         => $this->request->getPost('ban_badge') ?: '⭐ DESTAQUE',
            'ban_descricao'     => $this->request->getPost('ban_descricao'),
            'ban_botao_texto'   => $this->request->getPost('ban_botao_texto') ?: 'Ver Mais',
            'ban_botao_link'    => $this->request->getPost('ban_botao_link') ?: 'anuncie',
            'ban_tipo_fundo'    => $this->request->getPost('ban_tipo_fundo') ?: 'degrade',
            'ban_fundo_cor'     => $this->request->getPost('ban_fundo_cor') ?: '#0f172a',
            'ban_fundo_degrade' => $this->request->getPost('ban_fundo_degrade') ?: 'linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%)',
            'ban_ordem'         => (int)$this->request->getPost('ban_ordem') ?: 1,
            'ban_status'        => $this->request->getPost('ban_status') ?: 'ativo',
            'ban_cliente'       => $this->request->getPost('ban_cliente') ?: 'Boca Santa',
        ];

        $uploadPath = ROOTPATH . 'public/upimg/banners';
        if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

        // Upload de imagem de fundo
        $fundoImg = $this->request->getFile('ban_fundo_imagem');
        if ($fundoImg && $fundoImg->isValid() && !$fundoImg->hasMoved()) {
            $nomeFundo = 'bg_' . time() . '_' . $fundoImg->getRandomName();
            $fundoImg->move($uploadPath, $nomeFundo);
            $data['ban_fundo_imagem'] = 'upimg/banners/' . $nomeFundo;
        }

        // Upload de imagem à direita
        $imgDir = $this->request->getFile('ban_imagem_direita');
        if ($imgDir && $imgDir->isValid() && !$imgDir->hasMoved()) {
            $nomeDir = 'right_' . time() . '_' . $imgDir->getRandomName();
            $imgDir->move($uploadPath, $nomeDir);
            $data['ban_imagem_direita'] = 'upimg/banners/' . $nomeDir;
        }

        $this->bannerModel->insert($data);
        return redirect()->to(base_url('admin/banners'))->with('success', 'Banner criado com sucesso!');
    }

    public function editar(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $banner = $this->bannerModel->find($id);
        if (!$banner) {
            return redirect()->to(base_url('admin/banners'))->with('error', 'Banner não encontrado.');
        }

        return view('admin/banners/form', [
            'title'     => "Editar Banner #{$id}",
            'banner'    => $banner,
            'parceiros' => $this->parceiroModel->orderBy('par_nome', 'ASC')->findAll(),
        ]);
    }

    public function atualizar(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $titulo = trim((string)$this->request->getPost('ban_titulo'));

        $data = [
            'ban_titulo'        => $titulo,
            'ban_badge'         => $this->request->getPost('ban_badge') ?: '⭐ DESTAQUE',
            'ban_descricao'     => $this->request->getPost('ban_descricao'),
            'ban_botao_texto'   => $this->request->getPost('ban_botao_texto') ?: 'Ver Mais',
            'ban_botao_link'    => $this->request->getPost('ban_botao_link') ?: 'anuncie',
            'ban_tipo_fundo'    => $this->request->getPost('ban_tipo_fundo') ?: 'degrade',
            'ban_fundo_cor'     => $this->request->getPost('ban_fundo_cor') ?: '#0f172a',
            'ban_fundo_degrade' => $this->request->getPost('ban_fundo_degrade') ?: 'linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%)',
            'ban_ordem'         => (int)$this->request->getPost('ban_ordem') ?: 1,
            'ban_status'        => $this->request->getPost('ban_status') ?: 'ativo',
            'ban_cliente'       => $this->request->getPost('ban_cliente') ?: 'Boca Santa',
        ];

        $uploadPath = ROOTPATH . 'public/upimg/banners';
        if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

        // Upload de imagem de fundo
        $fundoImg = $this->request->getFile('ban_fundo_imagem');
        if ($fundoImg && $fundoImg->isValid() && !$fundoImg->hasMoved()) {
            $nomeFundo = 'bg_' . time() . '_' . $fundoImg->getRandomName();
            $fundoImg->move($uploadPath, $nomeFundo);
            $data['ban_fundo_imagem'] = 'upimg/banners/' . $nomeFundo;
        }

        // Upload de imagem à direita
        $imgDir = $this->request->getFile('ban_imagem_direita');
        if ($imgDir && $imgDir->isValid() && !$imgDir->hasMoved()) {
            $nomeDir = 'right_' . time() . '_' . $imgDir->getRandomName();
            $imgDir->move($uploadPath, $nomeDir);
            $data['ban_imagem_direita'] = 'upimg/banners/' . $nomeDir;
        }

        $this->bannerModel->update($id, $data);
        return redirect()->to(base_url('admin/banners'))->with('success', 'Banner atualizado com sucesso!');
    }

    public function excluir(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $this->bannerModel->delete($id);
        return redirect()->to(base_url('admin/banners'))->with('success', 'Banner excluído com sucesso!');
    }
}
