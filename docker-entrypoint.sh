#!/bin/bash
set -e

# Se existirem variáveis de ambiente passadas pelo Docker/Easypanel, garante que estejam no .env
if [ ! -f /var/www/html/.env ]; then
    echo "Gerando .env a partir das variaveis do ambiente..."
    touch /var/www/html/.env
    
    # Adiciona todas as variaveis para o CodeIgniter 4 ler
    env | while IFS='=' read -r name value ; do
        if [[ ! -z "$name" ]]; then
            echo "$name=\"$value\"" >> /var/www/html/.env
        fi
    done
    chown www-data:www-data /var/www/html/.env
fi

# Garante permissões em pastas de escrita
chown -R www-data:www-data /var/www/html/writable /var/www/html/public/upimg || true
chmod -R 775 /var/www/html/writable /var/www/html/public/upimg || true

# Executa migrações automáticas de schema do banco se necessário
if [ -f /var/www/html/schema_updates.sql ]; then
    php -r '
        require "/var/www/html/vendor/autoload.php";
        try {
            $app = \Config\Services::codeigniter();
            $app->initialize();
            $db = \Config\Database::connect();
            $sql = file_get_contents("/var/www/html/schema_updates.sql");
            $queries = explode(";", $sql);
            foreach ($queries as $q) {
                $q = trim($q);
                if (!empty($q)) {
                    try { $db->query($q); } catch (\Throwable $e) {}
                }
            }
            echo "Schema updates applied.\n";
        } catch (\Throwable $e) {
            echo "Schema auto-update skipped: " . $e->getMessage() . "\n";
        }
    ' || true
fi

exec "$@"
