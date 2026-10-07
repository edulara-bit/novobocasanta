<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\Produto;
use CodeIgniter\Model;

class ProdutoModel extends Model
{
    protected $table            = 'tb_produtos';
    protected $primaryKey       = 'pro_id';
    protected $useAutoIncrement = true;
    protected $returnType       = Produto::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;

    protected $allowedFields    = [
        'pro_titulo', 'pro_parceiro', 'pro_marca', 'pro_unidade', 'pro_preco',
        'pro_precodesconto', 'pro_apartirde', 'pro_descricao', 'pro_caracteristicas',
        'pro_categoria', 'pro_categoria_pai', 'pro_cidade', 'pro_destaque', 'pro_melhor_preco', 'pro_cartao', 'pro_ordem',
        'pro_slug', 'pro_foto', 'pro_thumb', 'pro_data', 'pro_comprar', 'pro_video',
        'pro_visitas', 'pro_compartilhado', 'origem', 'linefast_product_id',
        'linefast_sku', 'linefast_stock', 'linefast_buy_url', 'linefast_sync_at',
    ];

    /**
     * Busca ofertas ativas com suporte a filtros de cidade, categoria, destaque, busca textual e paginação
     */
    public function getOfertas(array $params = [], int $limit = 12, int $offset = 0): array
    {
        $builder = $this->select('tb_produtos.*, tb_parceiros.par_nome, tb_parceiros.par_apelido, tb_parceiros.par_imagem, tb_parceiros.par_whatsapp, tb_parceiros.par_telefone, tb_parceiros.par_email, tb_cidades.cid_nome, tb_cidades.cid_url, tb_categorias.cat_titulo, tb_categorias.cat_url, ft.fot_imagem, ft.fot_thumb, ft.fot_parceiro')
            ->join('tb_parceiros', 'tb_produtos.pro_parceiro = tb_parceiros.par_id', 'left')
            ->join('tb_cidades', 'tb_produtos.pro_cidade = tb_cidades.cid_id', 'left')
            ->join('tb_categorias', 'tb_produtos.pro_categoria = tb_categorias.cat_id', 'left')
            ->join('tb_fotos_produtos as ft', 'tb_produtos.pro_id = ft.fot_produto AND ft.fot_ordem = 0', 'left');

        if (!empty($params['cidade_id'])) {
            $builder->where('tb_produtos.pro_cidade', (int)$params['cidade_id']);
        }

        if (!empty($params['cidade_slug'])) {
            $builder->where('tb_cidades.cid_url', $params['cidade_slug']);
        }

        if (!empty($params['categoria_id'])) {
            $builder->groupStart()
                ->where('tb_produtos.pro_categoria', (int)$params['categoria_id'])
                ->orWhere('tb_produtos.pro_categoria_pai', (int)$params['categoria_id'])
                ->groupEnd();
        }

        if (!empty($params['categoria_slug'])) {
            $builder->where('tb_categorias.cat_url', $params['categoria_slug']);
        }

        if (!empty($params['parceiro_id'])) {
            $builder->where('tb_produtos.pro_parceiro', (int)$params['parceiro_id']);
        }

        if (!empty($params['destaque'])) {
            $builder->where('tb_produtos.pro_destaque', 1);
        }

        if (!empty($params['somente_desconto'])) {
            $builder->where('tb_produtos.pro_precodesconto >', 0);
        }

        if (!empty($params['somente_linefast'])) {
            if ($this->db->fieldExists('origem', 'tb_produtos')) {
                $builder->groupStart()
                    ->where('tb_produtos.origem', 'linefast')
                    ->orWhere('tb_produtos.linefast_product_id IS NOT NULL')
                    ->groupEnd();
            }
        }

        if (!empty($params['busca'])) {
            $termo = trim($params['busca']);
            $builder->groupStart()
                ->like('tb_produtos.pro_titulo', $termo)
                ->orLike('tb_produtos.pro_descricao', $termo)
                ->orLike('tb_parceiros.par_nome', $termo)
                ->groupEnd();
        }

        // Ordenação
        $ordem = $params['ordem'] ?? 'relevancia';
        switch ($ordem) {
            case 'menor_preco':
                $builder->orderBy('IF(tb_produtos.pro_precodesconto > 0, tb_produtos.pro_precodesconto, tb_produtos.pro_preco) ASC');
                break;
            case 'maior_preco':
                $builder->orderBy('IF(tb_produtos.pro_precodesconto > 0, tb_produtos.pro_precodesconto, tb_produtos.pro_preco) DESC');
                break;
            case 'mais_recentes':
                $builder->orderBy('tb_produtos.pro_id', 'DESC');
                break;
            case 'mais_vistos':
                $builder->orderBy('tb_produtos.pro_visitas', 'DESC');
                break;
            default:
                $builder->orderBy('tb_produtos.pro_destaque DESC, tb_produtos.pro_ordem ASC, tb_produtos.pro_id DESC');
                break;
        }

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        return $builder->find();
    }

