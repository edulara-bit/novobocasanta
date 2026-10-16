<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Linefast extends BaseConfig
{
    /**
     * URL base da API do Linefast
     */
    public string $apiBaseUrl = 'https://linefast.com.br/api/v1/';

    /**
     * Chave de API de integração
     */
    public string $apiKey = '';

    /**
     * Chave Secreta para validação de Webhooks
     */
    public string $apiSecret = '';

    /**
     * URL de Deep Link do carrinho de compras do Linefast
     * Exemplo: https://linefast.com.br/cart/add?product_id={id}&qty={qty}&ref=bocasanta
     */
    public string $cartUrl = 'https://linefast.com.br/cart/add';

    /**
     * Intervalo de sincronização automática de estoque/produtos em minutos
     */
    public int $syncIntervalMinutes = 30;

    /**
     * Se a integração está ativa globalmente
     */
    public bool $enabled = true;

    /**
     * Timeout para chamadas HTTP (segundos)
     */
    public int $timeout = 15;

    public function __construct()
    {
        parent::__construct();

        $this->apiBaseUrl = env('linefast.apiBaseUrl', env('LINEFAST_API_URL', $this->apiBaseUrl));
        $this->apiKey = env('linefast.apiKey', env('LINEFAST_API_KEY', $this->apiKey));
        $this->apiSecret = env('linefast.apiSecret', env('LINEFAST_API_SECRET', $this->apiSecret));
        $this->cartUrl = env('linefast.cartUrl', env('LINEFAST_CART_URL', $this->cartUrl));
    }
}