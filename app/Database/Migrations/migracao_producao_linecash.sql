-- ============================================================================
-- MIGRAÇÃO DE PRODUÇÃO - NOVO BOCA SANTA & ECOSSISTEMA LINECASH (OPÇÃO A)
-- Base destino: bocasanta (Produção em mysql-boca no Easypanel)
-- Seguro para reexecução (Idempotente) - Preserva 100% dos dados de produção
-- ============================================================================

SET NAMES utf8mb4;

-- ----------------------------------------------------------------------------
-- 1. CRIAÇÃO DAS 8 NOVAS TABELAS DO ECOSSISTEMA LINECASH
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tb_lc_contas` (
  `lcc_id` int NOT NULL AUTO_INCREMENT,
  `lcc_tipo` enum('cliente','lojista') COLLATE utf8mb4_unicode_ci NOT NULL,
  `lcc_referencia_id` int DEFAULT NULL COMMENT 'par_id para lojistas ou cli_id/user_id para clientes',
  `lcc_cpf_cnpj` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lcc_nome` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lcc_whatsapp` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lcc_saldo` decimal(10,2) DEFAULT '0.00',
  `lcc_saldo_bloqueado` decimal(10,2) DEFAULT '0.00',
  `lcc_ultimo_movimento` datetime DEFAULT NULL,
  `lcc_ativo` tinyint(1) DEFAULT '1',
  `lcc_criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`lcc_id`),
  UNIQUE KEY `lcc_cpf_cnpj` (`lcc_cpf_cnpj`),
  KEY `idx_lc_contas_ref` (`lcc_tipo`,`lcc_referencia_id`),
  KEY `idx_lc_contas_cpf` (`lcc_cpf_cnpj`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lc_config_lojista` (
  `lcf_lojista_id` int NOT NULL COMMENT 'ID da tb_lc_contas do lojista',
  `lcf_percentual_padrao` decimal(5,2) DEFAULT '1.00',
  `lcf_produto_destaque_titulo` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lcf_produto_destaque_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lcf_mensagem_customizada` text COLLATE utf8mb4_unicode_ci,
  `lcf_atualizado_em` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`lcf_lojista_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lc_assinaturas_lojistas` (
  `lca_id` int NOT NULL AUTO_INCREMENT,
  `lca_lojista_id` int NOT NULL COMMENT 'ID da tb_lc_contas do lojista',
  `lca_status` enum('ativa','inadimplente','cancelada') COLLATE utf8mb4_unicode_ci DEFAULT 'ativa',
  `lca_mp_preapproval_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID de assinatura Mercado Pago',
  `lca_valor_mensal` decimal(10,2) DEFAULT '20.00',
  `lca_vencimento_dia` int DEFAULT '10',
  `lca_ultimo_pagamento` datetime DEFAULT NULL,
  `lca_criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`lca_id`),
  KEY `idx_lca_lojista` (`lca_lojista_id`),
  KEY `idx_lca_status` (`lca_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lc_parceiro_produtos` (
  `lpp_id` int NOT NULL AUTO_INCREMENT,
  `lpp_parceiro_id` int NOT NULL,
  `lpp_codigo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lpp_descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lpp_ativo` tinyint(1) DEFAULT '1',
  `lpp_criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  `lpp_atualizado_em` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`lpp_id`),
  KEY `idx_parceiro` (`lpp_parceiro_id`),
  KEY `idx_codigo` (`lpp_parceiro_id`,`lpp_codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lc_transacoes` (
  `lct_id` int NOT NULL AUTO_INCREMENT,
  `lct_origem_id` int DEFAULT NULL COMMENT 'ID da conta origem ou NULL se emissão pelo sistema',
  `lct_destino_id` int NOT NULL COMMENT 'ID da conta destino',
  `lct_tipo` enum('cashback_venda','pagamento_compra','deposito_recarga','resgate_pix','mensalidade','expiracao','estorno') COLLATE utf8mb4_unicode_ci NOT NULL,
  `lct_valor_lc` decimal(10,2) NOT NULL,
  `lct_valor_compra_reais` decimal(10,2) DEFAULT NULL,
  `lct_percentual_aplicado` decimal(5,2) DEFAULT NULL,
  `lct_descricao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lct_status` enum('concluida','estornada','bloqueada') COLLATE utf8mb4_unicode_ci DEFAULT 'concluida',
  `lct_criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`lct_id`),
  KEY `idx_lct_origem` (`lct_origem_id`),
  KEY `idx_lct_destino` (`lct_destino_id`),
  KEY `idx_lct_tipo` (`lct_tipo`),
  KEY `idx_lct_criado_em` (`lct_criado_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lc_transacoes_produtos` (
  `ltp_id` int NOT NULL AUTO_INCREMENT,
  `ltp_transacao_id` int NOT NULL,
  `ltp_codigo_produto` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ltp_descricao` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ltp_id`),
  KEY `idx_ltp_transacao` (`ltp_transacao_id`),
  KEY `idx_ltp_codigo` (`ltp_codigo_produto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lc_depositos` (
  `lcd_id` int NOT NULL AUTO_INCREMENT,
  `lcd_lojista_id` int NOT NULL COMMENT 'ID da tb_lc_contas do lojista',
  `lcd_valor_reais` decimal(10,2) NOT NULL,
  `lcd_valor_lc_creditado` decimal(10,2) NOT NULL COMMENT '90% do valor',
  `lcd_taxa_bs_reais` decimal(10,2) NOT NULL COMMENT '10% de comissao retida Boca Santa',
  `lcd_comprovante_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lcd_status` enum('pendente','aprovado','rejeitado') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `lcd_observacao` text COLLATE utf8mb4_unicode_ci,
  `lcd_aprovado_por` int DEFAULT NULL,
  `lcd_criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  `lcd_aprovado_em` datetime DEFAULT NULL,
  PRIMARY KEY (`lcd_id`),
  KEY `idx_lcd_lojista` (`lcd_lojista_id`),
  KEY `idx_lcd_status` (`lcd_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tb_lc_resgates` (
  `lcr_id` int NOT NULL AUTO_INCREMENT,
  `lcr_lojista_id` int NOT NULL COMMENT 'ID da tb_lc_contas do lojista',
  `lcr_valor_lc` decimal(10,2) NOT NULL,
  `lcr_desagio_reais` decimal(10,2) NOT NULL COMMENT '2% desagio retido Boca Santa',
  `lcr_valor_liquido_reais` decimal(10,2) NOT NULL COMMENT '98% pago ao lojista',
  `lcr_chave_pix` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lcr_tipo_chave` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lcr_status` enum('pendente','pago','cancelado') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `lcr_comprovante_pix` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lcr_observacao` text COLLATE utf8mb4_unicode_ci,
  `lcr_pago_por` int DEFAULT NULL,
  `lcr_criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  `lcr_pago_em` datetime DEFAULT NULL,
  PRIMARY KEY (`lcr_id`),
  KEY `idx_lcr_lojista` (`lcr_lojista_id`),
  KEY `idx_lcr_status` (`lcr_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. DADOS DE CADASTRO E CONFIGURAÇÃO INICIAL (OPÇÃO A - SEM TRANSAÇÕES TESTE)
-- ----------------------------------------------------------------------------

-- 2.1. Contas LineCash cadastradas (com saldos zerados para Go-Live limpo)
INSERT IGNORE INTO `tb_lc_contas` (
  `lcc_id`, `lcc_tipo`, `lcc_referencia_id`, `lcc_cpf_cnpj`, `lcc_nome`,
  `lcc_whatsapp`, `lcc_saldo`, `lcc_saldo_bloqueado`, `lcc_ultimo_movimento`, `lcc_ativo`, `lcc_criado_em`
) VALUES
  (1, 'lojista', 11, '72788391000161', 'Carnes Emilio Zilio Carnes Cortes Especiais Prime Rib', '(19) 3371.1853', 0.00, 0.00, NULL, 1, '2026-10-29 22:34:33'),
  (2, 'lojista', 503, '74564063000107', 'Pharmácia Alquimia Drogaria Manipulação', '1', 0.00, 0.00, NULL, 1, '2026-10-30 04:29:47'),
  (3, 'cliente', NULL, '05352571807', 'Lowel Trevisan', '19997696660', 0.00, 0.00, NULL, 1, '2026-10-30 04:36:22');

-- 2.2. Configurações padrão dos lojistas LineCash
INSERT IGNORE INTO `tb_lc_config_lojista` (
  `lcf_lojista_id`, `lcf_percentual_padrao`, `lcf_produto_destaque_titulo`,
  `lcf_produto_destaque_url`, `lcf_mensagem_customizada`, `lcf_atualizado_em`
) VALUES
  (1, 1.00, NULL, NULL, NULL, '2026-10-30 01:34:33'),
  (2, 1.00, NULL, NULL, NULL, '2026-10-30 07:29:47');

-- 2.3. Cesta de Produtos / Zona de Calor (Pharmácia Alquimia - par_id = 503)
INSERT IGNORE INTO `tb_lc_parceiro_produtos` (
  `lpp_id`, `lpp_parceiro_id`, `lpp_codigo`, `lpp_descricao`, `lpp_ativo`, `lpp_criado_em`, `lpp_atualizado_em`
) VALUES
  (2, 503, '001', 'composto anti reumatico', 1, '2026-11-07 21:56:49', '2026-11-07 21:56:49'),
  (3, 503, '002', 'Colágeno uc2 + 05  elementos', 1, '2026-11-07 21:57:17', '2026-11-07 21:57:17'),
  (4, 503, '003', 'Floral de Bach', 1, '2026-11-07 21:57:31', '2026-11-07 21:57:31'),
  (5, 503, '004', 'Consulta', 1, '2026-11-07 21:57:42', '2026-11-07 21:57:42');

-- 2.4. Atualização de endereço e contato do cliente Lowel Trevisan (cli_id = 20)
UPDATE `tb_clientes`
SET `cli_email`       = 'loweltrevisan@gmail.com',
    `cli_celular`     = '19997696660',
    `cli_endereco`    = 'pça Jose Bonifário 799',
    `cli_numero`      = '799',
    `cli_complemento` = '55',
    `cli_bairro`      = 'Centro',
    `cli_parceiro`    = 503
WHERE `cli_id` = 20 AND `cli_cpf` = '05352571807';

-- 2.5. Vínculo Linefast no parceiro 1044 (Mimos e Afetos) e URL de compra no produto 21663
UPDATE `tb_parceiros`
SET `linefast_partner_id` = '31',
    `linefast_ativo`      = 1,
    `linefast_is_test`    = 1
WHERE `par_id` = 1044;

UPDATE `tb_produtos`
SET `linefast_buy_url` = 'https://linefast.com.br/piracicaba/mimos-afetos-presentes/amigurumi-de-croche-anime-luffy-mimos-e-afetos-648'
WHERE `pro_id` = 21663;

-- 2.6. Ativação do Banner Oficial Linefast em Produção
UPDATE `tb_banners`
SET `ban_status` = 'inativo'
WHERE `ban_fundo_imagem` IS NULL OR `ban_fundo_imagem` NOT LIKE '%banner_linefast_oficial%';

INSERT INTO `tb_banners` (
  `ban_url`, `ban_titulo`, `ban_cliente`, `ban_link`, `ban_badge`, `ban_descricao`,
  `ban_botao_texto`, `ban_botao_link`, `ban_tipo_fundo`, `ban_fundo_cor`,
  `ban_fundo_degrade`, `ban_fundo_imagem`, `ban_imagem_direita`, `ban_ordem`, `ban_status`
)
SELECT
  '', 'Linefast - O Jeito Antigo de Vender Novinho em Folha', 0, '', 'LINEFAST', '',
  'Conheça o Linefast', 'https://linefast.com.br', 'imagem_unica', '#0f1c33',
  '', 'upimg/banners/banner_linefast_oficial.png', NULL, 1, 'ativo'
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `tb_banners` WHERE `ban_fundo_imagem` LIKE '%banner_linefast_oficial%'
);
