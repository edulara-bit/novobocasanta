<?php

declare(strict_types=1);

namespace App\Controllers\Parceiro;

use App\Controllers\BaseController;
use App\Models\CartaoModel;
use App\Models\CategoriaModel;
use App\Models\CidadeModel;
use App\Models\ClienteModel;
use App\Models\EstadoModel;
use App\Models\LancamentoModel;
use App\Models\ParceiroModel;
use App\Models\ProdutoModel;
use App\Models\ResgateModel;
use Config\Database;

class Dashboard extends BaseController
{
    protected ParceiroModel $parceiroModel;
    protected ProdutoModel $produtoModel;
    protected CartaoModel $cartaoModel;
    protected CategoriaModel $categoriaModel;
    protected CidadeModel $cidadeModel;
    protected EstadoModel $estadoModel;
    protected LancamentoModel $lancamentoModel;
    protected ClienteModel $clienteModel;
    protected ResgateModel $resgateModel;

    public function __construct()
    {
        $this->parceiroModel = new ParceiroModel();
        $this->produtoModel = new ProdutoModel();
        $this->cartaoModel = new CartaoModel();
        $this->categoriaModel = new CategoriaModel();
        $this->cidadeModel = new CidadeModel();
        $this->estadoModel = new EstadoModel();
        $this->lancamentoModel = new LancamentoModel();
        $this->clienteModel = new ClienteModel();
        $this->resgateModel = new ResgateModel();
    }

    protected function getParceiroLogado()
    {
        $id = (int)session()->get('parceiro_id');
        if ($id <= 0) {
            return null;
        }
        return $this->parceiroModel->find($id);
    }

