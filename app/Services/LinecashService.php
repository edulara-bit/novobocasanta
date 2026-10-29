<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LcContaModel;
use App\Models\LcTransacaoModel;
use App\Models\LcDepositoModel;
use App\Models\LcResgateModel;
use App\Models\LcConfigLojistaModel;
use App\Models\LcAssinaturaLojistaModel;
use App\Models\ParceiroModel;
use CodeIgniter\Database\BaseConnection;
use Config\Database;
use Exception;

class LinecashService
{
    protected BaseConnection $db;
    protected LcContaModel $contaModel;
    protected LcTransacaoModel $transacaoModel;
    protected LcDepositoModel $depositoModel;
    protected LcResgateModel $resgateModel;
    protected LcConfigLojistaModel $configModel;
    protected LcAssinaturaLojistaModel $assinaturaModel;
    protected ParceiroModel $parceiroModel;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->contaModel = new LcContaModel();
        $this->transacaoModel = new LcTransacaoModel();
        $this->depositoModel = new LcDepositoModel();
        $this->resgateModel = new LcResgateModel();
        $this->configModel = new LcConfigLojistaModel();
        $this->assinaturaModel = new LcAssinaturaLojistaModel();
        $this->parceiroModel = new ParceiroModel();
    }

    /**
     * Limpa CPF ou CNPJ removendo pontuações
     */
    public function cleanDoc(string $doc): string
    {
        return preg_replace('/\D/', '', $doc);
    }

    /**
     * Obtém ou cria a conta LineCash de um Parceiro/Lojista
     */
    public function getOrCreateContaLojista(int $parceiroId): array
    {
        $conta = $this->contaModel->findByParceiroId($parceiroId);
        if ($conta) {
            return $conta;
        }

        $parceiro = $this->parceiroModel->find($parceiroId);
        if (!$parceiro) {
            throw new Exception("Parceiro ID {$parceiroId} não encontrado no sistema.");
        }

        $doc = !empty($parceiro->par_cnpj) ? $this->cleanDoc($parceiro->par_cnpj) : 'PAR' . $parceiroId;
        $nome = (string)($parceiro->getNome() ?: 'Lojista #' . $parceiroId);
        $whatsapp = (string)($parceiro->par_whatsapp ?: $parceiro->par_telefone ?: '');

        $contaId = $this->contaModel->insert([
            'lcc_tipo'             => 'lojista',
            'lcc_referencia_id'    => $parceiroId,
            'lcc_cpf_cnpj'         => $doc,
            'lcc_nome'             => $nome,
            'lcc_whatsapp'         => $whatsapp,
            'lcc_saldo'            => 0.00,
            'lcc_saldo_bloqueado'  => 0.00,
            'lcc_ultimo_movimento' => date('Y-m-d H:i:s'),
            'lcc_ativo'            => 1,
            'lcc_criado_em'        => date('Y-m-d H:i:s'),
        ]);

        // Cria configuração padrão se não existir
        $this->configModel->ignore(true)->insert([
            'lcf_lojista_id'        => $contaId,
            'lcf_percentual_padrao' => 1.00,
        ]);

        return $this->contaModel->find($contaId);
    }

    /**
     * Obtém ou cria a carteira LineCash de um Cliente por CPF
     */
    public function getOrCreateContaCliente(string $cpf, ?string $nome = null, ?string $whatsapp = null): array
    {
        $cleanCpf = $this->cleanDoc($cpf);
        if (strlen($cleanCpf) < 11) {
            throw new Exception("CPF inválido: {$cpf}");
        }

        $conta = $this->contaModel->findByDoc($cleanCpf);
        if ($conta) {
            // Atualiza nome e whatsapp se fornecidos e vazios na conta
            $updates = [];
            if (!empty($nome) && empty($conta['lcc_nome'])) {
                $updates['lcc_nome'] = $nome;
            }
            if (!empty($whatsapp) && empty($conta['lcc_whatsapp'])) {
                $updates['lcc_whatsapp'] = $whatsapp;
            }
            if (!empty($updates)) {
                $this->contaModel->update($conta['lcc_id'], $updates);
                $conta = array_merge($conta, $updates);
            }
            return $conta;
        }

        $contaId = $this->contaModel->insert([
            'lcc_tipo'             => 'cliente',
            'lcc_referencia_id'    => null,
            'lcc_cpf_cnpj'         => $cleanCpf,
            'lcc_nome'             => $nome ?: 'Cliente ' . substr($cleanCpf, 0, 3) . '***',
            'lcc_whatsapp'         => $whatsapp ?: '',
            'lcc_saldo'            => 0.00,
            'lcc_saldo_bloqueado'  => 0.00,
            'lcc_ultimo_movimento' => date('Y-m-d H:i:s'),
            'lcc_ativo'            => 1,
            'lcc_criado_em'        => date('Y-m-d H:i:s'),
        ]);

        return $this->contaModel->find($contaId);
    }

    /**
     * Solicitação de recarga/depósito feita pelo lojista
     * Valor mínimo R$ 100,00
     * 90% em LC creditado após aprovação | 10% comissão Boca Santa
     */
    public function solicitarDeposito(int $lojistaContaId, float $valorReais, ?string $comprovanteUrl = null, ?string $obs = null): array
    {
        if ($valorReais < 100.00) {
            return ['success' => false, 'message' => 'O valor mínimo de depósito é de R$ 100,00.'];
        }

        $taxaBs = round($valorReais * 0.10, 2);
        $valorLc = round($valorReais - $taxaBs, 2);

        $depositoId = $this->depositoModel->insert([
            'lcd_lojista_id'         => $lojistaContaId,
            'lcd_valor_reais'        => $valorReais,
            'lcd_valor_lc_creditado' => $valorLc,
            'lcd_taxa_bs_reais'      => $taxaBs,
            'lcd_comprovante_url'    => $comprovanteUrl,
            'lcd_status'             => 'pendente',
            'lcd_observacao'         => $obs,
            'lcd_criado_em'          => date('Y-m-d H:i:s'),
        ]);

        return [
            'success'     => true,
            'message'     => 'Solicitação de recarga enviada com sucesso para aprovação.',
            'deposito_id' => $depositoId,
            'valor_lc'    => $valorLc,
            'taxa_bs'     => $taxaBs,
        ];
    }

    /**
     * Aprovação de depósito pelo Administrador
     */
    public function aprovarDeposito(int $depositoId, int $adminId): array
    {
        $this->db->transStart();

        $deposito = $this->depositoModel->find($depositoId);
        if (!$deposito) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Depósito não encontrado.'];
        }

        if ($deposito['lcd_status'] !== 'pendente') {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Este depósito já foi processado anteriormente.'];
        }

        $conta = $this->contaModel->find($deposito['lcd_lojista_id']);
        if (!$conta) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Conta do lojista não encontrada.'];
        }

        // Credita os 90% em LC na conta do lojista
        $novoSaldo = (float)$conta['lcc_saldo'] + (float)$deposito['lcd_valor_lc_creditado'];
        $this->contaModel->update($conta['lcc_id'], [
            'lcc_saldo'            => $novoSaldo,
            'lcc_ultimo_movimento' => date('Y-m-d H:i:s'),
        ]);

        // Registra a transação no Ledger
        $this->transacaoModel->insert([
            'lct_origem_id'          => null, // Emissão central
            'lct_destino_id'         => $conta['lcc_id'],
            'lct_tipo'               => 'deposito_recarga',
            'lct_valor_lc'           => $deposito['lcd_valor_lc_creditado'],
            'lct_valor_compra_reais' => $deposito['lcd_valor_reais'],
            'lct_percentual_aplicado'=> 90.00,
            'lct_descricao'          => "Recarga aprovada (R$ {$deposito['lcd_valor_reais']} - 10% comissão BS: R$ {$deposito['lcd_taxa_bs_reais']})",
            'lct_status'             => 'concluida',
            'lct_criado_em'          => date('Y-m-d H:i:s'),
        ]);

        // Atualiza status do depósito
        $this->depositoModel->update($depositoId, [
            'lcd_status'       => 'aprovado',
            'lcd_aprovado_por' => $adminId,
            'lcd_aprovado_em'  => date('Y-m-d H:i:s'),
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Erro ao aprovar depósito no banco de dados.'];
        }

        return [
            'success'  => true,
            'message'  => "Depósito aprovado com sucesso! Creditados {$deposito['lcd_valor_lc_creditado']} LC para {$conta['lcc_nome']}.",
            'valor_lc' => $deposito['lcd_valor_lc_creditado'],
        ];
    }

    /**
     * Rejeição de depósito pelo Administrador
     */
    public function rejeitarDeposito(int $depositoId, int $adminId, string $motivo): array
    {
        $deposito = $this->depositoModel->find($depositoId);
        if (!$deposito || $deposito['lcd_status'] !== 'pendente') {
            return ['success' => false, 'message' => 'Depósito não encontrado ou já processado.'];
        }

        $this->depositoModel->update($depositoId, [
            'lcd_status'       => 'rejeitado',
            'lcd_observacao'   => $motivo,
            'lcd_aprovado_por' => $adminId,
            'lcd_aprovado_em'  => date('Y-m-d H:i:s'),
        ]);

        return ['success' => true, 'message' => 'Depósito rejeitado com sucesso.'];
    }

    /**
     * Lançamento de Cashback no PDV (Lojista -> Cliente)
     * Regra: Mínimo 1% configurável
     */
    public function lancarCashbackPdv(
        int $lojistaContaId,
        string $clienteCpf,
        float $valorCompra,
        float $percentualCashback,
        array $produtos = [],
        ?string $clienteNome = null,
        ?string $clienteWhatsapp = null
    ): array {
        if ($valorCompra <= 0) {
            return ['success' => false, 'message' => 'Valor da compra inválido.'];
        }

        if ($percentualCashback < 1.0) {
            return ['success' => false, 'message' => 'O percentual mínimo de cashback é de 1%.'];
        }

        $valorLc = round($valorCompra * ($percentualCashback / 100), 2);
        if ($valorLc <= 0) {
            return ['success' => false, 'message' => 'Valor de cashback calculado deve ser maior que zero.'];
        }

        $this->db->transStart();

        $contaLojista = $this->contaModel->find($lojistaContaId);
        if (!$contaLojista) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Conta do lojista não encontrada.'];
        }

        $saldoDisponivel = (float)$contaLojista['lcc_saldo'] - (float)$contaLojista['lcc_saldo_bloqueado'];
        if ($saldoDisponivel < $valorLc) {
            $this->db->transRollback();
            return [
                'success' => false,
                'message' => "Saldo de LineCash insuficiente no caixa (Disponível: " . number_format($saldoDisponivel, 2, ',', '.') . " LC | Necessário: " . number_format($valorLc, 2, ',', '.') . " LC). Faça uma recarga.",
            ];
        }

        // Obtém ou cria conta do cliente
        $contaCliente = $this->getOrCreateContaCliente($clienteCpf, $clienteNome, $clienteWhatsapp);

        // Debita do lojista
        $novoSaldoLojista = (float)$contaLojista['lcc_saldo'] - $valorLc;
        $this->contaModel->update($contaLojista['lcc_id'], [
            'lcc_saldo'            => $novoSaldoLojista,
            'lcc_ultimo_movimento' => date('Y-m-d H:i:s'),
        ]);

        // Credita no cliente
        $novoSaldoCliente = (float)$contaCliente['lcc_saldo'] + $valorLc;
        $this->contaModel->update($contaCliente['lcc_id'], [
            'lcc_saldo'            => $novoSaldoCliente,
            'lcc_ultimo_movimento' => date('Y-m-d H:i:s'),
        ]);

        // Registra transação
        $transacaoId = $this->transacaoModel->insert([
            'lct_origem_id'          => $contaLojista['lcc_id'],
            'lct_destino_id'         => $contaCliente['lcc_id'],
            'lct_tipo'               => 'cashback_venda',
            'lct_valor_lc'           => $valorLc,
            'lct_valor_compra_reais' => $valorCompra,
            'lct_percentual_aplicado'=> $percentualCashback,
            'lct_descricao'          => "Cashback de {$percentualCashback}% s/ compra de R$ " . number_format($valorCompra, 2, ',', '.'),
            'lct_status'             => 'concluida',
            'lct_criado_em'          => date('Y-m-d H:i:s'),
        ]);

        // Grava produtos da cesta/zona de calor se houver
        if (!empty($produtos) && is_array($produtos)) {
            $builderProd = $this->db->table('tb_lc_transacoes_produtos');
            foreach ($produtos as $prod) {
                $codigo = trim((string)($prod['codigo'] ?? ''));
                $descricao = trim((string)($prod['descricao'] ?? ''));
                if (!empty($codigo)) {
                    $builderProd->insert([
                        'ltp_transacao_id'   => $transacaoId,
                        'ltp_codigo_produto' => $codigo,
                        'ltp_descricao'      => $descricao ?: null,
                    ]);
                }
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Erro ao processar cashback no banco de dados.'];
        }

        return [
            'success'             => true,
            'message'             => "Cashback de {$valorLc} LC concedido com sucesso para o CPF {$contaCliente['lcc_cpf_cnpj']}!",
            'transacao_id'        => $transacaoId,
            'valor_lc'            => $valorLc,
            'novo_saldo_cliente'  => $novoSaldoCliente,
            'novo_saldo_lojista'  => $novoSaldoLojista,
            'cliente'             => $contaCliente,
        ];
    }

    /**
     * Pagamento com LineCash (Cliente paga a compra -> Lojista)
     * Regra de Negócio: Mínimo 100 LC (R$ 100,00)
     */
    public function pagarComLinecash(int $lojistaContaId, string $clienteCpf, float $valorLc): array
    {
        if ($valorLc < 100.00) {
            return [
                'success' => false,
                'message' => 'O valor mínimo para pagamento com LineCash é de 100 LC (R$ 100,00).',
            ];
        }

        $cleanCpf = $this->cleanDoc($clienteCpf);
        $contaCliente = $this->contaModel->findByDoc($cleanCpf);

        if (!$contaCliente) {
            return ['success' => false, 'message' => 'Cliente não encontrado com este CPF.'];
        }

        $saldoDisponivel = (float)$contaCliente['lcc_saldo'] - (float)$contaCliente['lcc_saldo_bloqueado'];
        if ($saldoDisponivel < $valorLc) {
            return [
                'success' => false,
                'message' => "Saldo insuficiente do cliente. Disponível: " . number_format($saldoDisponivel, 2, ',', '.') . " LC.",
            ];
        }

        $contaLojista = $this->contaModel->find($lojistaContaId);
        if (!$contaLojista) {
            return ['success' => false, 'message' => 'Conta do lojista não encontrada.'];
        }

        $this->db->transStart();

        // Debita do cliente
        $novoSaldoCliente = (float)$contaCliente['lcc_saldo'] - $valorLc;
        $this->contaModel->update($contaCliente['lcc_id'], [
            'lcc_saldo'            => $novoSaldoCliente,
            'lcc_ultimo_movimento' => date('Y-m-d H:i:s'),
        ]);

        // Credita no lojista
        $novoSaldoLojista = (float)$contaLojista['lcc_saldo'] + $valorLc;
        $this->contaModel->update($contaLojista['lcc_id'], [
            'lcc_saldo'            => $novoSaldoLojista,
            'lcc_ultimo_movimento' => date('Y-m-d H:i:s'),
        ]);

        // Registra transação
        $transacaoId = $this->transacaoModel->insert([
            'lct_origem_id'          => $contaCliente['lcc_id'],
            'lct_destino_id'         => $contaLojista['lcc_id'],
            'lct_tipo'               => 'pagamento_compra',
            'lct_valor_lc'           => $valorLc,
            'lct_valor_compra_reais' => $valorLc,
            'lct_percentual_aplicado'=> null,
            'lct_descricao'          => "Pagamento de compra via LineCash no caixa",
            'lct_status'             => 'concluida',
            'lct_criado_em'          => date('Y-m-d H:i:s'),
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Erro ao processar pagamento com LineCash no banco de dados.'];
        }

        return [
            'success'            => true,
            'message'            => "Pagamento de " . number_format($valorLc, 2, ',', '.') . " LC recebido com sucesso!",
            'transacao_id'       => $transacaoId,
            'novo_saldo_lojista' => $novoSaldoLojista,
        ];
    }

    /**
     * Solicitação de Resgate PIX pelo Lojista (LC -> Real)
     * Regra: Mínimo 100 LC, 2% de deságio retido para o Boca Santa
     */
    public function solicitarResgatePix(int $lojistaContaId, float $valorLc, string $chavePix, ?string $tipoChave = null): array
    {
        if ($valorLc < 100.00) {
            return ['success' => false, 'message' => 'O resgate mínimo é de 100 LC.'];
        }

        $conta = $this->contaModel->find($lojistaContaId);
        if (!$conta) {
            return ['success' => false, 'message' => 'Conta do lojista não encontrada.'];
        }

        $saldoDisponivel = (float)$conta['lcc_saldo'] - (float)$conta['lcc_saldo_bloqueado'];
        if ($saldoDisponivel < $valorLc) {
            return [
                'success' => false,
                'message' => "Saldo disponível insuficiente para resgate. Disponível: " . number_format($saldoDisponivel, 2, ',', '.') . " LC.",
            ];
        }

        $desagio = round($valorLc * 0.02, 2); // 2% de deságio para o Boca Santa
        $valorLiquido = round($valorLc - $desagio, 2);

        $this->db->transStart();

        // Bloqueia o saldo para evitar saque duplo enquanto está pendente
        $this->contaModel->update($conta['lcc_id'], [
            'lcc_saldo_bloqueado' => (float)$conta['lcc_saldo_bloqueado'] + $valorLc,
        ]);

        $resgateId = $this->resgateModel->insert([
            'lcr_lojista_id'          => $conta['lcc_id'],
            'lcr_valor_lc'            => $valorLc,
            'lcr_desagio_reais'       => $desagio,
            'lcr_valor_liquido_reais' => $valorLiquido,
            'lcr_chave_pix'           => $chavePix,
            'lcr_tipo_chave'          => $tipoChave,
            'lcr_status'              => 'pendente',
            'lcr_criado_em'           => date('Y-m-d H:i:s'),
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Erro ao registrar pedido de resgate.'];
        }

        return [
            'success'       => true,
            'message'       => "Resgate de {$valorLc} LC solicitado com sucesso! Líquido a receber via PIX: R$ " . number_format($valorLiquido, 2, ',', '.') . " (prazo até 24h).",
            'resgate_id'    => $resgateId,
            'valor_liquido' => $valorLiquido,
            'desagio'       => $desagio,
        ];
    }

    /**
     * Confirmação do pagamento do resgate PIX pelo Administrador
     */
    public function confirmarPagamentoResgate(int $resgateId, int $adminId, ?string $comprovantePix = null, ?string $obs = null): array
    {
        $this->db->transStart();

        $resgate = $this->resgateModel->find($resgateId);
        if (!$resgate || $resgate['lcr_status'] !== 'pendente') {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Pedido de resgate não encontrado ou já finalizado.'];
        }

        $conta = $this->contaModel->find($resgate['lcr_lojista_id']);
        if (!$conta) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Conta do lojista não encontrada.'];
        }

        $valorLc = (float)$resgate['lcr_valor_lc'];

        // Baixa definitiva do saldo e do saldo bloqueado
        $novoSaldo = max(0.00, (float)$conta['lcc_saldo'] - $valorLc);
        $novoBloqueado = max(0.00, (float)$conta['lcc_saldo_bloqueado'] - $valorLc);

        $this->contaModel->update($conta['lcc_id'], [
            'lcc_saldo'            => $novoSaldo,
            'lcc_saldo_bloqueado'  => $novoBloqueado,
            'lcc_ultimo_movimento' => date('Y-m-d H:i:s'),
        ]);

        // Registra a transação de resgate no Ledger
        $this->transacaoModel->insert([
            'lct_origem_id'          => $conta['lcc_id'],
            'lct_destino_id'         => $conta['lcc_id'],
            'lct_tipo'               => 'resgate_pix',
            'lct_valor_lc'           => $valorLc,
            'lct_valor_compra_reais' => $resgate['lcr_valor_liquido_reais'],
            'lct_percentual_aplicado'=> 2.00,
            'lct_descricao'          => "Resgate PIX pago: R$ {$resgate['lcr_valor_liquido_reais']} (2% deságio retido BS: R$ {$resgate['lcr_desagio_reais']})",
            'lct_status'             => 'concluida',
            'lct_criado_em'          => date('Y-m-d H:i:s'),
        ]);

        // Atualiza status do resgate
        $this->resgateModel->update($resgateId, [
            'lcr_status'          => 'pago',
            'lcr_comprovante_pix' => $comprovantePix,
            'lcr_observacao'      => $obs,
            'lcr_pago_por'        => $adminId,
            'lcr_pago_em'         => date('Y-m-d H:i:s'),
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Erro ao processar baixa do resgate.'];
        }

        return ['success' => true, 'message' => 'Resgate PIX marcado como pago com sucesso!'];
    }

    /**
     * Cancelamento de resgate PIX pelo Administrador (devolve saldo bloqueado)
     */
    public function cancelarResgatePix(int $resgateId, int $adminId, string $motivo): array
    {
        $this->db->transStart();

        $resgate = $this->resgateModel->find($resgateId);
        if (!$resgate || $resgate['lcr_status'] !== 'pendente') {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Pedido de resgate não encontrado ou já finalizado.'];
        }

        $conta = $this->contaModel->find($resgate['lcr_lojista_id']);
        if ($conta) {
            $novoBloqueado = max(0.00, (float)$conta['lcc_saldo_bloqueado'] - (float)$resgate['lcr_valor_lc']);
            $this->contaModel->update($conta['lcc_id'], [
                'lcc_saldo_bloqueado' => $novoBloqueado,
            ]);
        }

        $this->resgateModel->update($resgateId, [
            'lcr_status'     => 'cancelado',
            'lcr_observacao' => $motivo,
            'lcr_pago_por'   => $adminId,
            'lcr_pago_em'    => date('Y-m-d H:i:s'),
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Erro ao cancelar resgate.'];
        }

        return ['success' => true, 'message' => 'Resgate cancelado e saldo desbloqueado.'];
    }

    /**
     * Estatísticas gerais para o Dashboard Admin do Boca Santa
     */
    public function getMetricasGerais(): array
    {
        // Saldo total de clientes
        $saldoClientes = (float)($this->contaModel
            ->selectSum('lcc_saldo')
            ->where('lcc_tipo', 'cliente')
            ->first()['lcc_saldo'] ?? 0);

        // Saldo total de lojistas
        $saldoLojistas = (float)($this->contaModel
            ->selectSum('lcc_saldo')
            ->where('lcc_tipo', 'lojista')
            ->first()['lcc_saldo'] ?? 0);

        // Total retido comissão 10% de recargas aprovadas
        $receitaComissoesRecargas = (float)($this->depositoModel
            ->selectSum('lcd_taxa_bs_reais')
            ->where('lcd_status', 'aprovado')
            ->first()['lcd_taxa_bs_reais'] ?? 0);

        // Total retido deságio 2% de resgates pagos
        $receitaDesagioResgates = (float)($this->resgateModel
            ->selectSum('lcr_desagio_reais')
            ->where('lcr_status', 'pago')
            ->first()['lcr_desagio_reais'] ?? 0);

        // Contagens pendentes
        $depositosPendentes = $this->depositoModel->where('lcd_status', 'pendente')->countAllResults();
        $resgatesPendentes = $this->resgateModel->where('lcr_status', 'pendente')->countAllResults();

        // Total de transações
        $totalTransacoes = $this->transacaoModel->countAllResults();

        // Total de carteiras ativas
        $totalClientes = $this->contaModel->where('lcc_tipo', 'cliente')->countAllResults();
        $totalLojistas = $this->contaModel->where('lcc_tipo', 'lojista')->countAllResults();

        return [
            'saldo_clientes'             => $saldoClientes,
            'saldo_lojistas'             => $saldoLojistas,
            'saldo_total_circulacao'     => $saldoClientes + $saldoLojistas,
            'receita_comissoes_recargas' => $receitaComissoesRecargas,
            'receita_desagio_resgates'   => $receitaDesagioResgates,
            'receita_total_bs'           => $receitaComissoesRecargas + $receitaDesagioResgates,
            'depositos_pendentes_count'  => $depositosPendentes,
            'resgates_pendentes_count'   => $resgatesPendentes,
            'total_transacoes'           => $totalTransacoes,
            'total_clientes'             => $totalClientes,
            'total_lojistas'             => $totalLojistas,
        ];
    }
}
