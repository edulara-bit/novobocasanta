<?php

declare(strict_types=1);

namespace App\Services;

use Config\Database;
use Config\Lgpd as LgpdConfig;

class LgpdService
{
    protected LgpdConfig $config;
    protected $db;

    public function __construct(?LgpdConfig $config = null)
    {
        $this->config = $config ?? config('Lgpd');
        $this->db = Database::connect();
    }

    /**
     * Salva o consentimento de cookies e preferências do visitante (LGPD Art. 7, I)
     */
    public function saveConsent(string $visitorId, array $preferences, ?int $userId = null, string $ip = '', string $userAgent = ''): bool
    {
        $ipHash = hash('sha256', $ip . 'bocasanta_lgpd_salt');

        $data = [
            'visitor_id'         => $visitorId,
            'ip_address'         => substr($ip, 0, 45),
            'user_id'            => $userId,
            'ip_hash'            => $ipHash,
            'consent_essential'  => 1, // Sempre obrigatório
            'consent_analytics'  => !empty($preferences['analytics']) ? 1 : 0,
            'consent_marketing'  => !empty($preferences['marketing']) ? 1 : 0,
            'user_agent'         => substr($userAgent, 0, 255),
            'updated_at'         => date('Y-m-d H:i:s'),
        ];

        $existing = $this->db->table('tb_lgpd_consents')->where('visitor_id', $visitorId)->get()->getRow();

        if ($existing) {
            return $this->db->table('tb_lgpd_consents')->where('visitor_id', $visitorId)->update($data);
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->table('tb_lgpd_consents')->insert($data);
    }

    /**
     * Registra solicitação formal de direitos do titular (Art. 18 da LGPD)
     */
    public function createDataSubjectRequest(array $requestData): int
    {
        $data = [
            'user_id'         => $requestData['user_id'] ?? null,
            'nome'            => $requestData['nome'] ?? 'Anônimo',
            'email'           => $requestData['email'] ?? '',
            'tipo_requisicao' => $requestData['tipo'] ?? 'exportar_dados',
            'status'          => 'pendente',
            'observacoes'     => $requestData['mensagem'] ?? null,
            'created_at'      => date('Y-m-d H:i:s'),
        ];

        $this->db->table('tb_lgpd_requests')->insert($data);
        return (int) $this->db->insertID();
    }

    /**
     * Exporta todos os dados do titular em formato estruturado JSON (Portabilidade - Art. 18, V)
     */
    public function exportUserData(int $userId): array
    {
        $usuario = $this->db->table('tb_usuario')->where('usu_id', $userId)->get()->getRowArray();
        if (!$usuario) {
            return ['error' => 'Usuário não encontrado.'];
        }

        // Remove dados sensíveis como hash de senha
        unset($usuario['usu_senha']);

        $cupons = $this->db->table('tb_cupons_emitidos')
            ->where('cue_usuario', $userId)
            ->get()->getResultArray();

        $cartoes = $this->db->table('tb_usuarios_cartao')
            ->where('usc_usuario', $userId)
            ->get()->getResultArray();

        $consentimentos = $this->db->table('tb_lgpd_consents')
            ->where('user_id', $userId)
            ->get()->getResultArray();

        return [
            'titular'        => $usuario,
            'cupons'         => $cupons,
            'fidelidade'     => $cartoes,
            'consentimentos' => $consentimentos,
            'exportado_em'   => date('Y-m-d H:i:s'),
            'controlador'    => $this->config->companyName,
            'dpo_contato'    => $this->config->dpoEmail,
        ];
    }

    /**
     * Anonimiza os dados do usuário (Direito ao esquecimento / Art. 18, VI)
     */
    public function anonymizeUser(int $userId): bool
    {
        $anonymizedName = 'Usuario_Anonimizado_' . $userId;
        $anonymizedEmail = 'anonimo_' . $userId . '@removido.lgpd';

        return $this->db->table('tb_usuario')
            ->where('usu_id', $userId)
            ->update([
                'usu_nome'          => $anonymizedName,
                'usu_email'         => $anonymizedEmail,
                'usu_cpf'           => null,
                'usu_telefone'      => null,
                'usu_celular'       => null,
                'usu_endereco'      => null,
                'anonymized_at'     => date('Y-m-d H:i:s'),
                'deleted_at'        => date('Y-m-d H:i:s'),
            ]);
    }
}
