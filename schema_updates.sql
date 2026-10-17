-- ========================================================
-- ATUALIZAÇÕES AUTOMÁTICAS E SEGURAS DE SCHEMA (NOVO BOCA SANTA)
-- ========================================================
-- Este script pode ser executado QUANTAS VEZES QUISER no phpMyAdmin!
-- Ele verifica o banco e NÃO INTERROMPE se os campos já existirem.
-- ========================================================

-- 1. CORREÇÃO DE AUTO INCREMENT
ALTER TABLE `tb_produtos` MODIFY COLUMN `pro_id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `tb_parceiros` MODIFY COLUMN `par_id` INT(11) NOT NULL AUTO_INCREMENT;

-- 2. CRIAÇÃO DE PROCEDIMENTO TEMPORÁRIO (Adiciona coluna somente se NÃO existir)
DROP PROCEDURE IF EXISTS `AddCol`;
DELIMITER $$
CREATE PROCEDURE `AddCol`(
    IN tblName VARCHAR(64),
    IN colName VARCHAR(64),
    IN colDef TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = tblName
          AND COLUMN_NAME = colName
    ) THEN
        SET @ddl = CONCAT('ALTER TABLE `', tblName, '` ADD `', colName, '` ', colDef);
        PREPARE stmt FROM @ddl;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$
DELIMITER ;

-- 3. PRODUTOS (tb_produtos)
CALL AddCol('tb_produtos', 'pro_cartao', 'INT(11) DEFAULT 0');
CALL AddCol('tb_produtos', 'pro_melhor_preco', 'INT(11) DEFAULT 0');
CALL AddCol('tb_produtos', 'origem', 'VARCHAR(50) DEFAULT \'local\'');
CALL AddCol('tb_produtos', 'linefast_product_id', 'VARCHAR(100) DEFAULT NULL');
CALL AddCol('tb_produtos', 'linefast_sku', 'VARCHAR(100) DEFAULT NULL');
CALL AddCol('tb_produtos', 'linefast_stock', 'INT(11) DEFAULT 0');
CALL AddCol('tb_produtos', 'linefast_buy_url', 'VARCHAR(500) DEFAULT NULL');
CALL AddCol('tb_produtos', 'linefast_sync_at', 'DATETIME DEFAULT NULL');

-- 4. CARTÕES DE FIDELIDADE & CASHBACK (tb_cartoes)
CALL AddCol('tb_cartoes', 'car_oferta', 'INT(11) DEFAULT 0');
CALL AddCol('tb_cartoes', 'car_percentagem', 'DECIMAL(10,2) DEFAULT 0.00');
CALL AddCol('tb_cartoes', 'car_minimo', 'DECIMAL(10,2) DEFAULT 0.00');

-- 5. PARCEIROS & LINEFAST (tb_parceiros)
CALL AddCol('tb_parceiros', 'linefast_partner_id', 'VARCHAR(100) DEFAULT NULL');
CALL AddCol('tb_parceiros', 'linefast_ativo', 'TINYINT(1) DEFAULT 0');
CALL AddCol('tb_parceiros', 'linefast_is_test', 'TINYINT(1) NOT NULL DEFAULT 0');

-- 6. BANNERS (tb_banners)
ALTER TABLE `tb_banners` MODIFY COLUMN `ban_url` VARCHAR(200) DEFAULT '';
ALTER TABLE `tb_banners` MODIFY COLUMN `ban_cliente` INT(11) DEFAULT 0;
ALTER TABLE `tb_banners` MODIFY COLUMN `ban_link` VARCHAR(300) DEFAULT '';
CALL AddCol('tb_banners', 'ban_titulo', 'VARCHAR(255) DEFAULT NULL');
CALL AddCol('tb_banners', 'ban_badge', 'VARCHAR(100) DEFAULT \'⭐ DESTAQUE\'');
CALL AddCol('tb_banners', 'ban_descricao', 'TEXT DEFAULT NULL');
CALL AddCol('tb_banners', 'ban_botao_texto', 'VARCHAR(100) DEFAULT \'Conhecer Ofertas\'');
CALL AddCol('tb_banners', 'ban_botao_link', 'VARCHAR(255) DEFAULT \'anuncie\'');
CALL AddCol('tb_banners', 'ban_tipo_fundo', 'VARCHAR(50) DEFAULT \'degrade\'');
CALL AddCol('tb_banners', 'ban_fundo_cor', 'VARCHAR(50) DEFAULT \'#0d1b2a\'');
CALL AddCol('tb_banners', 'ban_fundo_degrade', 'VARCHAR(255) DEFAULT NULL');
CALL AddCol('tb_banners', 'ban_fundo_imagem', 'VARCHAR(255) DEFAULT NULL');
CALL AddCol('tb_banners', 'ban_imagem_direita', 'VARCHAR(255) DEFAULT NULL');
CALL AddCol('tb_banners', 'ban_ordem', 'INT(11) DEFAULT 0');
CALL AddCol('tb_banners', 'ban_status', 'VARCHAR(20) DEFAULT \'ativo\'');

-- 7. ADMINISTRADORES (tb_administradores)
CALL AddCol('tb_administradores', 'adm_nivel', 'VARCHAR(50) DEFAULT \'admin\'');
CALL AddCol('tb_administradores', 'adm_criado_em', 'DATETIME DEFAULT CURRENT_TIMESTAMP');

-- REMOVE O PROCEDIMENTO TEMPORÁRIO
DROP PROCEDURE IF EXISTS `AddCol`;

-- 8. CRIAÇÃO DE NOVAS TABELAS (Se não existirem)
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
