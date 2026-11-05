-- ============================================================================
-- ATUALIZAÇÃO DA TABELA TB_CLIENTES - CAMPOS DE ENDEREÇO COMPLETO
-- Executar no banco de dados de produção / homologação antes ou após o deploy
-- ============================================================================

ALTER TABLE `tb_clientes` 
  ADD COLUMN IF NOT EXISTS `cli_endereco` VARCHAR(150) NULL AFTER `cli_cep`,
  ADD COLUMN IF NOT EXISTS `cli_numero` VARCHAR(20) NULL AFTER `cli_endereco`,
  ADD COLUMN IF NOT EXISTS `cli_complemento` VARCHAR(80) NULL AFTER `cli_numero`,
  ADD COLUMN IF NOT EXISTS `cli_bairro` VARCHAR(80) NULL AFTER `cli_complemento`,
  ADD COLUMN IF NOT EXISTS `cli_cidade` VARCHAR(80) NULL AFTER `cli_bairro`,
  ADD COLUMN IF NOT EXISTS `cli_estado` VARCHAR(2) NULL AFTER `cli_cidade`;
