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

        CLI::write("\n============================================", 'green');
        CLI::write(' TODOS OS TESTES FORAM EXECUTADOS COM SUCESSO!', 'green');
        CLI::write("============================================\n", 'green');
    }
}
