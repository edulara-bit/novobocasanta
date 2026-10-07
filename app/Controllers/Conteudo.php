<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CategoriaModel;
use App\Models\CidadeModel;
use App\Services\SeoService;
use Config\Database;

class Conteudo extends BaseController
{
    protected CidadeModel $cidadeModel;
    protected CategoriaModel $categoriaModel;
    protected SeoService $seoService;

    public function __construct()
    {
        $this->cidadeModel = new CidadeModel();
        $this->categoriaModel = new CategoriaModel();
        $this->seoService = new SeoService();
    }

    public function anuncie()
    {
        $cidade = $this->cidadeModel->first() ?? ['cid_id' => 1, 'cid_nome' => 'Piracicaba', 'cid_url' => 'piracicaba'];
        $todasCidades = $this->cidadeModel->getCidades();
        $principaisCidades = $this->cidadeModel->getPrincipaisCidades(4);
        $categoriasPrincipais = $this->categoriaModel->getCategoriasPrincipais();

        $seo = $this->seoService->generateMeta([
            'title' => 'Anuncie no Boca Santa Ofertas - Divulgue sua Empresa e Venda Mais',
            'description' => 'Cadastre seu comércio, anuncie ofertas no Google e integre seu estoque direto com o Linefast.',
        ]);

        return view('conteudo/anuncie', [
            'cidadeAtual'          => $cidade,
            'todasCidades'         => $todasCidades,
            'principaisCidades'    => $principaisCidades,
            'categoriasBar'        => array_slice($categoriasPrincipais, 0, 8),
            'seo'                  => $seo,
        ]);
    }

    public function enviarProposta()
    {
        $rules = [
            'nome'     => 'required|min_length[3]',
            'empresa'  => 'required|min_length[2]',
            'email'    => 'required|valid_email',
            'telefone' => 'required',
            'cidade'   => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Por favor, preencha todos os campos obrigatórios.');
        }

        $db = Database::connect();
        $db->table('tb_mensagens')->insert([
            'men_nome'     => $this->request->getPost('nome'),
            'men_email'    => $this->request->getPost('email'),
            'men_telefone' => $this->request->getPost('telefone'),
            'men_cidade'   => $this->request->getPost('cidade'),
            'men_mensagem' => 'Interesse em Anunciar / Empresa: ' . $this->request->getPost('empresa') . ' | Plano: ' . ($this->request->getPost('plano') ?? 'Padrão'),
            'men_data'     => date('Y-m-d'),
        ]);

        return redirect()->to(base_url('anuncie'))->with('success', 'Obrigado pelo interesse! Nossa equipe comercial entrará em contato em breve.');
    }
}