    /**
     * Busca uma oferta única por ID ou Slug com dados completos do parceiro e categoria
     */
    public function getDetalhesOferta(int|string $idOrSlug): ?Produto
    {
        $builder = $this->select('tb_produtos.*, tb_parceiros.par_nome, tb_parceiros.par_apelido, tb_parceiros.par_endereco, tb_parceiros.par_numero, tb_parceiros.par_complemento, tb_parceiros.par_bairro, tb_parceiros.par_cep, tb_parceiros.par_telefone, tb_parceiros.par_telefone2, tb_parceiros.par_telefone3, tb_parceiros.par_whatsapp, tb_parceiros.par_email, tb_parceiros.par_descricao as par_descricao_empresa, tb_parceiros.par_imagem, tb_parceiros.par_site, tb_parceiros.par_facebook, tb_parceiros.par_maps, tb_cidades.cid_nome, tb_cidades.cid_url, tb_categorias.cat_titulo, tb_categorias.cat_url, ft.fot_imagem, ft.fot_thumb, ft.fot_parceiro')
            ->join('tb_parceiros', 'tb_produtos.pro_parceiro = tb_parceiros.par_id', 'left')
            ->join('tb_cidades', 'tb_produtos.pro_cidade = tb_cidades.cid_id', 'left')
            ->join('tb_categorias', 'tb_produtos.pro_categoria = tb_categorias.cat_id', 'left')
            ->join('tb_fotos_produtos as ft', 'tb_produtos.pro_id = ft.fot_produto AND ft.fot_ordem = 0', 'left');

        if (is_numeric($idOrSlug)) {
            $builder->where('tb_produtos.pro_id', (int)$idOrSlug);
        } else {
            $builder->where('tb_produtos.pro_slug', $idOrSlug);
        }

        $produto = $builder->first();
        if ($produto) {
            // Incrementa contador de visualizações
            $this->where('pro_id', $produto->pro_id)->increment('pro_visitas', 1);
        }

        return $produto;
    }

    /**
     * Retorna ofertas relacionadas da mesma categoria ou cidade
     */
    public function getRelacionadas(Produto $produto, int $limit = 4): array
    {
        return $this->select('tb_produtos.*, tb_parceiros.par_nome, tb_cidades.cid_url, tb_categorias.cat_url, ft.fot_imagem, ft.fot_thumb, ft.fot_parceiro')
            ->join('tb_parceiros', 'tb_produtos.pro_parceiro = tb_parceiros.par_id', 'left')
            ->join('tb_cidades', 'tb_produtos.pro_cidade = tb_cidades.cid_id', 'left')
            ->join('tb_categorias', 'tb_produtos.pro_categoria = tb_categorias.cat_id', 'left')
            ->join('tb_fotos_produtos as ft', 'tb_produtos.pro_id = ft.fot_produto AND ft.fot_ordem = 0', 'left')
            ->where('tb_produtos.pro_id !=', $produto->pro_id)
            ->where('tb_produtos.pro_categoria', $produto->pro_categoria)
            ->orderBy('tb_produtos.pro_destaque DESC, tb_produtos.pro_id DESC')
            ->limit($limit)
            ->find();
    }
}
