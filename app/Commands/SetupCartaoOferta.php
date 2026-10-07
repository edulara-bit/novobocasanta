<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SetupCartaoOferta extends BaseCommand
{
    protected $group       = 'BocaSanta';
    protected $name        = 'bocasanta:setup-cartao';
    protected $description = 'Adiciona suporte a vinculacao de cartao fidelidade com ofertas';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        try {
            $db->query("ALTER TABLE tb_produtos ADD COLUMN pro_cartao INT(11) DEFAULT 0 AFTER pro_melhor_preco");
            CLI::write("Coluna pro_cartao adicionada em tb_produtos.", "green");
        } catch (\Throwable $e) {
            CLI::write("Coluna pro_cartao já existe em tb_produtos.", "yellow");
        }

        try {
            $db->query("ALTER TABLE tb_cartoes ADD COLUMN car_oferta INT(11) DEFAULT 0");
            CLI::write("Coluna car_oferta adicionada em tb_cartoes.", "green");
        } catch (\Throwable $e) {
            CLI::write("Coluna car_oferta já existe em tb_cartoes.", "yellow");
        }
    }
}
