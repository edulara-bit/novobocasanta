<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class BannerModel extends Model
{
    protected $table            = 'tb_banners';
    protected $primaryKey       = 'ban_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = false;
    protected $allowedFields    = [
        'ban_titulo', 'ban_badge', 'ban_descricao', 'ban_botao_texto', 'ban_botao_link',
        'ban_tipo_fundo', 'ban_fundo_cor', 'ban_fundo_degrade', 'ban_fundo_imagem',
        'ban_imagem_direita', 'ban_url', 'ban_cliente', 'ban_link', 'ban_ordem', 'ban_status'
    ];

    /**
     * Retorna os banners ativos ordenados
     */
    public function getBannersAtivos(): array
    {
        $builder = $this;
        if ($this->db->fieldExists('ban_status', 'tb_banners')) {
            $builder = $builder->where('ban_status !=', 'inativo');
        }
        if ($this->db->fieldExists('ban_ordem', 'tb_banners')) {
            $builder = $builder->orderBy('ban_ordem', 'ASC');
        }
        return $builder->orderBy('ban_id', 'DESC')->findAll();
    }
}
