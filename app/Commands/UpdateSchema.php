<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class UpdateSchema extends BaseCommand
{
    protected $group       = 'BocaSanta';
    protected $name        = 'bocasanta:update-schema';
    protected $description = 'Aplica todas as atualizações de schema e tabelas no banco de dados de forma 100% segura e compatível.';

    public function run(array $params)
    {
        $db = Database::connect();
        CLI::write('Iniciando sincronização de schema...', 'yellow');

        // 1. TABELA TB_PRODUTOS
        CLI::write("\n[1/5] Verificando tb_produtos...", 'cyan');
        if ($db->tableExists('tb_produtos')) {
            $cols = $db->getFieldNames('tb_produtos');
            
            // Garantir Auto Increment em pro_id
            try {
                $db->query("ALTER TABLE `tb_produtos` MODIFY COLUMN `pro_id` INT(11) NOT NULL AUTO_INCREMENT;");
                CLI::write("  ✓ AUTO_INCREMENT garantido em pro_id", 'green');
            } catch (\Throwable $e) {
                CLI::write("  ! AUTO_INCREMENT pro_id: " . $e->getMessage(), 'yellow');
            }

            $produtosCols = [
                'pro_cartao'          => "INT(11) DEFAULT 0",
                'pro_melhor_preco'    => "INT(11) DEFAULT 0",
                'origem'              => "VARCHAR(50) DEFAULT 'local'",
                'linefast_product_id' => "VARCHAR(100) DEFAULT NULL",
                'linefast_sku'        => "VARCHAR(100) DEFAULT NULL",
                'linefast_stock'      => "INT(11) DEFAULT 0",
                'linefast_buy_url'    => "VARCHAR(500) DEFAULT NULL",
                'linefast_sync_at'    => "DATETIME DEFAULT NULL",
            ];

            foreach ($produtosCols as $col => $def) {
                if (!in_array($col, $cols)) {
                    $db->query("ALTER TABLE `tb_produtos` ADD `{$col}` {$def};");
                    CLI::write("  ✓ Adicionada coluna {$col}", 'green');
                } else {
                    CLI::write("  - Coluna {$col} já existe", 'dark_gray');
                }
            }
        }

        // 2. TABELA TB_PARCEIROS
        CLI::write("\n[2/5] Verificando tb_parceiros...", 'cyan');
        if ($db->tableExists('tb_parceiros')) {
            $cols = $db->getFieldNames('tb_parceiros');

            // Garantir Auto Increment em par_id
            try {
                $db->query("ALTER TABLE `tb_parceiros` MODIFY COLUMN `par_id` INT(11) NOT NULL AUTO_INCREMENT;");
                CLI::write("  ✓ AUTO_INCREMENT garantido em par_id", 'green');
            } catch (\Throwable $e) {
                CLI::write("  ! AUTO_INCREMENT par_id: " . $e->getMessage(), 'yellow');
            }

            $parceirosCols = [
                'linefast_partner_id' => "VARCHAR(100) DEFAULT NULL",
                'linefast_ativo'      => "TINYINT(1) DEFAULT 0",
                'linefast_is_test'    => "TINYINT(1) NOT NULL DEFAULT 0",
            ];

            foreach ($parceirosCols as $col => $def) {
                if (!in_array($col, $cols)) {
                    $db->query("ALTER TABLE `tb_parceiros` ADD `{$col}` {$def};");
                    CLI::write("  ✓ Adicionada coluna {$col}", 'green');
                } else {
                    CLI::write("  - Coluna {$col} já existe", 'dark_gray');
                }
            }
        }

        // 3. TABELA TB_CARTOES
        CLI::write("\n[3/5] Verificando tb_cartoes...", 'cyan');
        if ($db->tableExists('tb_cartoes')) {
            $cols = $db->getFieldNames('tb_cartoes');
            $cartoesCols = [
                'car_oferta'      => "INT(11) DEFAULT 0",
                'car_percentagem' => "DECIMAL(10,2) DEFAULT 0.00",
                'car_minimo'      => "DECIMAL(10,2) DEFAULT 0.00",
            ];

            foreach ($cartoesCols as $col => $def) {
                if (!in_array($col, $cols)) {
                    $db->query("ALTER TABLE `tb_cartoes` ADD `{$col}` {$def};");
                    CLI::write("  ✓ Adicionada coluna {$col}", 'green');
                } else {
                    CLI::write("  - Coluna {$col} já existe", 'dark_gray');
                }
            }
        }

        // 4. TABELA TB_BANNERS
        CLI::write("\n[4/5] Verificando tb_banners...", 'cyan');
        if ($db->tableExists('tb_banners')) {
            $cols = $db->getFieldNames('tb_banners');

            // Ajustar defaults de colunas legadas
            try {
                $db->query("ALTER TABLE `tb_banners` MODIFY COLUMN `ban_url` VARCHAR(200) DEFAULT '', MODIFY COLUMN `ban_cliente` INT(11) DEFAULT 0, MODIFY COLUMN `ban_link` VARCHAR(300) DEFAULT '';");
                CLI::write("  ✓ Defaults ajustados em ban_url, ban_cliente e ban_link", 'green');
            } catch (\Throwable $e) {
                CLI::write("  ! Defaults tb_banners: " . $e->getMessage(), 'yellow');
            }

            $bannersCols = [
                'ban_titulo'         => "VARCHAR(255) DEFAULT NULL",
                'ban_badge'          => "VARCHAR(100) DEFAULT '⭐ DESTAQUE'",
                'ban_descricao'      => "TEXT DEFAULT NULL",
                'ban_botao_texto'    => "VARCHAR(100) DEFAULT 'Conhecer Ofertas'",
                'ban_botao_link'     => "VARCHAR(255) DEFAULT 'anuncie'",
                'ban_tipo_fundo'     => "VARCHAR(50) DEFAULT 'degrade'",
                'ban_fundo_cor'      => "VARCHAR(50) DEFAULT '#0d1b2a'",
                'ban_fundo_degrade'  => "VARCHAR(255) DEFAULT NULL",
                'ban_fundo_imagem'   => "VARCHAR(255) DEFAULT NULL",
                'ban_imagem_direita' => "VARCHAR(255) DEFAULT NULL",
                'ban_ordem'          => "INT(11) DEFAULT 0",
                'ban_status'         => "VARCHAR(20) DEFAULT 'ativo'",
            ];

            foreach ($bannersCols as $col => $def) {
                if (!in_array($col, $cols)) {
                    $db->query("ALTER TABLE `tb_banners` ADD `{$col}` {$def};");
                    CLI::write("  ✓ Adicionada coluna {$col}", 'green');
                } else {
                    CLI::write("  - Coluna {$col} já existe", 'dark_gray');
                }
            }
        }

        // 5. CRIAÇÃO DE NOVAS TABELAS DO NOVO BOCA SANTA
        CLI::write("\n[5/5] Verificando tabelas de suporte...", 'cyan');

        // tb_administradores
        $db->query("CREATE TABLE IF NOT EXISTS `tb_administradores` (
            `adm_id` INT(11) NOT NULL AUTO_INCREMENT,
            `adm_nome` VARCHAR(150) NOT NULL,
            `adm_login` VARCHAR(100) NOT NULL,
            `adm_email` VARCHAR(150) NOT NULL,
            `adm_senha` VARCHAR(255) NOT NULL,
            `adm_nivel` VARCHAR(50) DEFAULT 'admin',
            `adm_status` TINYINT(1) DEFAULT 1,
            `adm_criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`adm_id`),
            UNIQUE KEY `uk_adm_login` (`adm_login`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        CLI::write("  ✓ Tabela tb_administradores verificada", 'green');

        // Inserir admin padrao se tabela vazia
        $totalAdm = $db->table('tb_administradores')->countAllResults();
        if ($totalAdm === 0) {
            $db->table('tb_administradores')->insert([
                'adm_id'     => 1,
                'adm_nome'   => 'Eduardo Lara',
                'adm_login'  => 'eduardo',
                'adm_email'  => 'contato@edulara.com.br',
                'adm_senha'  => password_hash('blueball', PASSWORD_BCRYPT),
                'adm_nivel'  => 'admin',
                'adm_status' => 1,
            ]);
            CLI::write("  ✓ Usuário admin padrão criado (eduardo / blueball)", 'green');
        }

        // tb_redirecionamentos
        $db->query("CREATE TABLE IF NOT EXISTS `tb_redirecionamentos` (
            `red_id` INT(11) NOT NULL AUTO_INCREMENT,
            `red_origem` VARCHAR(255) NOT NULL,
            `red_destino` VARCHAR(255) NOT NULL,
            `red_codigo` INT(3) DEFAULT 301,
            `red_ativo` TINYINT(1) DEFAULT 1,
            `red_acessos` INT(11) DEFAULT 0,
            `red_criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`red_id`),
            KEY `idx_red_origem` (`red_origem`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        CLI::write("  ✓ Tabela tb_redirecionamentos verificada", 'green');

        // Tabelas LGPD
        $db->query("CREATE TABLE IF NOT EXISTS `tb_lgpd_consents` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `visitor_id` VARCHAR(100) NOT NULL,
            `user_id` INT(11) DEFAULT NULL,
            `ip_address` VARCHAR(45) DEFAULT NULL,
            `ip_hash` VARCHAR(64) DEFAULT NULL,
            `consent_essential` TINYINT(1) DEFAULT 1,
            `consent_analytics` TINYINT(1) DEFAULT 0,
            `consent_marketing` TINYINT(1) DEFAULT 0,
            `user_agent` VARCHAR(255) DEFAULT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_visitor` (`visitor_id`),
            KEY `idx_user` (`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $db->query("CREATE TABLE IF NOT EXISTS `tb_lgpd_requests` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `user_id` INT(11) DEFAULT NULL,
            `nome` VARCHAR(150) NOT NULL,
            `email` VARCHAR(150) NOT NULL,
            `tipo_requisicao` VARCHAR(50) NOT NULL,
            `status` VARCHAR(50) DEFAULT 'pendente',
            `observacoes` TEXT DEFAULT NULL,
            `resposta_dpo` TEXT DEFAULT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $db->query("CREATE TABLE IF NOT EXISTS `tb_consentimentos` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `ip_address` VARCHAR(45) NOT NULL,
            `user_agent` TEXT DEFAULT NULL,
            `tipo` VARCHAR(50) DEFAULT 'cookies',
            `status` VARCHAR(20) DEFAULT 'aceito',
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $db->query("CREATE TABLE IF NOT EXISTS `tb_lgpd_solicitacoes` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `nome` VARCHAR(150) NOT NULL,
            `email` VARCHAR(150) NOT NULL,
            `tipo` VARCHAR(50) NOT NULL,
            `mensagem` TEXT DEFAULT NULL,
            `status` VARCHAR(50) DEFAULT 'pendente',
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $db->query("CREATE TABLE IF NOT EXISTS `tb_lgpd_paginas` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `slug` VARCHAR(100) NOT NULL,
            `titulo` VARCHAR(255) NOT NULL,
            `conteudo` LONGTEXT NOT NULL,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_slug` (`slug`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        CLI::write("  ✓ Tabelas LGPD verificadas", 'green');

        CLI::write("\n=======================================================", 'green');
        CLI::write(" TODAS AS ATUALIZAÇÕES FORAM APLICADAS COM SUCESSO!", 'green');
        CLI::write("=======================================================\n", 'green');
    }
}
