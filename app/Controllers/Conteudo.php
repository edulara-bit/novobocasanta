<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CategoriaModel;
use App\Models\CidadeModel;
use App\Services\EmailService;
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
            'nome'     => 'required|min_length[3]|max_length[200]',
            'empresa'  => 'required|min_length[2]|max_length[200]',
            'email'    => 'required|valid_email|max_length[150]',
            'telefone' => 'required|min_length[8]|max_length[100]',
            'cidade'   => 'required|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('anuncie#formularioAnuncio'))
                ->withInput()
                ->with('error', 'Por favor, preencha todos os campos obrigatórios corretamente.');
        }

        $nome     = trim((string)$this->request->getPost('nome'));
        $empresa  = trim((string)$this->request->getPost('empresa'));
        $email    = trim((string)$this->request->getPost('email'));
        $telefone = trim((string)$this->request->getPost('telefone'));
        $cidade   = trim((string)$this->request->getPost('cidade'));
        $plano    = trim((string)($this->request->getPost('plano') ?: 'Plano Básico Mensal (60 produtos)'));
        $whatsNum = preg_replace('/[^0-9]/', '', $telefone);
        $dataHora = date('d/m/Y H:i:s');
        $ip       = $this->request->getIPAddress();

        $mensagemFormatada = "SOLICITAÇÃO DE ANÚNCIO - BOCA SANTA OFERTAS\n"
            . "--------------------------------------------------\n"
            . "Nome do Responsável: {$nome}\n"
            . "Empresa / Comércio: {$empresa}\n"
            . "E-mail: {$email}\n"
            . "Telefone / WhatsApp: {$telefone}\n"
            . "Cidade: {$cidade}\n"
            . "Plano Escolhido: {$plano}\n"
            . "Data/Hora: {$dataHora}\n"
            . "IP: {$ip}\n";

        // 1. Salvar no Banco de Dados (tb_mensagens)
        try {
            $db = Database::connect();
            $db->table('tb_mensagens')->insert([
                'men_parceiro' => 0,
                'men_data'     => date('Y-m-d'),
                'men_email'    => substr($email, 0, 150),
                'men_nome'     => substr($nome, 0, 200),
                'men_msg'      => $mensagemFormatada,
                'men_telefone' => substr($telefone, 0, 100),
                'men_oferta'   => 0,
                'men_cep'      => '',
                'men_whatsapp' => substr($whatsNum, 0, 15),
                'men_cupom'    => null,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[Anuncie] Erro ao gravar em tb_mensagens: ' . $e->getMessage());
        }

        // 2. Montar E-mail HTML para o Comercial (vendas@bocasantaofertas.com.br)
        $linkWhats = $whatsNum ? 'https://wa.me/55' . $whatsNum : '';
        $htmlEmail = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
            <div style='background-color: #dc2626; color: #ffffff; padding: 20px; text-align: center;'>
                <h2 style='margin: 0; font-size: 22px;'>Nova Solicitação de Anúncio</h2>
                <p style='margin: 5px 0 0; opacity: 0.9;'>Boca Santa Ofertas</p>
            </div>
            <div style='padding: 24px; background-color: #ffffff;'>
                <p style='font-size: 16px; color: #1e293b; margin-top: 0;'>Um novo anunciante preencheu a proposta no portal:</p>
                <table style='width: 100%; border-collapse: collapse; font-size: 14px; margin-bottom: 20px;'>
                    <tr>
                        <td style='padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-weight: bold; width: 35%; color: #475569;'>Empresa:</td>
                        <td style='padding: 10px 12px; border-bottom: 1px solid #f1f5f9; color: #0f172a; font-weight: bold;'>" . esc($empresa) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #475569;'>Responsável:</td>
                        <td style='padding: 10px 12px; border-bottom: 1px solid #f1f5f9; color: #0f172a;'>" . esc($nome) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #475569;'>Plano Escolhido:</td>
                        <td style='padding: 10px 12px; border-bottom: 1px solid #f1f5f9; color: #dc2626; font-weight: bold;'>" . esc($plano) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #475569;'>Cidade:</td>
                        <td style='padding: 10px 12px; border-bottom: 1px solid #f1f5f9; color: #0f172a;'>" . esc($cidade) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #475569;'>Telefone / WhatsApp:</td>
                        <td style='padding: 10px 12px; border-bottom: 1px solid #f1f5f9; color: #0f172a;'>" . esc($telefone) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #475569;'>E-mail:</td>
                        <td style='padding: 10px 12px; border-bottom: 1px solid #f1f5f9; color: #0f172a;'><a href='mailto:" . esc($email) . "'>" . esc($email) . "</a></td>
                    </tr>
                    <tr>
                        <td style='padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #475569;'>Data / Hora:</td>
                        <td style='padding: 10px 12px; border-bottom: 1px solid #f1f5f9; color: #64748b;'>{$dataHora} (IP: {$ip})</td>
                    </tr>
                </table>";

        if ($linkWhats) {
            $htmlEmail .= "
                <div style='text-align: center; margin: 25px 0;'>
                    <a href='{$linkWhats}' target='_blank' style='background-color: #25D366; color: #ffffff; padding: 12px 25px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 15px; display: inline-block;'>
                        Abrir WhatsApp do Anunciante
                    </a>
                </div>";
        }

        $htmlEmail .= "
            </div>
            <div style='background-color: #f8fafc; padding: 15px; text-align: center; font-size: 12px; color: #94a3b8;'>
                Portal Boca Santa Ofertas &bull; comercial@bocasantaofertas.com.br
            </div>
        </div>";

        // 3. Enviar para vendas@bocasantaofertas.com.br e cópia para loweltrevisan@gmail.com via SMTP autenticado (Poste.io porta 587)
        $destinatarios = ['vendas@bocasantaofertas.com.br', 'loweltrevisan@gmail.com'];
        $assunto = '[Novo Anunciante] ' . $empresa . ' - ' . $plano;

        try {
            $emailSvc = new EmailService();
            $enviado = $emailSvc->send(
                to: $destinatarios,
                subject: $assunto,
                htmlBody: $htmlEmail,
                fromEmail: 'contato@bocasantaofertas.com.br',
                fromName: 'Boca Santa Ofertas',
                replyToEmail: $email,
                replyToName: $nome
            );

            if ($enviado) {
                log_message('info', "[Anuncie] Proposta de {$empresa} ({$email}) enviada com sucesso para " . implode(', ', $destinatarios) . " via SMTP");
            } else {
                log_message('error', "[Anuncie] EmailService falhou ao enviar proposta de {$empresa} ({$email}) para " . implode(', ', $destinatarios) . ". Detalhes: " . $emailSvc->getLastError());
            }

            // 4. Cópia de confirmação para o anunciante (opcional, silencioso)
            $clientHtml = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
                    <div style='background-color: #dc2626; color: #ffffff; padding: 20px; text-align: center;'>
                        <h2 style='margin: 0; font-size: 20px;'>Olá, " . esc($nome) . "!</h2>
                        <p style='margin: 5px 0 0; opacity: 0.9;'>Recebemos sua solicitação de anúncio</p>
                    </div>
                    <div style='padding: 24px; color: #334155; line-height: 1.6;'>
                        <p>Agradecemos seu interesse em anunciar no <strong>Boca Santa Ofertas</strong>!</p>
                        <p>Nossa equipe comercial entrará em contato em breve para liberar o acesso ao seu painel e orientar sobre o cadastro de produtos.</p>
                        <p style='background-color: #f1f5f9; padding: 12px; border-radius: 6px; font-size: 14px;'>
                            <strong>Plano Solicitado:</strong> " . esc($plano) . "<br>
                            <strong>Empresa:</strong> " . esc($empresa) . "<br>
                            <strong>WhatsApp Informado:</strong> " . esc($telefone) . "
                        </p>
                        <p>Se preferir falar conosco agora mesmo, entre em contato com nosso consultor comercial:</p>
                        <p><strong>Lowel Trevisan</strong> &bull; Telefone / WhatsApp: <strong>(19) 9.9769.66.60</strong></p>
                    </div>
                </div>";

            $emailSvc->send(
                to: $email,
                subject: 'Recebemos sua solicitação - Boca Santa Ofertas',
                htmlBody: $clientHtml,
                fromEmail: 'contato@bocasantaofertas.com.br',
                fromName: 'Boca Santa Ofertas',
                replyToEmail: 'vendas@bocasantaofertas.com.br',
                replyToName: 'Equipe Comercial Boca Santa'
            );
        } catch (\Throwable $e) {
            log_message('error', '[Anuncie] Exceção ao enviar e-mail comercial: ' . $e->getMessage());
        }

        return redirect()->to(base_url('anuncie#formularioAnuncio'))
            ->with('success', 'Obrigado, ' . $nome . '! Recebemos sua solicitação com sucesso. Nossa equipe comercial (Lowel Trevisan) entrará em contato em breve para ativar seus anúncios.');
    }
}
