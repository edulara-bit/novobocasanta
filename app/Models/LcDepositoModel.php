<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class LcDepositoModel extends Model
{
    protected $table            = 'tb_lc_depositos';
    protected $primaryKey       = 'lcd_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;

    protected $allowedFields    = [
        'lcd_lojista_id',
        'lcd_valor_reais',
        'lcd_valor_lc_creditado',
        'lcd_taxa_bs_reais',
        'lcd_comprovante_url',
        'lcd_status',
        'lcd_observacao',
        'lcd_aprovado_por',
        'lcd_criado_em',
        'lcd_aprovado_em',
    ];

    /**
     * Retorna depósitos com dados da conta do lojista
     */
    public function getDepositosComLojista(?string $status = null): array
    {
        $builder = $this->select('tb_lc_depositos.*, tb_lc_contas.lcc_nome, tb_lc_contas.lcc_cpf_cnpj, tb_lc_contas.lcc_whatsapp, tb_lc_contas.lcc_saldo')
                        ->join('tb_lc_contas', 'tb_lc_contas.lcc_id = tb_lc_depositos.lcd_lojista_id', 'inner');

        if ($status !== null) {
            $builder->where('lcd_status', $status);
        }

        return $builder->orderBy('lcd_id', 'DESC')->findAll();
    }
}