    public function index()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) {
            return redirect()->to(base_url('parceiro/login'))->with('error', 'Sessão expirada. Faça login novamente.');
        }

        $ofertas = $this->produtoModel->getOfertas(['parceiro_id' => $parceiro->par_id], 100);
        $cartoes = $this->cartaoModel->where('car_cliente', $parceiro->par_id)->findAll();

        return view('parceiro/dashboard', [
            'title'    => "Painel do Parceiro &bull; {$parceiro->getNome()}",
            'parceiro' => $parceiro,
            'ofertas'  => $ofertas,
            'cartoes'  => $cartoes,
        ]);
    }

    /* --------------------------------------------------------------------------
       MEUS DADOS & EMPRESA
       -------------------------------------------------------------------------- */
    public function perfil()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) return redirect()->to(base_url('parceiro/login'));

        return view('parceiro/perfil', [
            'title'    => 'Meus Dados & Empresa',
            'parceiro' => $parceiro,
            'cidades'  => $this->cidadeModel->getCidades(),
            'estados'  => $this->estadoModel->findAll(),
        ]);
    }

    public function salvarPerfil()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) return redirect()->to(base_url('parceiro/login'));

        $data = [
            'par_nome'        => trim((string)$this->request->getPost('par_nome')),
            'par_razao'       => $this->request->getPost('par_razao'),
            'par_cnpj'        => $this->request->getPost('par_cnpj'),
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
        ];

        if ($this->request->getPost('par_senha')) {
            $data['par_senha'] = md5((string)$this->request->getPost('par_senha'));
        }

        // Upload de logo
        $logo = $this->request->getFile('par_imagem');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $uploadPath = ROOTPATH . 'public/upimg/parceiros';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);
            $nomeLogo = $parceiro->par_id . '_' . $logo->getRandomName();
            $logo->move($uploadPath, $nomeLogo);
            $data['par_imagem'] = $nomeLogo;
        }

        $this->parceiroModel->update($parceiro->par_id, $data);
        return redirect()->to(base_url('parceiro/perfil'))->with('success', 'Dados da empresa atualizados com sucesso!');
    }

    /* --------------------------------------------------------------------------
       OFERTAS DO PARCEIRO
       -------------------------------------------------------------------------- */
    public function ofertas()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) return redirect()->to(base_url('parceiro/login'));

        $ofertas = $this->produtoModel->getOfertas(['parceiro_id' => $parceiro->par_id], 100);

        return view('parceiro/ofertas/index', [
            'title'    => 'Minhas Ofertas & Produtos',
            'parceiro' => $parceiro,
            'ofertas'  => $ofertas,
        ]);
    }

    public function criarOferta()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) return redirect()->to(base_url('parceiro/login'));

        return view('parceiro/ofertas/form', [
            'title'      => 'Cadastrar Nova Oferta',
            'parceiro'   => $parceiro,
            'oferta'     => null,
            'categorias' => $this->categoriaModel->getCategoriasPrincipais(),
            'cartoes'    => $this->cartaoModel->where('car_cliente', $parceiro->par_id)->findAll(),
        ]);
    }

    public function salvarOferta()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) return redirect()->to(base_url('parceiro/login'));

        $titulo = trim((string)$this->request->getPost('pro_titulo'));
        if (empty($titulo)) {
            return redirect()->back()->withInput()->with('error', 'Informe o título da oferta.');
        }

        $data = [
            'pro_titulo'          => $titulo,
            'pro_parceiro'        => $parceiro->par_id,
            'pro_categoria'       => (int)$this->request->getPost('pro_categoria'),
            'pro_cidade'          => (int)$parceiro->par_cidade,
            'pro_preco'           => (float)str_replace(['.', ','], ['', '.'], (string)$this->request->getPost('pro_preco')),
            'pro_precodesconto'   => (float)str_replace(['.', ','], ['', '.'], (string)$this->request->getPost('pro_precodesconto')),
            'pro_apartirde'       => $this->request->getPost('pro_apartirde') ? 1 : 0,
            'pro_destaque'        => $this->request->getPost('pro_destaque') ? 1 : 0,
            'pro_melhor_preco'    => $this->request->getPost('pro_melhor_preco') ? 1 : 0,
            'pro_cartao'          => (int)$this->request->getPost('pro_cartao'),
            'pro_descricao'       => $this->request->getPost('pro_descricao'),
            'pro_caracteristicas' => $this->request->getPost('pro_caracteristicas'),
            'pro_slug'            => url_title(mb_strtolower($titulo), '-', true),
            'pro_data'            => date('Y-m-d H:i:s'),
        ];

        $this->produtoModel->insert($data);
        $novoId = $this->produtoModel->getInsertID();

        // Foto Upload
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $uploadPath = ROOTPATH . 'public/upimg/produtos/' . $parceiro->par_id;
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);
            $nomeFoto = $novoId . '_' . $foto->getRandomName();
            $foto->move($uploadPath, $nomeFoto);

            $db = Database::connect();
            $db->table('tb_fotos_produtos')->insert([
                'fot_produto'  => $novoId,
                'fot_parceiro' => $parceiro->par_id,
                'fot_imagem'   => $nomeFoto,
                'fot_thumb'    => $nomeFoto,
                'fot_ordem'    => 1,
            ]);
        }

        return redirect()->to(base_url('parceiro/ofertas'))->with('success', 'Oferta cadastrada com sucesso!');
    }

    public function editarOferta(int $id)
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) return redirect()->to(base_url('parceiro/login'));

        $oferta = $this->produtoModel->where('pro_id', $id)->where('pro_parceiro', $parceiro->par_id)->first();
        if (!$oferta) {
            return redirect()->to(base_url('parceiro/ofertas'))->with('error', 'Oferta não encontrada.');
        }

        return view('parceiro/ofertas/form', [
            'title'      => "Editar Oferta: {$oferta->pro_titulo}",
            'parceiro'   => $parceiro,
            'oferta'     => $oferta,
            'categorias' => $this->categoriaModel->getCategoriasPrincipais(),
            'cartoes'    => $this->cartaoModel->where('car_cliente', $parceiro->par_id)->findAll(),
        ]);
    }

    public function atualizarOferta(int $id)
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) return redirect()->to(base_url('parceiro/login'));

        $titulo = trim((string)$this->request->getPost('pro_titulo'));

        $data = [
            'pro_titulo'          => $titulo,
            'pro_categoria'       => (int)$this->request->getPost('pro_categoria'),
            'pro_preco'           => (float)str_replace(['.', ','], ['', '.'], (string)$this->request->getPost('pro_preco')),
            'pro_precodesconto'   => (float)str_replace(['.', ','], ['', '.'], (string)$this->request->getPost('pro_precodesconto')),
            'pro_apartirde'       => $this->request->getPost('pro_apartirde') ? 1 : 0,
            'pro_destaque'        => $this->request->getPost('pro_destaque') ? 1 : 0,
            'pro_melhor_preco'    => $this->request->getPost('pro_melhor_preco') ? 1 : 0,
            'pro_cartao'          => (int)$this->request->getPost('pro_cartao'),
            'pro_descricao'       => $this->request->getPost('pro_descricao'),
            'pro_caracteristicas' => $this->request->getPost('pro_caracteristicas'),
            'pro_slug'            => url_title(mb_strtolower($titulo), '-', true),
        ];

        $this->produtoModel->where('pro_id', $id)->where('pro_parceiro', $parceiro->par_id)->set($data)->update();

        // Foto Upload
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $uploadPath = ROOTPATH . 'public/upimg/produtos/' . $parceiro->par_id;
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);
            $nomeFoto = $id . '_' . $foto->getRandomName();
            $foto->move($uploadPath, $nomeFoto);

            $db = Database::connect();
            $db->table('tb_fotos_produtos')->insert([
                'fot_produto'  => $id,
                'fot_parceiro' => $parceiro->par_id,
                'fot_imagem'   => $nomeFoto,
                'fot_thumb'    => $nomeFoto,
                'fot_ordem'    => 1,
            ]);
        }

        return redirect()->to(base_url('parceiro/ofertas'))->with('success', 'Oferta atualizada com sucesso!');
    }

    public function excluirOferta(int $id)
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) return redirect()->to(base_url('parceiro/login'));

        $this->produtoModel->where('pro_id', $id)->where('pro_parceiro', $parceiro->par_id)->delete();
        return redirect()->to(base_url('parceiro/ofertas'))->with('success', 'Oferta excluída com sucesso!');
    }

    /* --------------------------------------------------------------------------
       CARTÕES FIDELIDADE
       -------------------------------------------------------------------------- */
    public function fidelidade()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) return redirect()->to(base_url('parceiro/login'));

        $cartoes = $this->cartaoModel->where('car_cliente', $parceiro->par_id)->findAll();
        $ofertas = $this->produtoModel->where('pro_parceiro', $parceiro->par_id)->findAll();

        return view('parceiro/fidelidade/index', [
            'title'    => 'Meus Cartões Fidelidade',
            'parceiro' => $parceiro,
            'cartoes'  => $cartoes,
            'ofertas'  => $ofertas,
        ]);
    }

    public function criarCartao()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) return redirect()->to(base_url('parceiro/login'));

        return view('parceiro/fidelidade/form', [
            'title'    => 'Criar Novo Cartão Fidelidade',
            'parceiro' => $parceiro,
            'cartao'   => null,
            'ofertas'  => $this->produtoModel->where('pro_parceiro', $parceiro->par_id)->findAll(),
        ]);
    }

    public function salvarCartao()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) return redirect()->to(base_url('parceiro/login'));

        $nome = trim((string)$this->request->getPost('car_nome'));
        if (empty($nome)) {
            return redirect()->back()->withInput()->with('error', 'Informe o nome da campanha do cartão fidelidade.');
        }

        $this->cartaoModel->insert([
            'car_cliente'     => $parceiro->par_id,
            'car_nome'        => $nome,
            'car_pontos'      => (int)$this->request->getPost('car_pontos') ?: 10,
            'car_validade'    => (int)$this->request->getPost('car_validade') ?: 90,
            'car_oferta'      => (int)$this->request->getPost('car_oferta') ?: 0,
            'car_regras'      => $this->request->getPost('car_regras'),
            'car_percentagem' => (float)$this->request->getPost('car_percentagem') ?: 0,
            'car_data'        => date('Y-m-d'),
        ]);

        return redirect()->to(base_url('parceiro/fidelidade'))->with('success', 'Cartão Fidelidade criado com sucesso!');
    }

    public function editarCartao(int $id)
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) return redirect()->to(base_url('parceiro/login'));

        $cartao = $this->cartaoModel->where('car_id', $id)->where('car_cliente', $parceiro->par_id)->first();
        if (!$cartao) {
            return redirect()->to(base_url('parceiro/fidelidade'))->with('error', 'Cartão não encontrado.');
        }

        return view('parceiro/fidelidade/form', [
            'title'    => "Editar Cartão: {$cartao['car_nome']}",
            'parceiro' => $parceiro,
            'cartao'   => $cartao,
            'ofertas'  => $this->produtoModel->where('pro_parceiro', $parceiro->par_id)->findAll(),
        ]);
    }

    public function atualizarCartao(int $id)
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) return redirect()->to(base_url('parceiro/login'));

        $this->cartaoModel->where('car_id', $id)->where('car_cliente', $parceiro->par_id)->set([
            'car_nome'        => trim((string)$this->request->getPost('car_nome')),
            'car_pontos'      => (int)$this->request->getPost('car_pontos'),
            'car_validade'    => (int)$this->request->getPost('car_validade'),
            'car_oferta'      => (int)$this->request->getPost('car_oferta') ?: 0,
            'car_regras'      => $this->request->getPost('car_regras'),
            'car_percentagem' => (float)$this->request->getPost('car_percentagem'),
        ])->update();

        return redirect()->to(base_url('parceiro/fidelidade'))->with('success', 'Cartão Fidelidade atualizado com sucesso!');
    }

    public function excluirCartao(int $id)
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) return redirect()->to(base_url('parceiro/login'));

        $this->cartaoModel->where('car_id', $id)->where('car_cliente', $parceiro->par_id)->delete();
        return redirect()->to(base_url('parceiro/fidelidade'))->with('success', 'Cartão Fidelidade excluído com sucesso!');
    }

    // ==========================================
    // APONTAMENTO DE COMPRAS E PONTOS / FIDELIDADE
    // ==========================================

    public function lancamentos()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) return redirect()->to(base_url('parceiro/login'));

        $cartoes = $this->cartaoModel->where('car_cliente', $parceiro->par_id)->findAll();
        $ofertas = $this->produtoModel->where('pro_parceiro', $parceiro->par_id)->findAll();

        $db = Database::connect();
        $ultimosLancamentos = $db->table('tb_lancamentos as l')
            ->select('l.*, c.car_nome, p.pro_titulo, cli.cli_nome, cli.cli_celular')
            ->join('tb_cartoes as c', 'c.car_id = l.lan_cartao', 'left')
            ->join('tb_produtos as p', 'p.pro_id = l.lan_oferta', 'left')
            ->join('tb_clientes as cli', 'cli.cli_cpf = l.lan_cpf', 'left')
            ->where('l.lan_parceiro', $parceiro->par_id)
            ->orderBy('l.lan_id', 'DESC')
            ->limit(50)
            ->get()
            ->getResultArray();

        $cpfBusca = preg_replace('/\D/', '', (string)$this->request->getGet('cpf'));
        $clienteEncontrado = null;
        $saldoCartao = 0;
        $totalCompras = 0;

        if (!empty($cpfBusca)) {
            $clienteEncontrado = $this->clienteModel->where('cli_cpf', $cpfBusca)->first();
            if ($clienteEncontrado && !empty($cartoes)) {
                $totalCompras = (float)$this->lancamentoModel
                    ->where('lan_cpf', $cpfBusca)
                    ->where('lan_parceiro', $parceiro->par_id)
                    ->selectSum('lan_valor')
                    ->first()['lan_valor'] ?? 0;

                $numLancamentos = $this->lancamentoModel
                    ->where('lan_cpf', $cpfBusca)
                    ->where('lan_parceiro', $parceiro->par_id)
                    ->countAllResults();

                $saldoCartao = $numLancamentos;
            }
        }

        return view('parceiro/fidelidade/lancamentos', [
            'title'              => 'Lançamento de Compras & Pontos',
            'parceiro'           => $parceiro,
            'cartoes'            => $cartoes,
            'ofertas'            => $ofertas,
            'ultimosLancamentos' => $ultimosLancamentos,
            'clienteEncontrado'  => $clienteEncontrado,
            'cpfBusca'           => $cpfBusca,
            'saldoCartao'        => $saldoCartao,
            'totalCompras'       => $totalCompras,
        ]);
    }

    public function salvarLancamento()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) return redirect()->to(base_url('parceiro/login'));

        $cpf = preg_replace('/\D/', '', (string)$this->request->getPost('cli_cpf'));
        if (empty($cpf) || strlen($cpf) < 11) {
            return redirect()->back()->withInput()->with('error', 'Informe um CPF válido (11 dígitos).');
        }

        $cartaoId = (int)$this->request->getPost('lan_cartao');
        if ($cartaoId <= 0) {
            return redirect()->back()->withInput()->with('error', 'Selecione o cartão fidelidade para pontuar.');
        }

        $nome = trim((string)$this->request->getPost('cli_nome'));
        $celular = trim((string)$this->request->getPost('cli_celular'));
        $email = trim((string)$this->request->getPost('cli_email'));

        // Se cliente não existe, cadastra
        $cliente = $this->clienteModel->where('cli_cpf', $cpf)->first();
        if (!$cliente) {
            if (empty($nome)) {
                return redirect()->back()->withInput()->with('error', 'Cliente novo: informe o nome do cliente.');
            }
            $this->clienteModel->insert([
                'cli_cpf'      => $cpf,
                'cli_nome'     => $nome,
                'cli_celular'  => $celular,
                'cli_email'    => $email,
                'cli_parceiro' => $parceiro->par_id,
                'cli_data'     => date('Y-m-d'),
            ]);
        } else if (!empty($nome) && empty($cliente['cli_nome'])) {
            $this->clienteModel->where('cli_id', $cliente['cli_id'])->set(['cli_nome' => $nome])->update();
        }

        $valorStr = (string)$this->request->getPost('lan_valor');
        $valor = (float)str_replace(['.', ','], ['', '.'], $valorStr);

        $dataLancamento = [
            'lan_cpf'       => $cpf,
            'lan_cartao'    => $cartaoId,
            'lan_parceiro'  => $parceiro->par_id,
            'lan_valor'     => $valor,
            'lan_nf'        => trim((string)$this->request->getPost('lan_nf')),
            'lan_oferta'    => (int)$this->request->getPost('lan_oferta') ?: 0,
            'lan_descricao' => trim((string)$this->request->getPost('lan_descricao')),
            'lan_data'      => date('Y-m-d H:i:s'),
            'lan_bocapoint' => 0,
        ];

        $this->lancamentoModel->insert($dataLancamento);

        return redirect()->to(base_url('parceiro/fidelidade/lancamentos?cpf=' . $cpf))->with('success', 'Pontos e compra registrados com sucesso para o cliente!');
    }

    public function excluirLancamento(int $id)
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) return redirect()->to(base_url('parceiro/login'));

        $this->lancamentoModel->where('lan_id', $id)->where('lan_parceiro', $parceiro->par_id)->delete();
        return redirect()->to(base_url('parceiro/fidelidade/lancamentos'))->with('success', 'Lançamento removido com sucesso!');
    }

    public function validarResgate()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) return redirect()->to(base_url('parceiro/login'));

        $codigo = trim((string)$this->request->getPost('res_codigo'));
        if (empty($codigo)) {
            return redirect()->back()->with('error', 'Informe o código do cupom/voucher de resgate.');
        }

        $db = Database::connect();
        $resgate = $db->table('tb_resgate as r')
            ->select('r.*, cli.cli_nome, cli.cli_cpf, pr.pre_nome, c.car_nome')
            ->join('tb_clientes as cli', 'cli.cli_id = r.res_usuario', 'left')
            ->join('tb_premios as pr', 'pr.pre_id = r.res_premio', 'left')
            ->join('tb_cartoes as c', 'c.car_id = r.res_cartao', 'left')
            ->where('r.res_codigo', $codigo)
            ->where('r.res_parceiro', $parceiro->par_id)
            ->get()
            ->getRowArray();

        if (!$resgate) {
            return redirect()->back()->with('error', 'Código de resgate não encontrado ou não pertence a este estabelecimento.');
        }

        if (($resgate['res_retirado'] ?? '') === 'sim') {
            $dataUso = date('d/m/Y H:i', strtotime((string)$resgate['res_retirado_data']));
            return redirect()->back()->with('error', "Este código já foi resgatado anteriormente em: {$dataUso}");
        }

        $this->resgateModel->where('res_id', $resgate['res_id'])->set([
            'res_retirado'      => 'sim',
            'res_retirado_data' => date('Y-m-d H:i:s'),
        ])->update();

        $clienteNome = $resgate['cli_nome'] ?: 'Cliente';
        $premioNome = $resgate['pre_nome'] ?: 'Recompensa do Cartão';
        return redirect()->to(base_url('parceiro/fidelidade/lancamentos'))->with('success', "Resgate confirmado com sucesso para {$clienteNome}! Prêmio: {$premioNome}.");
    }

    public function buscarClienteAjax()
    {
        $parceiro = $this->getParceiroLogado();
        if (!$parceiro) return $this->response->setJSON(['status' => 'error', 'message' => 'Não autorizado']);

        $cpf = preg_replace('/\D/', '', (string)$this->request->getGet('cpf'));
        if (strlen($cpf) < 11) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'CPF incompleto']);
        }

        $cliente = $this->clienteModel->where('cli_cpf', $cpf)->first();
        if (!$cliente) {
            return $this->response->setJSON(['status' => 'not_found', 'message' => 'Cliente novo']);
        }

        $numLancamentos = $this->lancamentoModel
            ->where('lan_cpf', $cpf)
            ->where('lan_parceiro', $parceiro->par_id)
            ->countAllResults();

        $totalGasto = (float)$this->lancamentoModel
            ->where('lan_cpf', $cpf)
            ->where('lan_parceiro', $parceiro->par_id)
            ->selectSum('lan_valor')
            ->first()['lan_valor'] ?? 0;

        return $this->response->setJSON([
            'status'     => 'found',
            'cliente'    => [
                'nome'    => $cliente['cli_nome'],
                'celular' => $cliente['cli_celular'],
                'email'   => $cliente['cli_email'],
                'cpf'     => $cliente['cli_cpf'],
            ],
            'pontos'     => $numLancamentos,
            'totalGasto' => number_format($totalGasto, 2, ',', '.'),
        ]);
    }
}

