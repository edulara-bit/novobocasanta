<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class LcTransacaoModel extends Model
{
    protected $table            = 'tb_lc_transacoes';
    protected $primaryKey       = 'lct_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;

    protected $allowedFields    = [
        'lct_origem_id',
        'lct_destino_id',
        'lct_tipo',
        'lct_valor_lc',
        'lct_valor_compra_reais',
        'lct_percentual_aplicado',
        'lct_descricao',
        'lct_status',
        'lct_criado_em',
    ];

    /**
     * Retorna extrato detalhado de uma conta (créditos e débitos)
     */
    public function getExtratoByConta(int|string $contaId, int $limit = 50): array
    {
        $id = (int)$contaId;
        return $this->select('tb_lc_transacoes.*, 
                             co.lcc_nome as origem_nome, co.lcc_tipo as origem_tipo, 
                             cd.lcc_nome as destino_nome, cd.lcc_tipo as destino_tipo')
                    ->join('tb_lc_contas co', 'co.lcc_id = tb_lc_transacoes.lct_origem_id', 'left')
                    ->join('tb_lc_contas cd', 'cd.lcc_id = tb_lc_transacoes.lct_destino_id', 'left')
                    ->groupStart()
                        ->where('lct_origem_id', $id)
                        ->orWhere('lct_destino_id', $id)
                    ->groupEnd()
                    ->orderBy('lct_id', 'DESC')
                    ->findAll($limit);
    }
}
