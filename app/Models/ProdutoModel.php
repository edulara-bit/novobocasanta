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
        $builder = $this->select('tb_produtos.*, tb_parceiros.par_nome, tb_parceiros.par_apelido, tb_parceiros.par_imagem, tb_parceiros.par_whatsapp, tb_parceiros.par_telefone, tb_parceiros.par_telefone2, tb_parceiros.par_telefone3, tb_parceiros.par_email, tb_cidades.cid_nome, tb_cidades.cid_url, tb_categorias.cat_titulo, tb_categorias.cat_url, ft.fot_imagem, ft.fot_thumb, ft.fot_parceiro')
            ->join('tb_parceiros', 'tb_produtos.pro_parceiro = tb_parceiros.par_id', 'left')
            ->join('tb_cidades', 'tb_produtos.pro_cidade = tb_cidades.cid_id', 'left')
            ->join('tb_categorias', 'tb_produtos.pro_categoria = tb_categorias.cat_id', 'left')
            ->join('tb_fotos_produtos as ft', 'tb_produtos.pro_id = ft.fot_produto AND (ft.fot_ordem = 0 OR ft.fot_ordem = 1)', 'left');

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

            // Exclui parceiros marcados como teste e exceções expressas (Packer, Loja do Nilo)
            if ($this->db->fieldExists('linefast_is_test', 'tb_parceiros')) {
                $builder->where('(tb_parceiros.linefast_is_test = 0 OR tb_parceiros.linefast_is_test IS NULL)');
            }
            $builder->where('tb_produtos.pro_parceiro !=', 970);
            $builder->notLike('tb_parceiros.par_nome', 'packer');
            $builder->notLike('tb_parceiros.par_nome', 'nilo');
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
            case 'random':
            case 'randomico':
            case 'rand':
                $builder->orderBy('RAND()');
                break;
            default:
                $builder->orderBy('tb_produtos.pro_destaque DESC, tb_produtos.pro_ordem ASC, tb_produtos.pro_id DESC');
                break;
        }

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        $builder->groupBy('tb_produtos.pro_id');

        return $builder->find();
    }

    /**
     * Conta o total de ofertas para paginação com suporte a filtros
     */
    public function countOfertas(array $params = []): int
    {
        $builder = $this->db->table('tb_produtos')
            ->join('tb_parceiros', 'tb_produtos.pro_parceiro = tb_parceiros.par_id', 'left');

        if (!empty($params['cidade_id'])) {
            $builder->where('tb_produtos.pro_cidade', (int)$params['cidade_id']);
        }

        if (!empty($params['parceiro_id'])) {
            $builder->where('tb_produtos.pro_parceiro', (int)$params['parceiro_id']);
        }

        if (!empty($params['categoria_id'])) {
            $builder->groupStart()
                ->where('tb_produtos.pro_categoria', (int)$params['categoria_id'])
                ->orWhere('tb_produtos.pro_categoria_pai', (int)$params['categoria_id'])
                ->groupEnd();
        }

        if (!empty($params['busca'])) {
            $termo = trim((string)$params['busca']);
            $builder->groupStart()
                ->like('tb_produtos.pro_titulo', $termo)
                ->orLike('tb_produtos.pro_descricao', $termo)
                ->orLike('tb_parceiros.par_nome', $termo)
                ->groupEnd();
        }

        return (int)$builder->countAllResults();
    }

    /**
     * Busca uma oferta única por ID ou Slug com dados completos do parceiro e categoria
     * Inclui suporte aos slugs históricos da tabela tb_slug e pro_slug2
     */
    public function getDetalhesOferta(int|string $idOrSlug): ?Produto
    {
        $selectCols = 'tb_produtos.*, tb_parceiros.par_nome, tb_parceiros.par_apelido, tb_parceiros.par_endereco, tb_parceiros.par_numero, tb_parceiros.par_complemento, tb_parceiros.par_bairro, tb_parceiros.par_cep, tb_parceiros.par_telefone, tb_parceiros.par_telefone2, tb_parceiros.par_telefone3, tb_parceiros.par_whatsapp, tb_parceiros.par_email, tb_parceiros.par_descricao as par_descricao_empresa, tb_parceiros.par_imagem, tb_parceiros.par_site, tb_parceiros.par_facebook, tb_parceiros.par_maps, tb_cidades.cid_nome, tb_cidades.cid_url, tb_categorias.cat_titulo, tb_categorias.cat_url, ft.fot_imagem, ft.fot_thumb, ft.fot_parceiro';

        $builder = $this->select($selectCols)
            ->join('tb_parceiros', 'tb_produtos.pro_parceiro = tb_parceiros.par_id', 'left')
            ->join('tb_cidades', 'tb_produtos.pro_cidade = tb_cidades.cid_id', 'left')
            ->join('tb_categorias', 'tb_produtos.pro_categoria = tb_categorias.cat_id', 'left')
            ->join('tb_fotos_produtos as ft', 'tb_produtos.pro_id = ft.fot_produto AND (ft.fot_ordem = 0 OR ft.fot_ordem = 1)', 'left');

        if (is_numeric($idOrSlug)) {
            $builder->where('tb_produtos.pro_id', (int)$idOrSlug);
        } else {
            $variations = $this->getSlugVariations((string)$idOrSlug);
            $builder->groupStart();
            $first = true;
            foreach ($variations as $v) {
                if ($first) {
                    $builder->where('tb_produtos.pro_slug', $v);
                    $first = false;
                } else {
                    $builder->orWhere('tb_produtos.pro_slug', $v);
                }
            }

            if ($this->db->fieldExists('pro_slug2', 'tb_produtos')) {
                foreach ($variations as $v) {
                    $builder->orWhere('tb_produtos.pro_slug2', $v)
                        ->orLike('tb_produtos.pro_slug2', '/' . $v, 'before');
                }
            }
            $builder->groupEnd();
        }

        $produto = $builder->first();

        // Se não encontrou diretamente em tb_produtos e for string, verifica no histórico tb_slug
        if (!$produto && !is_numeric($idOrSlug) && $this->db->tableExists('tb_slug')) {
            $variations = $this->getSlugVariations((string)$idOrSlug);
            if (!empty($variations)) {
                $slugBuilder = $this->db->table('tb_slug')->groupStart();
                $first = true;
                foreach ($variations as $v) {
                    if ($first) {
                        $slugBuilder->where('slu_slug', $v);
                        $first = false;
                    } else {
                        $slugBuilder->orWhere('slu_slug', $v);
                    }
                }
                $slugRow = $slugBuilder->groupEnd()
                    ->where('slu_produto >', 0)
                    ->orderBy('slu_id', 'DESC')
                    ->get()
                    ->getRowArray();

                if (!empty($slugRow['slu_produto'])) {
                    $produto = $this->select($selectCols)
                        ->join('tb_parceiros', 'tb_produtos.pro_parceiro = tb_parceiros.par_id', 'left')
                        ->join('tb_cidades', 'tb_produtos.pro_cidade = tb_cidades.cid_id', 'left')
                        ->join('tb_categorias', 'tb_produtos.pro_categoria = tb_categorias.cat_id', 'left')
                        ->join('tb_fotos_produtos as ft', 'tb_produtos.pro_id = ft.fot_produto AND (ft.fot_ordem = 0 OR ft.fot_ordem = 1)', 'left')
                        ->where('tb_produtos.pro_id', (int)$slugRow['slu_produto'])
                        ->first();
                }
            }
        }

        if ($produto) {
            // Incrementa contador de visualizações
            $this->where('pro_id', $produto->pro_id)->increment('pro_visitas', 1);
        }

        return $produto;
    }

    /**
     * Consulta a tabela legada tb_slug para redirecionamento de ofertas antigas/excluídas
     */
    public function getSlugHistorico(string $slug): ?array
    {
        if (!$this->db->tableExists('tb_slug')) {
            return null;
        }

        $variations = $this->getSlugVariations($slug);
        if (empty($variations)) {
            return null;
        }

        $builder = $this->db->table('tb_slug')->groupStart();
        $first = true;
        foreach ($variations as $v) {
            if ($first) {
                $builder->where('slu_slug', $v);
                $first = false;
            } else {
                $builder->orWhere('slu_slug', $v);
            }
        }
        $row = $builder->groupEnd()
            ->orderBy('slu_id', 'DESC')
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    /**
     * Gera lista de variações normalizadas de um slug para busca flexível (com/sem acento, url-decoded, traço final)
     *
     * @return string[]
     */
    protected function getSlugVariations(string $slug): array
    {
        $slug = trim($slug);
        if ($slug === '') {
            return [];
        }

        helper('text');
        $decoded = urldecode($slug);
        $rawDecoded = rawurldecode($slug);
        $unaccented = function_exists('convert_accented_characters') ? convert_accented_characters($slug) : $slug;
        $unaccentedDecoded = function_exists('convert_accented_characters') ? convert_accented_characters($decoded) : $decoded;

        $items = [
            $slug,
            rtrim($slug, '-'),
            rtrim($slug, '-') . '-',
            $decoded,
            rtrim($decoded, '-'),
            $rawDecoded,
            $unaccented,
            rtrim($unaccented, '-'),
            $unaccentedDecoded,
            rtrim($unaccentedDecoded, '-'),
            str_replace([' ', '+'], '-', $slug),
            str_replace([' ', '+'], '-', $decoded),
            str_replace([' ', '+'], '-', $unaccentedDecoded),
        ];

        return array_values(array_unique(array_filter($items)));
    }

    /**
     * Retorna todas as fotos cadastradas da oferta em tb_fotos_produtos
     */
    public function getFotosOferta(int $proId, int $parceiroId = 0): array
    {
        if (!$this->db->tableExists('tb_fotos_produtos')) {
            return [];
        }

        $rows = $this->db->table('tb_fotos_produtos')
            ->where('fot_produto', $proId)
            ->orderBy('fot_ordem', 'ASC')
            ->orderBy('fot_id', 'ASC')
            ->get()
            ->getResultArray();

        $fotos = [];
        foreach ($rows as $r) {
            $img = $r['fot_imagem'] ?? $r['fot_thumb'] ?? '';
            if (empty($img)) {
                continue;
            }
            $pId = !empty($r['fot_parceiro']) ? $r['fot_parceiro'] : $parceiroId;

            if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
                $url = $img;
            } elseif (!empty($pId) && file_exists(FCPATH . 'upimg/produtos/' . $pId . '/' . $img)) {
                $url = base_url('upimg/produtos/' . $pId . '/' . $img);
            } elseif (file_exists(FCPATH . 'upimg/produtos/' . $img)) {
                $url = base_url('upimg/produtos/' . $img);
            } elseif (!empty($pId)) {
                $url = "https://www.bocasantaofertas.com.br/upimg/produtos/{$pId}/{$img}";
            } else {
                $url = "https://www.bocasantaofertas.com.br/upimg/produtos/{$img}";
            }

            $fotos[] = $url;
        }

        if (empty($fotos)) {
            $prod = $this->select('pro_foto, pro_parceiro')->find($proId);
            if ($prod && !empty($prod->pro_foto)) {
                $img = $prod->pro_foto;
                $pId = !empty($prod->pro_parceiro) ? $prod->pro_parceiro : $parceiroId;
                if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
                    $fotos[] = $img;
                } elseif (!empty($pId) && file_exists(FCPATH . 'upimg/produtos/' . $pId . '/' . $img)) {
                    $fotos[] = base_url('upimg/produtos/' . $pId . '/' . $img);
                } elseif (file_exists(FCPATH . 'upimg/produtos/' . $img)) {
                    $fotos[] = base_url('upimg/produtos/' . $img);
                } elseif (!empty($pId)) {
                    $fotos[] = "https://www.bocasantaofertas.com.br/upimg/produtos/{$pId}/{$img}";
                } else {
                    $fotos[] = "https://www.bocasantaofertas.com.br/upimg/produtos/{$img}";
                }
            }
        }

        return array_values(array_unique($fotos));
    }

    /**
     * Retorna ofertas relacionadas da mesma categoria ou cidade
     */
    public function getRelacionadas(Produto $produto, int $limit = 4): array
    {
        return $this->select('tb_produtos.*, tb_parceiros.par_nome, tb_parceiros.par_whatsapp, tb_parceiros.par_telefone, tb_parceiros.par_telefone2, tb_parceiros.par_telefone3, tb_cidades.cid_url, tb_categorias.cat_url, ft.fot_imagem, ft.fot_thumb, ft.fot_parceiro')
            ->join('tb_parceiros', 'tb_produtos.pro_parceiro = tb_parceiros.par_id', 'left')
            ->join('tb_cidades', 'tb_produtos.pro_cidade = tb_cidades.cid_id', 'left')
            ->join('tb_categorias', 'tb_produtos.pro_categoria = tb_categorias.cat_id', 'left')
            ->join('tb_fotos_produtos as ft', 'tb_produtos.pro_id = ft.fot_produto AND (ft.fot_ordem = 0 OR ft.fot_ordem = 1)', 'left')
            ->where('tb_produtos.pro_id !=', $produto->pro_id)
            ->where('tb_produtos.pro_categoria', $produto->pro_categoria)
            ->orderBy('RAND()')
            ->limit($limit)
            ->find();
    }
}
