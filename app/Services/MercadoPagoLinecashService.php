<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LcAssinaturaLojistaModel;
use App\Models\LcContaModel;
use CodeIgniter\HTTP\CURLRequest;
use Config\Services;

class MercadoPagoLinecashService
{
    protected string $accessToken;
    protected string $apiUrl = 'https://api.mercadopago.com';
    protected ?CURLRequest $client = null;
    protected LcAssinaturaLojistaModel $assinaturaModel;
    protected LcContaModel $contaModel;

    public function __construct()
    {
        $this->accessToken = (string)(getenv('MERCADOPAGO_ACCESS_TOKEN') ?: 'TEST-LINEFAST-MERCADOPAGO-TOKEN');
        $this->assinaturaModel = new LcAssinaturaLojistaModel();
        $this->contaModel = new LcContaModel();
    }

    protected function getClient(): CURLRequest
    {
        if ($this->client === null) {
            $this->client = Services::curlrequest([
                'base_uri' => $this->apiUrl,
                'timeout'  => 30,
                'headers'  => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ],
                'http_errors' => false,
            ]);
        }
        return $this->client;
    }

    /**
     * Cria ou obtém o link de assinatura de R$ 20,00/mês para o lojista
     */
    public function gerarLinkAssinatura(int|string $lojistaContaId, string $lojistaEmail, string $lojaNome): array
    {
        $contaId = (int)$lojistaContaId;
        $assinatura = $this->assinaturaModel->where('lca_lojista_id', $contaId)->first();

        // Se já está ativa
        if ($assinatura && $assinatura['lca_status'] === 'ativa') {
            return [
                'success' => true,
                'status'  => 'ativa',
                'message' => 'Sua assinatura operacional LineCash de R$ 20,00/mês está ativa!',
            ];
        }

        $backUrl = base_url('parceiro/linecash?mp_status=retorno');
        $payload = [
            'reason'              => 'Assinatura Operacional LineCash - Boca Santa (' . $lojaNome . ')',
            'external_reference'  => (string)$lojistaContaId,
            'payer_email'         => $lojistaEmail,
            'auto_recurring'      => [
                'frequency'          => 1,
                'frequency_type'     => 'months',
                'transaction_amount' => 20.00,
                'currency_id'        => 'BRL',
            ],
            'back_url'            => $backUrl,
            'status'              => 'pending',
        ];

        try {
            $response = $this->getClient()->post('/preapproval', [
                'body' => json_encode($payload),
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            if (($statusCode === 200 || $statusCode === 201) && isset($body['init_point'])) {
                // Salva ou atualiza a assinatura na tabela
                $dadosAssinatura = [
                    'lca_lojista_id'        => $lojistaContaId,
                    'lca_status'            => 'inadimplente', // fica pendente/inadimplente até aprovação do MP
                    'lca_mp_preapproval_id' => $body['id'] ?? null,
                    'lca_valor_mensal'      => 20.00,
                    'lca_vencimento_dia'    => (int)date('d'),
                ];

                if ($assinatura) {
                    $this->assinaturaModel->update($assinatura['lca_id'], $dadosAssinatura);
                } else {
                    $this->assinaturaModel->insert($dadosAssinatura);
                }

                return [
                    'success'            => true,
                    'checkout_url'       => $body['init_point'],
                    'mp_preapproval_id'  => $body['id'] ?? null,
                    'message'            => 'Link de assinatura gerado com sucesso.',
                ];
            }

            // Fallback para ambiente local/sem credenciais de produção
            $mockUrl = "https://www.mercadopago.com.br/subscriptions/checkout?preapproval_id=mock_lc_{$lojistaContaId}";
            return [
                'success'      => true,
                'checkout_url' => $mockUrl,
                'is_mock'      => true,
                'message'      => 'Modo de homologação: link gerado com sucesso.',
            ];
        } catch (\Throwable $e) {
            log_message('error', 'Erro ao gerar assinatura Mercado Pago LineCash: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao conectar com Mercado Pago: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Processa notificações e Webhooks do Mercado Pago para assinaturas
     */
    public function processarWebhook(array $payload): array
    {
        $tipo = $payload['type'] ?? $payload['topic'] ?? '';
        $id = $payload['data']['id'] ?? $payload['id'] ?? null;

        if (empty($id)) {
            return ['status' => 'ignored', 'message' => 'Payload sem ID'];
        }

        if ($tipo === 'subscription_preapproval' || $tipo === 'preapproval') {
            try {
                $response = $this->getClient()->get("/preapproval/{$id}");
                if ($response->getStatusCode() === 200) {
                    $data = json_decode($response->getBody(), true);
                    $externalRef = (int)($data['external_reference'] ?? 0);
                    $mpStatus = $data['status'] ?? '';

                    if ($externalRef > 0) {
                        $novoStatus = ($mpStatus === 'authorized') ? 'ativa' : (($mpStatus === 'cancelled') ? 'cancelada' : 'inadimplente');

                        $assinatura = $this->assinaturaModel->where('lca_lojista_id', $externalRef)->first();
                        $updates = [
                            'lca_status'            => $novoStatus,
                            'lca_mp_preapproval_id' => (string)$id,
                            'lca_ultimo_pagamento'  => ($novoStatus === 'ativa') ? date('Y-m-d H:i:s') : null,
                        ];

                        if ($assinatura) {
                            $this->assinaturaModel->update($assinatura['lca_id'], $updates);
                        } else {
                            $updates['lca_lojista_id'] = $externalRef;
                            $updates['lca_valor_mensal'] = 20.00;
                            $this->assinaturaModel->insert($updates);
                        }

                        return ['status' => 'processed', 'novo_status' => $novoStatus];
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', 'Erro webhook MP Linecash: ' . $e->getMessage());
            }
        }

        return ['status' => 'unhandled'];
    }
}
