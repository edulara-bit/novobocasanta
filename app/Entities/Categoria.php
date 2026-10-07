<?php

declare(strict_types=1);

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Categoria extends Entity
{
    protected $casts = [
        'cat_id'     => 'integer',
        'cat_pai'    => 'integer',
        'cat_status' => 'integer',
        'cat_ordem'  => 'integer',
    ];

    public function getUrl(string $cidadeSlug = 'piracicaba'): string
    {
        $slug = $this->attributes['cat_url'] ?? 'categoria-' . $this->attributes['cat_id'];
        return base_url("{$cidadeSlug}/categoria/{$slug}");
    }

    /**
     * Retorna o ícone do FontAwesome correspondente à categoria
     */
    public function getIconeClass(): string
    {
        $classe = trim((string)($this->attributes['cat_classe'] ?? ''));

        // Se o usuário selecionou um ícone no admin (ex: 'fa-car', 'fa-paw', 'fa-utensils', etc.)
        if (!empty($classe)) {
            if (str_starts_with($classe, 'fa-')) {
                return $classe;
            }
            if (str_contains($classe, 'fa-')) {
                return $classe;
            }
        }

        // Fallback automático por palavras-chave
        $slug = mb_strtolower($this->attributes['cat_url'] ?? $this->attributes['cat_titulo'] ?? '');

        if (str_contains($slug, 'aliment') || str_contains($slug, 'gastronom') || str_contains($slug, 'restauran') || str_contains($slug, 'pizza') || str_contains($slug, 'lanche') || str_contains($slug, 'marmit')) {
            return 'fa-utensils';
        }
        if (str_contains($slug, 'pet') || str_contains($slug, 'animais') || str_contains($slug, 'veterin')) {
            return 'fa-paw';
        }
        if (str_contains($slug, 'auto') || str_contains($slug, 'carro') || str_contains($slug, 'moto') || str_contains($slug, 'mecanic') || str_contains($slug, 'pneu') || str_contains($slug, 'pecas') || str_contains($slug, 'veiculo')) {
            return 'fa-car';
        }
        if (str_contains($slug, 'belez') || str_contains($slug, 'estetic') || str_contains($slug, 'cabel') || str_contains($slug, 'salao') || str_contains($slug, 'massag') || str_contains($slug, 'cosmet')) {
            return 'fa-spa';
        }
        if (str_contains($slug, 'bebe') || str_contains($slug, 'crianca') || str_contains($slug, 'infantil') || str_contains($slug, 'brinquedo')) {
            return 'fa-baby';
        }
        if (str_contains($slug, 'eletron') || str_contains($slug, 'informat') || str_contains($slug, 'comput') || str_contains($slug, 'celular') || str_contains($slug, 'game')) {
            return 'fa-laptop';
        }
        if (str_contains($slug, 'esporte') || str_contains($slug, 'fit') || str_contains($slug, 'academ') || str_contains($slug, 'suplement')) {
            return 'fa-dumbbell';
        }
        if (str_contains($slug, 'movel') || str_contains($slug, 'moveis') || str_contains($slug, 'decor') || str_contains($slug, 'casa') || str_contains($slug, 'tapete') || str_contains($slug, 'colch')) {
            return 'fa-couch';
        }
        if (str_contains($slug, 'constru') || str_contains($slug, 'reforma') || str_contains($slug, 'ferramenta') || str_contains($slug, 'tinta') || str_contains($slug, 'areia') || str_contains($slug, 'telha')) {
            return 'fa-hammer';
        }
        if (str_contains($slug, 'educa') || str_contains($slug, 'curso') || str_contains($slug, 'escola') || str_contains($slug, 'idioma')) {
            return 'fa-graduation-cap';
        }
        if (str_contains($slug, 'saude') || str_contains($slug, 'medic') || str_contains($slug, 'dent') || str_contains($slug, 'farma') || str_contains($slug, 'otica') || str_contains($slug, 'manipula')) {
            return 'fa-heart-pulse';
        }
        if (str_contains($slug, 'moda') || str_contains($slug, 'roupa') || str_contains($slug, 'calcad') || str_contains($slug, 'bolsa') || str_contains($slug, 'lingerie')) {
            return 'fa-shirt';
        }
        if (str_contains($slug, 'arte') || str_contains($slug, 'artesanato') || str_contains($slug, 'foto') || str_contains($slug, 'grafic')) {
            return 'fa-palette';
        }
        if (str_contains($slug, 'equipamento') || str_contains($slug, 'maq') || str_contains($slug, 'impressor') || str_contains($slug, 'locacao')) {
            return 'fa-gears';
        }
        if (str_contains($slug, 'servico') || str_contains($slug, 'conserto') || str_contains($slug, 'assistencia') || str_contains($slug, 'limpeza')) {
            return 'fa-handshake';
        }

        return 'fa-tag';
    }

    /**
     * Retorna a cor temática para o ícone
     */
    public function getIconeCorClass(): string
    {
        $icon = $this->getIconeClass();
        if (str_contains($icon, 'utensils') || str_contains($icon, 'burger') || str_contains($icon, 'pizza') || str_contains($icon, 'couch')) {
            return 'text-warning';
        }
        if (str_contains($icon, 'paw') || str_contains($icon, 'dog') || str_contains($icon, 'cat')) {
            return 'text-success';
        }
        if (str_contains($icon, 'car') || str_contains($icon, 'heart') || str_contains($icon, 'dumbbell') || str_contains($icon, 'spa')) {
            return 'text-danger';
        }
        if (str_contains($icon, 'baby') || str_contains($icon, 'graduation') || str_contains($icon, 'handshake')) {
            return 'text-primary';
        }
        if (str_contains($icon, 'shirt') || str_contains($icon, 'palette')) {
            return 'text-info';
        }
        return 'text-danger';
    }
}
