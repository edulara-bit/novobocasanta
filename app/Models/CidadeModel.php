<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class CidadeModel extends Model
{
    protected $table            = 'tb_cidades';
    protected $primaryKey       = 'cid_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = false;

    /**
     * Retorna todas as cidades ativas
     */
    public function getCidades(): array
    {
        return $this->orderBy('cid_nome', 'ASC')->findAll();
    }

    /**
     * Retorna as principais cidades baseadas no volume de parceiros cadastrados
     */
    public function getPrincipaisCidades(int $limit = 4): array
    {
        return $this->select('tb_cidades.*, COUNT(tb_parceiros.par_id) as total_parceiros')
            ->join('tb_parceiros', 'tb_cidades.cid_id = tb_parceiros.par_cidade AND tb_parceiros.par_ativo != "0"', 'left')
            ->groupBy('tb_cidades.cid_id')
            ->orderBy('total_parceiros', 'DESC')
            ->orderBy('tb_cidades.cid_nome', 'ASC')
            ->limit($limit)
            ->find();
    }

    /**
     * Encontra cidade por URL slug
     */
    public function getBySlug(string $slug): ?array
    {
        return $this->where('cid_url', $slug)->first();
    }
}
