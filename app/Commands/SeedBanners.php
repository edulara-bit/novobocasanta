<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SeedBanners extends BaseCommand
{
    protected $group       = 'BocaSanta';
    protected $name        = 'bocasanta:seed-banners';
    protected $description = 'Reseta os banners do banco e insere os 3 slides atuais da home';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        $destDir = ROOTPATH . 'public/upimg/banners';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        $brainDir = 'C:/Users/conta/.gemini/antigravity-ide/brain/970fb732-c745-4e84-9503-b6be08c37d6a';
        $files = glob($brainDir . '/*banner*3d*.jpg');
        
        $imgOfertas = 'banner_ofertas_3d.jpg';
        $imgLinefast = 'banner_linefast_3d.jpg';
        $imgParceiros = 'banner_parceiros_3d.jpg';

        foreach ($files as $f) {
            if (str_contains($f, 'ofertas')) {
                copy($f, $destDir . '/' . $imgOfertas);
            } elseif (str_contains($f, 'linefast')) {
                copy($f, $destDir . '/' . $imgLinefast);
            } elseif (str_contains($f, 'parceiros')) {
                copy($f, $destDir . '/' . $imgParceiros);
            }
        }

        // Garantir que ban_id é PRIMARY KEY AUTO_INCREMENT
        try {
            $db->query("ALTER TABLE tb_banners MODIFY ban_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
        } catch (\Throwable $e) {
            try {
                $db->query("ALTER TABLE tb_banners MODIFY ban_id INT(11) NOT NULL AUTO_INCREMENT");
            } catch (\Throwable $e2) {}
        }

        $db->table('tb_banners')->truncate();
        CLI::write('Tabela tb_banners limpa.', 'yellow');

        $db->table('tb_banners')->insertBatch([
            [
                'ban_titulo'         => 'Economize até 70% no comércio local',
                'ban_badge'          => '🔥 SUPER OFERTAS LOCAIS',
                'ban_descricao'      => 'Descubra restaurantes, serviços automotivos, informática, saúde, beleza e produtos com os melhores preços da cidade.',
                'ban_botao_texto'    => 'Ver Ofertas',
                'ban_botao_link'     => '#ofertasDestaque',
                'ban_tipo_fundo'     => 'degrade',
                'ban_fundo_cor'      => '#dc2626',
                'ban_fundo_degrade'  => 'linear-gradient(90deg, #991b1b 0%, #dc2626 50%, #ea580c 100%)',
                'ban_imagem_direita' => 'upimg/banners/' . $imgOfertas,
                'ban_ordem'          => 1,
                'ban_status'         => 'ativo',
                'ban_cliente'        => 'Boca Santa',
            ],
            [
                'ban_titulo'         => 'Compre direto dos parceiros no Linefast',
                'ban_badge'          => '⚡ INTEGRAÇÃO LINEFAST',
                'ban_descricao'      => 'Produtos em estoque com compra em 1 clique e envio rápido diretamente pelo carrinho do parceiro.',
                'ban_botao_texto'    => 'Explorar Produtos Linefast',
                'ban_botao_link'     => '#secaoLinefast',
                'ban_tipo_fundo'     => 'degrade',
                'ban_fundo_cor'      => '#2563eb',
                'ban_fundo_degrade'  => 'linear-gradient(90deg, #1e3a8a 0%, #2563eb 50%, #38bdf8 100%)',
                'ban_imagem_direita' => 'upimg/banners/' . $imgLinefast,
                'ban_ordem'          => 2,
                'ban_status'         => 'ativo',
                'ban_cliente'        => 'Linefast',
            ],
            [
                'ban_titulo'         => 'Conheça as melhores empresas da sua região',
                'ban_badge'          => '⭐ CLUBE DE PARCEIROS',
                'ban_descricao'      => 'Comércios, lojas e prestadores de serviços de confiança com contato direto pelo WhatsApp.',
                'ban_botao_texto'    => 'Conhecer Parceiros',
                'ban_botao_link'     => 'piracicaba/parceiros',
                'ban_tipo_fundo'     => 'degrade',
                'ban_fundo_cor'      => '#059669',
                'ban_fundo_degrade'  => 'linear-gradient(90deg, #065f46 0%, #059669 50%, #10b981 100%)',
                'ban_imagem_direita' => 'upimg/banners/' . $imgParceiros,
                'ban_ordem'          => 3,
                'ban_status'         => 'ativo',
                'ban_cliente'        => 'Boca Santa',
            ]
        ]);

        CLI::write('3 slides atuais com imagens 3D inseridos com sucesso na tabela tb_banners!', 'green');
    }
}
