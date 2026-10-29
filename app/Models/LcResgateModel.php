<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class LcResgateModel extends Model
{
    protected $table            = 'tb_lc_resgates';
    protected $primaryKey       = 'lcr_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;

    protected $allowedFields    = [
        'lcr_lojista_id',
        'lcr_valor_lc',
        'lcr_desagio_reais',
        'lcr_valor_liquido_reais',
        'lcr_chave_pix',
        'lcr_tipo_chave',
        'lcr_status',
        'lcr_comprovante_pix',
        'lcr_observacao',
        'lcr_pago_por',
        'lcr_criado_em',
        'lcr_pago_em',
    ];

    /**
     * Retorna solicitações de resgate com dados do lojista
     */
    public function getResgatesComLojista(?string $status = null): array
    {
        $builder = $this->select('tb_lc_resgates.*, tb_lc_contas.lcc_nome, tb_lc_contas.lcc_cpf_cnpj, tb_lc_contas.lcc_whatsapp, tb_lc_contas.lcc_saldo')
                        ->join('tb_lc_contas', 'tb_lc_contas.lcc_id = tb_lc_resgates.lcr_lojista_id', 'inner');

        if ($status !== null) {
            $builder->where('lcr_status', $status);
        }

        return $builder->orderBy('lcr_id', 'DESC')->findAll();
    }
}
