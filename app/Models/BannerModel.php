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
        return $this->where('ban_status !=', 'inativo')
            ->orderBy('ban_ordem', 'ASC')
            ->orderBy('ban_id', 'DESC')
            ->findAll();
    }
}
