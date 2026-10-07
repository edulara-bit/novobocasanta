<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class CheckSchema extends BaseCommand
{
    protected $group       = 'BocaSanta';
    protected $name        = 'bocasanta:check-schema';
    protected $description = 'Verifica e ajusta schemas necessários para o Admin e Área do Parceiro.';

    public function run(array $params)
    {
        $db = Database::connect();
        
        // 1. tb_lgpd_consents
        $lgpdCols = $db->getFieldNames('tb_lgpd_consents');
        CLI::write('tb_lgpd_consents: ' . implode(', ', $lgpdCols), 'cyan');

        if (!in_array('ip_address', $lgpdCols)) {
            $db->query("ALTER TABLE `tb_lgpd_consents` ADD `ip_address` VARCHAR(45) NULL AFTER `visitor_id`;");
            CLI::write('✓ Adicionada coluna ip_address em tb_lgpd_consents', 'green');
        }

        // 2. Modernizar tb_banners
        $bannerCols = $db->getFieldNames('tb_banners');
        CLI::write('tb_banners: ' . implode(', ', $bannerCols), 'cyan');

        // Campos do novo formato de banner (Home hero banner moderno):
        // ban_badge, ban_titulo, ban_descricao, ban_botao_texto, ban_botao_link, ban_tipo_fundo, ban_fundo_cor, ban_fundo_degrade, ban_fundo_imagem, ban_imagem_direita, ban_ordem, ban_status
        $camposBannerNovos = [
            'ban_badge'          => "VARCHAR(100) DEFAULT '⭐ DESTAQUE'",
            'ban_descricao'      => "TEXT NULL",
            'ban_botao_texto'    => "VARCHAR(100) DEFAULT 'Conhecer Ofertas'",
            'ban_botao_link'     => "VARCHAR(255) DEFAULT 'anuncie'",
            'ban_tipo_fundo'     => "VARCHAR(20) DEFAULT 'degrade'", // cor, degrade, imagem
            'ban_fundo_cor'      => "VARCHAR(50) DEFAULT '#0f172a'",
            'ban_fundo_degrade'  => "VARCHAR(255) DEFAULT 'linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%)'",
            'ban_fundo_imagem'   => "VARCHAR(255) NULL",
            'ban_imagem_direita' => "VARCHAR(255) NULL",
            'ban_ordem'          => "INT DEFAULT 0",
            'ban_status'         => "VARCHAR(20) DEFAULT 'ativo'",
        ];

        foreach ($camposBannerNovos as $col => $def) {
            if (!in_array($col, $bannerCols)) {
                $db->query("ALTER TABLE `tb_banners` ADD `{$col}` {$def};");
                CLI::write("✓ Adicionada coluna {$col} em tb_banners", 'green');
            }
        }

        // Popular banners padrão modernos se tabela estiver com poucos registros ou vazia
        $totalBanners = $db->table('tb_banners')->countAllResults();
        if ($totalBanners < 2) {
            $db->table('tb_banners')->insert([
                'ban_titulo'         => 'Conheça as melhores empresas da sua região',
                'ban_badge'          => '⭐ CLUBE DE PARCEIROS',
                'ban_descricao'      => 'Comércios, lojas e prestadores de serviços de confiança com contato direto pelo WhatsApp.',
                'ban_botao_texto'    => 'Conhecer Parceiros',
                'ban_botao_link'     => 'parceiros',
                'ban_tipo_fundo'     => 'degrade',
                'ban_fundo_degrade'  => 'linear-gradient(135deg, #059669 0%, #047857 100%)',
                'ban_ordem'          => 1,
                'ban_status'         => 'ativo',
            ]);

            $db->table('tb_banners')->insert([
                'ban_titulo'         => 'Economize todos os dias com ofertas exclusivas',
                'ban_badge'          => '🔥 SUPER DESCONTOS',
                'ban_descricao'      => 'As melhores promoções de alimentação, eletrônicos, serviços e lazer pertinho de você.',
                'ban_botao_texto'    => 'Ver Todas as Ofertas',
                'ban_botao_link'     => 'categoria/alimentacao',
                'ban_tipo_fundo'     => 'degrade',
                'ban_fundo_degrade'  => 'linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%)',
                'ban_ordem'          => 2,
                'ban_status'         => 'ativo',
            ]);
            CLI::write('✓ Inseridos banners modernos padrão', 'green');
        }

        CLI::write('Ajustes de schema concluídos com sucesso!', 'green');
    }
}
