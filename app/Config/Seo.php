<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Seo extends BaseConfig
{
    /**
     * Nome do Site / Marca
     */
    public string $siteName = 'Boca Santa Ofertas';

    /**
     * Título padrão para páginas sem título explícito
     */
    public string $defaultTitle = 'Boca Santa Ofertas - As Melhores Ofertas, Descontos e Cupons da Sua Cidade';

    /**
     * Descrição padrão para meta description
     */
    public string $defaultDescription = 'Encontre as melhores ofertas, descontos exclusivos, cupons de desconto e produtos dos melhores parceiros de Piracicaba e região no Boca Santa Ofertas.';

    /**
     * Palavras-chave padrão
     */
    public string $defaultKeywords = 'ofertas, descontos, cupons, piracicaba, boca santa, compras, restaurantes, serviços, produtos';

    /**
     * Imagem OpenGraph padrão para compartilhamento social
     */
    public string $defaultOgImage = 'assets/images/og-bocasanta-share.jpg';

    /**
     * Twitter Card Handler
     */
    public string $twitterHandle = '@bocasanta';

    /**
     * Se o roteamento de preservação de SEO com suporte a URLs antigas está ativo
     */
    public bool $preserveLegacyUrls = true;

    /**
     * Se os redirecionamentos de URLs canônicas devem usar status 301
     */
    public int $redirectStatusCode = 301;
}
