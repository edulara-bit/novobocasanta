-- ========================================================
-- ATUALIZAÇÕES AUTOMÁTICAS DE SCHEMA - NOVO BOCA SANTA CI4
-- ========================================================

-- Colunas novas em tb_produtos
ALTER TABLE `tb_produtos` ADD COLUMN IF NOT EXISTS `pro_cartao` INT(11) DEFAULT 0;
ALTER TABLE `tb_produtos` ADD COLUMN IF NOT EXISTS `pro_melhor_preco` INT(11) DEFAULT 0;
ALTER TABLE `tb_produtos` ADD COLUMN IF NOT EXISTS `origem` VARCHAR(50) DEFAULT 'local';
ALTER TABLE `tb_produtos` ADD COLUMN IF NOT EXISTS `linefast_product_id` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `tb_produtos` ADD COLUMN IF NOT EXISTS `linefast_sku` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `tb_produtos` ADD COLUMN IF NOT EXISTS `linefast_stock` INT(11) DEFAULT 0;
ALTER TABLE `tb_produtos` ADD COLUMN IF NOT EXISTS `linefast_buy_url` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `tb_produtos` ADD COLUMN IF NOT EXISTS `linefast_sync_at` DATETIME DEFAULT NULL;

-- Colunas novas em tb_cartoes
ALTER TABLE `tb_cartoes` ADD COLUMN IF NOT EXISTS `car_oferta` INT(11) DEFAULT 0;

-- Colunas novas em tb_parceiros
ALTER TABLE `tb_parceiros` ADD COLUMN IF NOT EXISTS `linefast_partner_id` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `tb_parceiros` ADD COLUMN IF NOT EXISTS `linefast_ativo` TINYINT(1) DEFAULT 0;

-- Colunas novas em tb_banners
ALTER TABLE `tb_banners` ADD COLUMN IF NOT EXISTS `ban_titulo` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `tb_banners` ADD COLUMN IF NOT EXISTS `ban_badge` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `tb_banners` ADD COLUMN IF NOT EXISTS `ban_descricao` TEXT DEFAULT NULL;
ALTER TABLE `tb_banners` ADD COLUMN IF NOT EXISTS `ban_botao_texto` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `tb_banners` ADD COLUMN IF NOT EXISTS `ban_botao_link` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `tb_banners` ADD COLUMN IF NOT EXISTS `ban_tipo_fundo` VARCHAR(50) DEFAULT 'degrade';
ALTER TABLE `tb_banners` ADD COLUMN IF NOT EXISTS `ban_fundo_cor` VARCHAR(50) DEFAULT '#0d1b2a';
ALTER TABLE `tb_banners` ADD COLUMN IF NOT EXISTS `ban_fundo_degrade` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `tb_banners` ADD COLUMN IF NOT EXISTS `ban_fundo_imagem` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `tb_banners` ADD COLUMN IF NOT EXISTS `ban_imagem_direita` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `tb_banners` ADD COLUMN IF NOT EXISTS `ban_status` VARCHAR(20) DEFAULT 'ativo';

-- Tabela de Administradores
CREATE TABLE IF NOT EXISTS `tb_administradores` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuário admin padrão (eduardo / blueball)
INSERT IGNORE INTO `tb_administradores` (`adm_id`, `adm_nome`, `adm_login`, `adm_email`, `adm_senha`, `adm_nivel`, `adm_status`)
VALUES (1, 'Eduardo Lara', 'eduardo', 'contato@edulara.com.br', '$2y$12$Nq1P2P5c67LhZJ5vYqWnneP6L4yQh1G8sXvUuJ/9H8uK5k1c6m3mG', 'admin', 1);

-- Tabela de Redirecionamentos SEO
CREATE TABLE IF NOT EXISTS `tb_redirecionamentos` (
  `red_id` INT(11) NOT NULL AUTO_INCREMENT,
  `red_origem` VARCHAR(255) NOT NULL,
  `red_destino` VARCHAR(255) NOT NULL,
  `red_codigo` INT(3) DEFAULT 301,
  `red_ativo` TINYINT(1) DEFAULT 1,
  `red_acessos` INT(11) DEFAULT 0,
  `red_criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`red_id`),
  KEY `idx_red_origem` (`red_origem`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabelas LGPD
CREATE TABLE IF NOT EXISTS `tb_lgpd_consents` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lgpd_requests` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_consentimentos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ip_address` VARCHAR(45) NOT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `tipo` VARCHAR(50) DEFAULT 'cookies',
  `status` VARCHAR(20) DEFAULT 'aceito',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lgpd_solicitacoes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `tipo` VARCHAR(50) NOT NULL,
  `mensagem` TEXT DEFAULT NULL,
  `status` VARCHAR(50) DEFAULT 'pendente',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lgpd_paginas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(100) NOT NULL,
  `titulo` VARCHAR(255) NOT NULL,
  `conteudo` LONGTEXT NOT NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
