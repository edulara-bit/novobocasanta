<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Linefast extends BaseConfig
{
    /**
     * URL base da API do Linefast
     */
    public string $apiBaseUrl = 'https://api.linefast.com.br/v1/';

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
}
