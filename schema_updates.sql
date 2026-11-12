-- ========================================================
-- ATUALIZAÇÕES AUTOMÁTICAS E SEGURAS DE SCHEMA (NOVO BOCA SANTA)
-- ========================================================
-- Executado automaticamente pelo docker-entrypoint.sh ou manualmente no phpMyAdmin.
-- Todas as instruções são compatíveis com execução individual (sem DELIMITER).
-- ========================================================

-- 1. CORREÇÃO DE AUTO INCREMENT E PADRÕES DE BANNERS
ALTER TABLE `tb_produtos` MODIFY COLUMN `pro_id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `tb_parceiros` MODIFY COLUMN `par_id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `tb_banners` MODIFY COLUMN `ban_url` VARCHAR(200) DEFAULT '';
ALTER TABLE `tb_banners` MODIFY COLUMN `ban_cliente` INT(11) DEFAULT 0;
ALTER TABLE `tb_banners` MODIFY COLUMN `ban_link` VARCHAR(300) DEFAULT '';

-- 2. ATIVAÇÃO DO BANNER OFICIAL LINEFAST (Apenas se ainda não existir)
UPDATE `tb_banners` SET `ban_status` = 'inativo' WHERE `ban_fundo_imagem` IS NULL OR `ban_fundo_imagem` NOT LIKE '%banner_linefast_oficial%';

INSERT INTO `tb_banners` (
    `ban_titulo`, `ban_badge`, `ban_descricao`, `ban_botao_texto`, `ban_botao_link`,
    `ban_tipo_fundo`, `ban_fundo_cor`, `ban_fundo_degrade`, `ban_fundo_imagem`,
    `ban_ordem`, `ban_status`
)
SELECT
    'Linefast - O Jeito Antigo de Vender Novinho em Folha',
    'LINEFAST',
    '',
    'Conheça o Linefast',
    'https://linefast.com.br',
    'imagem_unica',
    '#0f1c33',
    '',
    'upimg/banners/banner_linefast_oficial.png',
    1,
    'ativo'
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `tb_banners` WHERE `ban_fundo_imagem` LIKE '%banner_linefast_oficial%'
);

-- 3. TABELAS DE REDIRECIONAMENTO E LGPD (Se não existirem)
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

