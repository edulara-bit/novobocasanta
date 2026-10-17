<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;

class CheckSchema extends BaseCommand
{
    protected $group       = 'BocaSanta';
    protected $name        = 'bocasanta:check-schema';
    protected $description = 'Verifica e atualiza schemas necessários para o Novo Boca Santa (Alias para update-schema).';

    public function run(array $params)
    {
        $updateCommand = new UpdateSchema($this->logger, $this->commands);
        return $updateCommand->run($params);
    }
}
