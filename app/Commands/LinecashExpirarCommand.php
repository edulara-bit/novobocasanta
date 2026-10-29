<?php

declare(strict_types=1);

namespace App\Commands;

use App\Models\LcContaModel;
use App\Models\LcTransacaoModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class LinecashExpirarCommand extends BaseCommand
{
    protected $group       = 'LineCash';
    protected $name        = 'linecash:expirar';
    protected $description = 'Executa a rotina de expiração de saldos inativos há 12 meses e gera avisos aos 11 meses.';

    public function run(array $params)
    {
        $db = Database::connect();
        $contaModel = new LcContaModel();
        $transacaoModel = new LcTransacaoModel();

        CLI::write("=== INICIANDO CRON DE EXPIRAÇÃO LINECASH ===", 'yellow');

        $dataLimite12Meses = date('Y-m-d H:i:s', strtotime('-12 months'));
        $dataLimite11Meses = date('Y-m-d H:i:s', strtotime('-11 months'));

        // 1. AVISO PRÉVIO AOS 11 MESES (Contas com último movimento entre 11 e 12 meses atrás e saldo > 0)
        $contasAviso = $contaModel
            ->where('lcc_saldo >', 0)
            ->where('lcc_ultimo_movimento <', $dataLimite11Meses)
            ->where('lcc_ultimo_movimento >=', $dataLimite12Meses)
            ->findAll();

        CLI::write("1. Contas na zona de aviso prévio (11 meses sem uso): " . count($contasAviso), 'cyan');
        foreach ($contasAviso as $ca) {
            CLI::write("   -> Aviso pendente para CPF {$ca['lcc_cpf_cnpj']} ({$ca['lcc_nome']}): {$ca['lcc_saldo']} LC expirando em 30 dias.", 'light_gray');
        }

        // 2. EXPIRAÇÃO EFETIVA AOS 12 MESES (Contas com último movimento há mais de 12 meses e saldo > 0)
        $contasExpirar = $contaModel
            ->where('lcc_saldo >', 0)
            ->where('lcc_ultimo_movimento <', $dataLimite12Meses)
            ->findAll();

        CLI::write("2. Contas com saldo a expirar (12 meses inativas): " . count($contasExpirar), 'red');

        $totalExpirado = 0.0;
        $contasProcessadas = 0;

        foreach ($contasExpirar as $c) {
            $saldoExpirar = (float)$c['lcc_saldo'];
            if ($saldoExpirar <= 0) {
                continue;
            }

            $db->transStart();

            // Zera saldo da conta
            $contaModel->update($c['lcc_id'], [
                'lcc_saldo'            => 0.00,
                'lcc_ultimo_movimento' => date('Y-m-d H:i:s'),
            ]);

            // Registra movimentação de expiração no Ledger
            $transacaoModel->insert([
                'lct_origem_id'          => $c['lcc_id'],
                'lct_destino_id'         => $c['lcc_id'],
                'lct_tipo'               => 'expiracao',
                'lct_valor_lc'           => $saldoExpirar,
                'lct_valor_compra_reais' => 0.00,
                'lct_descricao'          => "Expiração automática de saldo por inatividade superior a 12 meses.",
                'lct_status'             => 'concluida',
                'lct_criado_em'          => date('Y-m-d H:i:s'),
            ]);

            $db->transComplete();

            if ($db->transStatus() !== false) {
                $totalExpirado += $saldoExpirar;
                $contasProcessadas++;
                CLI::write("   [EXPIRADO] Conta #{$c['lcc_id']} ({$c['lcc_cpf_cnpj']}): -{$saldoExpirar} LC", 'green');
            }
        }

        CLI::write("=== FINALIZADO: {$contasProcessadas} contas processadas | Total expirado: {$totalExpirado} LC ===", 'yellow');
    }
}
