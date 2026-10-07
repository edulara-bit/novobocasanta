<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CidadeModel;
use App\Models\EstadoModel;
use App\Models\ParceiroModel;
use App\Models\ProdutoModel;
use Config\Database;

class Parceiros extends BaseController
{
    protected ParceiroModel $parceiroModel;
    protected CidadeModel $cidadeModel;
    protected EstadoModel $estadoModel;

    public function __construct()
    {
        $this->parceiroModel = new ParceiroModel();
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

        $parceiros = $this->parceiroModel->getParceiros([], 500);

        return view('admin/parceiros/index', [
            'title'     => 'Gerenciamento de Parceiros Comerciais',
            'parceiros' => $parceiros,
        ]);
    }

    public function criar()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        return view('admin/parceiros/form', [
            'title'    => 'Cadastrar Novo Parceiro',
            'parceiro' => null,
            'cidades'  => $this->cidadeModel->getCidades(),
            'estados'  => $this->estadoModel->findAll(),
        ]);
    }

    public function salvar()
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $nome = trim((string)$this->request->getPost('par_nome'));
        $email = trim((string)$this->request->getPost('par_email'));

        if (empty($nome)) {
            return redirect()->back()->withInput()->with('error', 'Informe o nome do parceiro.');
        }

        $data = [
            'par_nome'        => $nome,
            'par_razao'       => $this->request->getPost('par_razao'),
            'par_apelido'     => $this->request->getPost('par_apelido') ?: url_title(mb_strtolower($nome), '-', true),
            'par_cidade'      => (int)$this->request->getPost('par_cidade'),
            'par_estado'      => (int)$this->request->getPost('par_estado'),
            'par_endereco'    => $this->request->getPost('par_endereco'),
            'par_numero'      => $this->request->getPost('par_numero'),
            'par_complemento' => $this->request->getPost('par_complemento'),
            'par_bairro'      => $this->request->getPost('par_bairro'),
            'par_cep'         => $this->request->getPost('par_cep'),
            'par_telefone'    => $this->request->getPost('par_telefone'),
            'par_telefone2'   => $this->request->getPost('par_telefone2'),
            'par_whatsapp'    => $this->request->getPost('par_whatsapp'),
            'par_email'       => $email,
            'par_site'        => $this->request->getPost('par_site'),
            'par_facebook'    => $this->request->getPost('par_facebook'),
            'par_instagram'   => $this->request->getPost('par_instagram'),
            'par_descricao'   => $this->request->getPost('par_descricao'),
            'par_ativo'       => $this->request->getPost('par_ativo') ? '1' : '0',
            'par_senha'       => md5($this->request->getPost('par_senha') ?: '123456'),
            'par_datacadastro'=> date('Y-m-d'),
        ];

        $this->parceiroModel->insert($data);
        $novoId = $this->parceiroModel->getInsertID();

        // Logo Upload
        $logo = $this->request->getFile('par_imagem');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $uploadPath = ROOTPATH . 'public/upimg/parceiros';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);
            $nomeLogo = $novoId . '_' . $logo->getRandomName();
            $logo->move($uploadPath, $nomeLogo);
            $this->parceiroModel->update($novoId, ['par_imagem' => $nomeLogo]);
        }

        return redirect()->to(base_url('admin/parceiros'))->with('success', 'Parceiro cadastrado com sucesso!');
    }

    public function editar(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $parceiro = $this->parceiroModel->find($id);
        if (!$parceiro) {
            return redirect()->to(base_url('admin/parceiros'))->with('error', 'Parceiro não encontrado.');
        }

        return view('admin/parceiros/form', [
            'title'    => "Editar Parceiro: {$parceiro->getNome()}",
            'parceiro' => $parceiro,
            'cidades'  => $this->cidadeModel->getCidades(),
            'estados'  => $this->estadoModel->findAll(),
        ]);
    }

    public function atualizar(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $nome = trim((string)$this->request->getPost('par_nome'));

        $data = [
            'par_nome'        => $nome,
            'par_razao'       => $this->request->getPost('par_razao'),
            'par_apelido'     => $this->request->getPost('par_apelido'),
            'par_cidade'      => (int)$this->request->getPost('par_cidade'),
            'par_estado'      => (int)$this->request->getPost('par_estado'),
            'par_endereco'    => $this->request->getPost('par_endereco'),
            'par_numero'      => $this->request->getPost('par_numero'),
            'par_complemento' => $this->request->getPost('par_complemento'),
            'par_bairro'      => $this->request->getPost('par_bairro'),
            'par_cep'         => $this->request->getPost('par_cep'),
            'par_telefone'    => $this->request->getPost('par_telefone'),
            'par_telefone2'   => $this->request->getPost('par_telefone2'),
            'par_whatsapp'    => $this->request->getPost('par_whatsapp'),
            'par_email'       => $this->request->getPost('par_email'),
            'par_site'        => $this->request->getPost('par_site'),
            'par_facebook'    => $this->request->getPost('par_facebook'),
            'par_instagram'   => $this->request->getPost('par_instagram'),
            'par_descricao'   => $this->request->getPost('par_descricao'),
            'par_ativo'       => $this->request->getPost('par_ativo') ? '1' : '0',
            'par_alteracao'   => date('Y-m-d'),
        ];

        if ($this->request->getPost('par_senha')) {
            $data['par_senha'] = md5((string)$this->request->getPost('par_senha'));
        }

        // Logo Upload
        $logo = $this->request->getFile('par_imagem');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $uploadPath = ROOTPATH . 'public/upimg/parceiros';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);
            $nomeLogo = $id . '_' . $logo->getRandomName();
            $logo->move($uploadPath, $nomeLogo);
            $data['par_imagem'] = $nomeLogo;
        }

        $this->parceiroModel->update($id, $data);

        return redirect()->to(base_url('admin/parceiros'))->with('success', 'Dados do parceiro atualizados com sucesso!');
    }

    public function excluir(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $this->parceiroModel->delete($id);
        return redirect()->to(base_url('admin/parceiros'))->with('success', 'Parceiro excluído com sucesso!');
    }

    /**
     * Item 10: Logar na área do parceiro como se fosse ele
     */
    public function loginAs(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $parceiro = $this->parceiroModel->find($id);
        if (!$parceiro) {
            return redirect()->to(base_url('admin/parceiros'))->with('error', 'Parceiro não encontrado.');
        }

        session()->set([
            'parceiro_id'        => $parceiro->par_id,
            'parceiro_nome'      => $parceiro->getNome(),
            'parceiro_email'     => $parceiro->par_email,
            'parceiro_logado'    => true,
            'impersonated_by_adm'=> session()->get('admin_nome'),
        ]);

        return redirect()->to(base_url('parceiro/dashboard'))
            ->with('success', "Você está logado na área restrita como o parceiro: {$parceiro->getNome()}");
    }

    /**
     * Item 10: Zerar contador de acessos do parceiro e seus produtos
     */
    public function zerarAcessos(int $id)
    {
        if ($redirect = $this->checkAuth()) return $redirect;

        $db = Database::connect();
        $db->table('tb_parceiros')->where('par_id', $id)->update([
            'par_acesso'   => 0,
            'par_viewfone' => 0,
        ]);
        $db->table('tb_produtos')->where('pro_parceiro', $id)->update([
            'pro_visitas' => 0,
        ]);

        return redirect()->to(base_url('admin/parceiros'))->with('success', 'Contadores de acessos e visualizações do parceiro foram zerados com sucesso!');
    }
}
