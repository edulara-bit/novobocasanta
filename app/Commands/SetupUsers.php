<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class SetupUsers extends BaseCommand
{
    protected $group       = 'BocaSanta';
    protected $name        = 'bocasanta:setup-users';
    protected $description = 'Configura usuários locais de teste para Admin e Parceiro.';

    public function run(array $params)
    {
        $db = Database::connect();
        
        CLI::write('Configurando usuários no ambiente local...', 'yellow');

        // 1. Admin: login 'eduardo', senha 'blueball'
        $adminTable = 'tb_administradores';
        $adminFields = $db->getFieldNames($adminTable);

        $adminExists = $db->table($adminTable)
            ->groupStart()
                ->where('adm_login', 'eduardo')
                ->orWhere('adm_email', 'eduardo')
                ->orWhere('adm_email', 'contato@edulara.com.br')
            ->groupEnd()
            ->get()->getRowArray();

        $adminData = [
            'adm_nome'         => 'Eduardo Lara',
            'adm_login'        => 'eduardo',
            'adm_email'        => 'contato@edulara.com.br',
            'adm_senha'        => md5('blueball'),
            'adm_nivel_acesso' => 1,
            'adm_status'       => 'ativo',
        ];

        $adminInsert = array_intersect_key($adminData, array_flip($adminFields));

        if ($adminExists) {
            $db->table($adminTable)->where('adm_id', $adminExists['adm_id'])->update($adminInsert);
            CLI::write("✓ Usuário Admin atualizado com sucesso (ID: {$adminExists['adm_id']}). Login: eduardo | Senha: md5(blueball)", 'green');
        } else {
            $db->table($adminTable)->insert($adminInsert);
            $newId = $db->insertID();
            CLI::write("✓ Usuário Admin criado com sucesso (ID: {$newId}). Login: eduardo | Senha: md5(blueball)", 'green');
        }

        // 2. Parceiro: email 'contato@edulara.com.br', senha 'blueball'
        $parcTable = 'tb_parceiros';

        $parcExists = $db->table($parcTable)
            ->where('par_email', 'contato@edulara.com.br')
            ->get()->getRowArray();

        if ($parcExists) {
            $db->table($parcTable)->where('par_id', $parcExists['par_id'])->update([
                'par_senha' => md5('blueball'),
                'par_ativo' => '1',
            ]);
            CLI::write("✓ Parceiro atualizado com sucesso (ID: {$parcExists['par_id']}, Nome: {$parcExists['par_nome']}). E-mail: contato@edulara.com.br | Senha: md5(blueball)", 'green');
        } else {
            // Find the first active partner to update
            $firstParc = $db->table($parcTable)->where('par_ativo', '1')->get()->getRowArray();
            if ($firstParc) {
                $db->table($parcTable)->where('par_id', $firstParc['par_id'])->update([
                    'par_email' => 'contato@edulara.com.br',
                    'par_senha' => md5('blueball'),
                ]);
                CLI::write("✓ Parceiro ID {$firstParc['par_id']} ({$firstParc['par_nome']}) atualizado com E-mail: contato@edulara.com.br | Senha: md5(blueball)", 'green');
            } else {
                CLI::write("Nenhum parceiro encontrado para atualizar.", 'red');
            }
        }

        CLI::write("\nConcluído com sucesso!", 'green');
    }
}
