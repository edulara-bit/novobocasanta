<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CidadeModel;
use App\Services\LgpdService;
use App\Services\SeoService;

class Lgpd extends BaseController
{
    protected LgpdService $lgpdService;
    protected CidadeModel $cidadeModel;
    protected SeoService $seoService;

    public function __construct()
    {
        $this->lgpdService = new LgpdService();
        $this->cidadeModel = new CidadeModel();
        $this->seoService = new SeoService();
    }

    public function politica()
    {
        $cidade = $this->cidadeModel->first() ?? ['cid_id' => 1, 'cid_nome' => 'Piracicaba', 'cid_url' => 'piracicaba'];
        $todasCidades = $this->cidadeModel->getCidades();
        
        $conteudoModel = new \App\Models\ConteudoModel();
        $slug = uri_string() === 'termos' ? 'termos-de-uso' : 'politica-privacidade';
        $pagina = $conteudoModel->where('con_slug', $slug)->first();

        $seo = $this->seoService->generateMeta([
            'title' => $pagina['con_titulo'] ?? 'Política de Privacidade e Proteção de Dados (LGPD)',
            'description' => $pagina['con_description'] ?? 'Privacidade e conformidade com a LGPD no Boca Santa Ofertas.',
        ]);

        return view('lgpd/politica_privacidade', [
            'cidadeAtual'  => $cidade,
            'todasCidades' => $todasCidades,
            'seo'          => $seo,
            'pagina'       => $pagina,
        ]);
    }

    public function solicitarDados()
    {
        $cidade = $this->cidadeModel->first() ?? ['cid_id' => 1, 'cid_nome' => 'Piracicaba', 'cid_url' => 'piracicaba'];
        $todasCidades = $this->cidadeModel->getCidades();
        $seo = $this->seoService->generateMeta([
            'title' => 'Canal do Titular de Dados - LGPD',
        ]);

        return view('lgpd/solicitar_dados', [
            'cidadeAtual'  => $cidade,
            'todasCidades' => $todasCidades,
            'seo'          => $seo,
        ]);
    }

    public function processarSolicitacao()
    {
        $rules = [
            'tipo'  => 'required|in_list[exportar_dados,excluir_dados,revogar_consentimento,correcao_dados,outros]',
            'nome'  => 'required|min_length[3]|max_length[150]',
            'email' => 'required|valid_email|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Por favor, preencha todos os campos obrigatórios corretamente.');
        }

        $id = $this->lgpdService->createDataSubjectRequest([
            'tipo'     => $this->request->getPost('tipo'),
            'nome'     => $this->request->getPost('nome'),
            'email'    => $this->request->getPost('email'),
            'mensagem' => $this->request->getPost('mensagem'),
        ]);

        return redirect()->to(base_url('lgpd/solicitar-dados'))->with('success', "Sua solicitação (Protocolo #{$id}) foi registrada com sucesso! Nosso Encarregado pelo Tratamento de Dados (DPO) responderá em até 15 dias úteis.");
    }

    public function salvarConsentimento()
    {
        $json = $this->request->getJSON(true) ?? [];
        $visitorId = $this->request->getCookie('bocasanta_visitor_id') ?? md5($this->request->getIPAddress() . session_id());
        $ip = $this->request->getIPAddress();
        $ua = $this->request->getUserAgent()->getAgentString();

        $this->lgpdService->saveConsent($visitorId, $json, session()->get('usuario_id'), $ip, $ua);

        return $this->response->setJSON(['status' => 'success', 'saved' => true]);
    }
}
