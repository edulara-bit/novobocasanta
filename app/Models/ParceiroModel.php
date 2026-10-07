<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\Parceiro;
use CodeIgniter\Model;

class ParceiroModel extends Model
{
    protected $table            = 'tb_parceiros';
    protected $primaryKey       = 'par_id';
    protected $useAutoIncrement = true;
    protected $returnType       = Parceiro::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;

    protected $allowedFields    = [
        'par_estado', 'par_nome', 'par_plano', 'par_datacadastro', 'par_imagem',
        'par_endereco', 'par_numero', 'par_complemento', 'par_alteracoes',
        'par_visitas', 'par_cep', 'par_telefone', 'par_telefone2', 'par_telefone3',
        'par_senha', 'par_bairro', 'par_cidade', 'par_razao', 'par_cnpj', 'par_site',
        'par_facebook', 'par_twitter', 'par_google', 'par_email', 'par_alteracao',
        'par_descricao', 'par_vendedor', 'par_tipo', 'par_ie', 'par_ativo',
        'par_apelido', 'par_acesso', 'par_whatsapp', 'par_maps', 'par_fidelidade',
        'linefast_partner_id', 'linefast_store_id', 'linefast_ativo', 'linefast_sync_at',
    ];

    /**
     * Busca parceiros ativos com suporte a filtros de cidade e busca textual
     */
    public function getParceiros(array $params = [], int $limit = 20, int $offset = 0): array
    {
        $builder = $this->select('tb_parceiros.*, tb_cidades.cid_nome, tb_cidades.cid_url')
            ->join('tb_cidades', 'tb_parceiros.par_cidade = tb_cidades.cid_id', 'left')
            ->where('tb_parceiros.par_ativo !=', '0');

        if (!empty($params['cidade_id'])) {
            $builder->where('tb_parceiros.par_cidade', (int)$params['cidade_id']);
        }

        if (!empty($params['cidade_slug'])) {
            $builder->where('tb_cidades.cid_url', $params['cidade_slug']);
        }

        if (!empty($params['somente_linefast']) && $this->db->fieldExists('linefast_ativo', 'tb_parceiros')) {
            $builder->where('tb_parceiros.linefast_ativo', 1);
        }

        if (!empty($params['busca'])) {
            $termo = trim($params['busca']);
            $builder->groupStart()
                ->like('tb_parceiros.par_nome', $termo)
                ->orLike('tb_parceiros.par_descricao', $termo)
                ->orLike('tb_parceiros.par_apelido', $termo)
                ->groupEnd();
        }

        if ($this->db->fieldExists('par_ordem', 'tb_parceiros')) {
            $builder->orderBy('tb_parceiros.par_ordem', 'ASC');
        }
        $builder->orderBy('tb_parceiros.par_nome', 'ASC');

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        return $builder->find();
    }
}
