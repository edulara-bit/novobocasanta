-- ============================================================================
-- LINECASH SCHEMA - BOCA SANTA & LINEFAST
-- Versão 1.2 - Outubro 2026
-- ============================================================================

CREATE TABLE IF NOT EXISTS `tb_lc_contas` (
  `lcc_id` INT AUTO_INCREMENT PRIMARY KEY,
  `lcc_tipo` ENUM('cliente', 'lojista') NOT NULL,
  `lcc_referencia_id` INT NULL COMMENT 'par_id para lojistas ou cli_id/user_id para clientes',
  `lcc_cpf_cnpj` VARCHAR(20) NOT NULL UNIQUE,
  `lcc_nome` VARCHAR(150) NOT NULL,
  `lcc_whatsapp` VARCHAR(20) NOT NULL,
  `lcc_saldo` DECIMAL(10,2) DEFAULT 0.00,
  `lcc_saldo_bloqueado` DECIMAL(10,2) DEFAULT 0.00,
  `lcc_ultimo_movimento` DATETIME NULL,
  `lcc_ativo` TINYINT(1) DEFAULT 1,
  `lcc_criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_lc_contas_ref` (`lcc_tipo`, `lcc_referencia_id`),
  INDEX `idx_lc_contas_cpf` (`lcc_cpf_cnpj`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lc_transacoes` (
  `lct_id` INT AUTO_INCREMENT PRIMARY KEY,
  `lct_origem_id` INT NULL COMMENT 'ID da conta origem ou NULL se emissão pelo sistema',
  `lct_destino_id` INT NOT NULL COMMENT 'ID da conta destino',
  `lct_tipo` ENUM('cashback_venda', 'pagamento_compra', 'deposito_recarga', 'resgate_pix', 'mensalidade', 'expiracao', 'estorno') NOT NULL,
  `lct_valor_lc` DECIMAL(10,2) NOT NULL,
  `lct_valor_compra_reais` DECIMAL(10,2) NULL,
  `lct_percentual_aplicado` DECIMAL(5,2) NULL,
  `lct_descricao` VARCHAR(255) NULL,
  `lct_status` ENUM('concluida', 'estornada', 'bloqueada') DEFAULT 'concluida',
  `lct_criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_lct_origem` (`lct_origem_id`),
  INDEX `idx_lct_destino` (`lct_destino_id`),
  INDEX `idx_lct_tipo` (`lct_tipo`),
  INDEX `idx_lct_criado_em` (`lct_criado_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lc_transacoes_produtos` (
  `ltp_id` INT AUTO_INCREMENT PRIMARY KEY,
  `ltp_transacao_id` INT NOT NULL,
  `ltp_codigo_produto` VARCHAR(50) NOT NULL,
  `ltp_descricao` VARCHAR(150) NULL,
  INDEX `idx_ltp_transacao` (`ltp_transacao_id`),
  INDEX `idx_ltp_codigo` (`ltp_codigo_produto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lc_depositos` (
  `lcd_id` INT AUTO_INCREMENT PRIMARY KEY,
  `lcd_lojista_id` INT NOT NULL COMMENT 'ID da tb_lc_contas do lojista',
  `lcd_valor_reais` DECIMAL(10,2) NOT NULL,
  `lcd_valor_lc_creditado` DECIMAL(10,2) NOT NULL COMMENT '90% do valor',
  `lcd_taxa_bs_reais` DECIMAL(10,2) NOT NULL COMMENT '10% de comissao retida Boca Santa',
  `lcd_comprovante_url` VARCHAR(255) NULL,
  `lcd_status` ENUM('pendente', 'aprovado', 'rejeitado') DEFAULT 'pendente',
  `lcd_observacao` TEXT NULL,
  `lcd_aprovado_por` INT NULL,
  `lcd_criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `lcd_aprovado_em` DATETIME NULL,
  INDEX `idx_lcd_lojista` (`lcd_lojista_id`),
  INDEX `idx_lcd_status` (`lcd_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lc_resgates` (
  `lcr_id` INT AUTO_INCREMENT PRIMARY KEY,
  `lcr_lojista_id` INT NOT NULL COMMENT 'ID da tb_lc_contas do lojista',
  `lcr_valor_lc` DECIMAL(10,2) NOT NULL,
  `lcr_desagio_reais` DECIMAL(10,2) NOT NULL COMMENT '2% desagio retido Boca Santa',
  `lcr_valor_liquido_reais` DECIMAL(10,2) NOT NULL COMMENT '98% pago ao lojista',
  `lcr_chave_pix` VARCHAR(150) NOT NULL,
  `lcr_tipo_chave` VARCHAR(50) NULL,
  `lcr_status` ENUM('pendente', 'pago', 'cancelado') DEFAULT 'pendente',
  `lcr_comprovante_pix` VARCHAR(255) NULL,
  `lcr_observacao` TEXT NULL,
  `lcr_pago_por` INT NULL,
  `lcr_criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `lcr_pago_em` DATETIME NULL,
  INDEX `idx_lcr_lojista` (`lcr_lojista_id`),
  INDEX `idx_lcr_status` (`lcr_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lc_assinaturas_lojistas` (
  `lca_id` INT AUTO_INCREMENT PRIMARY KEY,
  `lca_lojista_id` INT NOT NULL COMMENT 'ID da tb_lc_contas do lojista',
  `lca_status` ENUM('ativa', 'inadimplente', 'cancelada') DEFAULT 'ativa',
  `lca_mp_preapproval_id` VARCHAR(100) NULL COMMENT 'ID de assinatura Mercado Pago',
  `lca_valor_mensal` DECIMAL(10,2) DEFAULT 20.00,
  `lca_vencimento_dia` INT DEFAULT 10,
  `lca_ultimo_pagamento` DATETIME NULL,
  `lca_criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_lca_lojista` (`lca_lojista_id`),
  INDEX `idx_lca_status` (`lca_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lc_config_lojista` (
  `lcf_lojista_id` INT PRIMARY KEY COMMENT 'ID da tb_lc_contas do lojista',
  `lcf_percentual_padrao` DECIMAL(5,2) DEFAULT 1.00,
  `lcf_produto_destaque_titulo` VARCHAR(150) NULL,
  `lcf_produto_destaque_url` VARCHAR(255) NULL,
  `lcf_mensagem_customizada` TEXT NULL,
  `lcf_atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