-- 4. TABELAS DO ECOSSISTEMA LINECASH (8 tabelas)
CREATE TABLE IF NOT EXISTS `tb_lc_contas` (
  `lcc_id` INT(11) NOT NULL AUTO_INCREMENT,
  `lcc_tipo` ENUM('cliente','lojista') NOT NULL,
  `lcc_referencia_id` INT(11) DEFAULT NULL COMMENT 'par_id para lojistas ou cli_id/user_id para clientes',
  `lcc_cpf_cnpj` VARCHAR(20) NOT NULL,
  `lcc_nome` VARCHAR(150) NOT NULL,
  `lcc_whatsapp` VARCHAR(20) NOT NULL,
  `lcc_saldo` DECIMAL(10,2) DEFAULT 0.00,
  `lcc_saldo_bloqueado` DECIMAL(10,2) DEFAULT 0.00,
  `lcc_ultimo_movimento` DATETIME DEFAULT NULL,
  `lcc_ativo` TINYINT(1) DEFAULT 1,
  `lcc_criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`lcc_id`),
  UNIQUE KEY `lcc_cpf_cnpj` (`lcc_cpf_cnpj`),
  KEY `idx_lc_contas_ref` (`lcc_tipo`, `lcc_referencia_id`),
  KEY `idx_lc_contas_cpf` (`lcc_cpf_cnpj`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lc_config_lojista` (
  `lcf_lojista_id` INT(11) NOT NULL COMMENT 'ID da tb_lc_contas do lojista',
  `lcf_percentual_padrao` DECIMAL(5,2) DEFAULT 1.00,
  `lcf_produto_destaque_titulo` VARCHAR(150) DEFAULT NULL,
  `lcf_produto_destaque_url` VARCHAR(255) DEFAULT NULL,
  `lcf_mensagem_customizada` TEXT DEFAULT NULL,
  `lcf_atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`lcf_lojista_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lc_assinaturas_lojistas` (
  `lca_id` INT(11) NOT NULL AUTO_INCREMENT,
  `lca_lojista_id` INT(11) NOT NULL COMMENT 'ID da tb_lc_contas do lojista',
  `lca_status` ENUM('ativa','inadimplente','cancelada') DEFAULT 'ativa',
  `lca_mp_preapproval_id` VARCHAR(100) DEFAULT NULL COMMENT 'ID de assinatura Mercado Pago',
  `lca_valor_mensal` DECIMAL(10,2) DEFAULT 20.00,
  `lca_vencimento_dia` INT(11) DEFAULT 10,
  `lca_ultimo_pagamento` DATETIME DEFAULT NULL,
  `lca_criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`lca_id`),
  KEY `idx_lca_lojista` (`lca_lojista_id`),
  KEY `idx_lca_status` (`lca_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lc_parceiro_produtos` (
  `lpp_id` INT(11) NOT NULL AUTO_INCREMENT,
  `lpp_parceiro_id` INT(11) NOT NULL,
  `lpp_codigo` VARCHAR(50) NOT NULL,
  `lpp_descricao` VARCHAR(255) NOT NULL,
  `lpp_ativo` TINYINT(1) DEFAULT 1,
  `lpp_criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `lpp_atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`lpp_id`),
  KEY `idx_parceiro` (`lpp_parceiro_id`),
  KEY `idx_codigo` (`lpp_parceiro_id`, `lpp_codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lc_transacoes` (
  `lct_id` INT(11) NOT NULL AUTO_INCREMENT,
  `lct_origem_id` INT(11) DEFAULT NULL COMMENT 'ID da conta origem ou NULL se emissão pelo sistema',
  `lct_destino_id` INT(11) NOT NULL COMMENT 'ID da conta destino',
  `lct_tipo` ENUM('cashback_venda','pagamento_compra','deposito_recarga','resgate_pix','mensalidade','expiracao','estorno') NOT NULL,
  `lct_valor_lc` DECIMAL(10,2) NOT NULL,
  `lct_valor_compra_reais` DECIMAL(10,2) DEFAULT NULL,
  `lct_percentual_aplicado` DECIMAL(5,2) DEFAULT NULL,
  `lct_descricao` VARCHAR(255) DEFAULT NULL,
  `lct_status` ENUM('concluida','estornada','bloqueada') DEFAULT 'concluida',
  `lct_criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`lct_id`),
  KEY `idx_lct_origem` (`lct_origem_id`),
  KEY `idx_lct_destino` (`lct_destino_id`),
  KEY `idx_lct_tipo` (`lct_tipo`),
  KEY `idx_lct_criado_em` (`lct_criado_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lc_transacoes_produtos` (
  `ltp_id` INT(11) NOT NULL AUTO_INCREMENT,
  `ltp_transacao_id` INT(11) NOT NULL,
  `ltp_codigo_produto` VARCHAR(50) NOT NULL,
  `ltp_descricao` VARCHAR(150) DEFAULT NULL,
  PRIMARY KEY (`ltp_id`),
  KEY `idx_ltp_transacao` (`ltp_transacao_id`),
  KEY `idx_ltp_codigo` (`ltp_codigo_produto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lc_depositos` (
  `lcd_id` INT(11) NOT NULL AUTO_INCREMENT,
  `lcd_lojista_id` INT(11) NOT NULL COMMENT 'ID da tb_lc_contas do lojista',
  `lcd_valor_reais` DECIMAL(10,2) NOT NULL,
  `lcd_valor_lc_creditado` DECIMAL(10,2) NOT NULL COMMENT '90% do valor',
  `lcd_taxa_bs_reais` DECIMAL(10,2) NOT NULL COMMENT '10% de comissao retida Boca Santa',
  `lcd_comprovante_url` VARCHAR(255) DEFAULT NULL,
  `lcd_status` ENUM('pendente','aprovado','rejeitado') DEFAULT 'pendente',
  `lcd_observacao` TEXT DEFAULT NULL,
  `lcd_aprovado_por` INT(11) DEFAULT NULL,
  `lcd_criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `lcd_aprovado_em` DATETIME DEFAULT NULL,
  PRIMARY KEY (`lcd_id`),
  KEY `idx_lcd_lojista` (`lcd_lojista_id`),
  KEY `idx_lcd_status` (`lcd_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lc_resgates` (
  `lcr_id` INT(11) NOT NULL AUTO_INCREMENT,
  `lcr_lojista_id` INT(11) NOT NULL COMMENT 'ID da tb_lc_contas do lojista',
  `lcr_valor_lc` DECIMAL(10,2) NOT NULL,
  `lcr_desagio_reais` DECIMAL(10,2) NOT NULL COMMENT '2% desagio retido Boca Santa',
  `lcr_valor_liquido_reais` DECIMAL(10,2) NOT NULL COMMENT '98% pago ao lojista',
  `lcr_chave_pix` VARCHAR(150) NOT NULL,
  `lcr_tipo_chave` VARCHAR(50) DEFAULT NULL,
  `lcr_status` ENUM('pendente','pago','cancelado') DEFAULT 'pendente',
  `lcr_comprovante_pix` VARCHAR(255) DEFAULT NULL,
  `lcr_observacao` TEXT DEFAULT NULL,
  `lcr_pago_por` INT(11) DEFAULT NULL,
  `lcr_criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `lcr_pago_em` DATETIME DEFAULT NULL,
  PRIMARY KEY (`lcr_id`),
  KEY `idx_lcr_lojista` (`lcr_lojista_id`),
  KEY `idx_lcr_status` (`lcr_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
