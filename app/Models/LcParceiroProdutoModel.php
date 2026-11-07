<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class LcParceiroProdutoModel extends Model
{
    protected $table            = 'tb_lc_parceiro_produtos';
    protected $primaryKey       = 'lpp_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;

    protected $allowedFields    = [
        'lpp_parceiro_id',
        'lpp_codigo',
        'lpp_descricao',
        'lpp_ativo',
        'lpp_criado_em',
        'lpp_atualizado_em',
    ];

    /**
     * Retorna a lista de produtos cadastrados do parceiro
     */
    public function getByParceiro(int $parceiroId, bool $apenasAtivos = true): array
    {
        $builder = $this->where('lpp_parceiro_id', $parceiroId);
        if ($apenasAtivos) {
            $builder->where('lpp_ativo', 1);
        }
        return $builder->orderBy('lpp_descricao', 'ASC')->findAll();
    }

    /**
     * Localiza um produto pelo código específico do parceiro
     */
    public function findByCodigo(int $parceiroId, string $codigo): ?array
    {
        return $this->where('lpp_parceiro_id', $parceiroId)
            ->where('lpp_codigo', trim($codigo))
            ->where('lpp_ativo', 1)
            ->first();
    }
}
