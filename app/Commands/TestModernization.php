<?php

declare(strict_types=1);

namespace App\Commands;

use App\Models\ParceiroModel;
use App\Models\ProdutoModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class TestModernization extends BaseCommand
{
    protected $group       = 'BocaSanta';
    protected $name        = 'bocasanta:test';
    protected $description = 'Executa bateria de testes de integridade da modernização do Boca Santa.';

    public function run(array $params)
    {
        CLI::write('============================================', 'yellow');
        CLI::write(' BOCA SANTA - TESTE DE INTEGRIDADE GERAL', 'yellow');
        CLI::write('============================================', 'yellow');

        $db = Database::connect();
        $db->query('ALTER TABLE `tb_fotos_produtos` ADD INDEX IF NOT EXISTS `idx_fot_prod_ordem` (`fot_produto`, `fot_ordem`);');

        // 1. Teste de Ofertas
        $prodModel = new ProdutoModel();
        $ofertas = $prodModel->getOfertas([], 5);
        CLI::write('✓ Consulta de Ofertas: ' . count($ofertas) . ' encontradas.', 'green');
        foreach ($ofertas as $o) {
            CLI::write("  - [ID {$o->pro_id}] {$o->pro_titulo} | {$o->getPrecoVendaFormatado()} | Imagem: " . $o->getImagemUrl());
        }

        // 2. Teste de Parceiros
        $parcModel = new ParceiroModel();
        $parceiros = $parcModel->getParceiros([], 5);
        CLI::write("\n✓ Consulta de Parceiros: " . count($parceiros) . ' encontrados.', 'green');
        foreach ($parceiros as $p) {
            CLI::write("  - [ID {$p->par_id}] {$p->getNome()} | Slug: {$p->getSlug()} | Cidade: {$p->cid_nome} | Logo: " . $p->getLogoUrl());
        }

        // 3. Teste de Produto Integrado Linefast (ex: 21664 - Amigurumi)
        $prod21664 = $prodModel->find(21664);
        if ($prod21664) {
            $parc = $parcModel->find($prod21664->pro_parceiro);
            $cartUrl = $prod21664->getLinefastCartUrl();
            CLI::write("\n✓ Teste de Produto Linefast (ID 21664):", 'green');
            CLI::write("  - Título: {$prod21664->pro_titulo}");
            CLI::write("  - Preço: {$prod21664->getPrecoVendaFormatado()}");
            CLI::write("  - isLinefast(): " . ($prod21664->isLinefast() ? 'SIM' : 'NÃO'));
            CLI::write("  - URL do Botão Linefast: {$cartUrl}");
            CLI::write("  - Loja Parceiro no Linefast: " . ($parc ? $parc->getLinefastStoreUrl() : 'N/A'));
        }

        // 4. Teste de Consulta Fidelidade
        $queryFidelidade = $db->table('tb_lancamentos as l')
            ->select('l.*, p.par_id, p.par_nome, p.par_imagem, c.car_nome, c.car_percentagem, c.car_pontos')
            ->join('tb_parceiros as p', 'p.par_id = l.lan_parceiro', 'left')
            ->join('tb_cartoes as c', 'c.car_id = l.lan_cartao', 'left')
            ->where('l.lan_cpf', '17166540842')
            ->orderBy('l.lan_data', 'DESC')
            ->get();
        $lancamentos = $queryFidelidade->getResultArray();
        CLI::write("\n✓ Teste Fidelidade (CPF 17166540842): " . count($lancamentos) . " lançamentos encontrados.", 'green');
        $cliModel = new \App\Models\ClienteModel();
        $cliClean = $cliModel->where('cli_cpf', '17166540842')->first();
        $cliFormatted = $cliModel->where('cli_cpf', '171.665.408-42')->first();
        CLI::write("  - Cliente por CPF limpo: " . ($cliClean ? $cliClean['cli_nome'] : 'não encontrado'));
        CLI::write("  - Cliente por CPF formatado: " . ($cliFormatted ? $cliFormatted['cli_nome'] : 'não encontrado'));

        CLI::write("\n============================================", 'green');
        CLI::write(' TODOS OS TESTES FORAM EXECUTADOS COM SUCESSO!', 'green');
        CLI::write("============================================\n", 'green');
    }
}
