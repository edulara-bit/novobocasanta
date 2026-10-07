<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Lgpd extends BaseConfig
{
    /**
     * Versão atual dos termos de uso e política de privacidade
     */
    public string $termsVersion = '1.0.0';

    /**
     * Se o banner de consentimento de cookies está ativo
     */
    public bool $cookieBannerEnabled = true;

    /**
     * Nome do cookie onde fica gravado o consentimento
     */
    public string $cookieName = 'bocasanta_lgpd_consent';

    /**
     * Duração do consentimento em dias
     */
    public int $cookieDurationDays = 365;

    /**
     * Anonimizar endereços IP em logs estatísticos (LGPD Art. 13)
     */
    public bool $anonymizeIp = true;

    /**
     * E-mail do Encarregado pelo Tratamento de Dados Pessoais (DPO)
     */
    public string $dpoEmail = 'privacidade@bocasanta.com.br';

    /**
     * Nome da empresa controladora
     */
    public string $companyName = 'Boca Santa Ofertas & Publicidade';

    /**
     * CNPJ da empresa controladora
     */
    public string $companyCnpj = '';

    /**
     * Prazo em dias para resposta de solicitações dos titulares (Art. 19 LGPD)
     */
    public int $slaResponseDays = 15;
}
