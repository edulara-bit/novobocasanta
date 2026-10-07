<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\Parceiro;
use App\Entities\Produto;
use Config\Database;
use Config\Seo as SeoConfig;

class SeoService
{
    protected SeoConfig $config;
    protected $db;

    public function __construct(?SeoConfig $config = null)
    {
        $this->config = $config ?? config('Seo');
        $this->db = Database::connect();
    }

    /**
     * Gera os metadados completos da página (Title, Description, Canonical, OG, Twitter)
     */
    public function generateMeta(array $custom = []): array
    {
        $title = !empty($custom['title']) 
            ? $custom['title'] . ' | ' . $this->config->siteName 
            : $this->config->defaultTitle;

        $description = !empty($custom['description']) 
            ? substr(strip_tags($custom['description']), 0, 160) 
            : $this->config->defaultDescription;

        $url = !empty($custom['url']) ? $custom['url'] : current_url();
        $image = !empty($custom['image']) ? $custom['image'] : base_url($this->config->defaultOgImage);
        $type = !empty($custom['type']) ? $custom['type'] : 'website';

        return [
            'title'       => $title,
            'description' => $description,
            'canonical'   => $url,
            'keywords'    => $custom['keywords'] ?? $this->config->defaultKeywords,
            'og'          => [
                'title'       => $title,
                'description' => $description,
                'url'         => $url,
                'image'       => $image,
                'type'        => $type,
                'site_name'   => $this->config->siteName,
            ],
            'twitter'     => [
                'card'        => 'summary_large_image',
                'title'       => $title,
                'description' => $description,
                'image'       => $image,
                'site'        => $this->config->twitterHandle,
            ],
            'schema_json' => $custom['schema_json'] ?? null,
        ];
    }

    /**
     * Gera marcação estruturada Schema.org (JSON-LD) para Oferta / Produto
     */
    public function generateProductSchema(Produto $produto, ?Parceiro $parceiro = null): string
    {
        $schema = [
            '@context'    => 'https://schema.org/',
            '@type'       => 'Product',
            'name'        => $produto->pro_titulo,
            'image'       => [$produto->getImagemUrl()],
            'description' => strip_tags((string)$produto->pro_descricao),
            'sku'         => $produto->linefast_sku ?? 'BCS-' . $produto->pro_id,
            'offers'      => [
                '@type'         => 'Offer',
                'url'           => $produto->getUrl(),
                'priceCurrency' => 'BRL',
                'price'         => number_format($produto->getPrecoVenda(), 2, '.', ''),
                'priceValidUntil' => !empty($produto->pro_validade) ? $produto->pro_validade->format('Y-m-d') : date('Y-12-31'),
                'itemCondition' => 'https://schema.org/NewCondition',
                'availability'  => ($produto->linefast_stock > 0 || !$produto->isLinefast()) 
                                    ? 'https://schema.org/InStock' 
                                    : 'https://schema.org/OutOfStock',
                'seller'        => [
                    '@type' => 'Organization',
                    'name'  => $parceiro ? $parceiro->getNome() : 'Boca Santa Ofertas',
                ],
            ],
        ];

        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Verifica e resolve redirecionamento 301 de rotas históricas
     */
    public function checkRedirect(string $path): ?string
    {
        $path = trim($path, '/');
        $redirect = $this->db->table('tb_seo_redirects')->where('url_antiga', $path)->get()->getRow();

        if ($redirect) {
            // Atualiza contador de acessos
            $this->db->table('tb_seo_redirects')->where('id', $redirect->id)->update([
                'contador_acessos' => $redirect->contador_acessos + 1,
                'ultimo_acesso'    => date('Y-m-d H:i:s'),
            ]);

            return base_url($redirect->url_nova);
        }

        return null;
    }
}
