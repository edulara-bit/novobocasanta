<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\MercadoPagoLinecashService;

class LinecashMpWebhook extends BaseController
{
    protected MercadoPagoLinecashService $mpService;

    public function __construct()
    {
        $this->mpService = new MercadoPagoLinecashService();
    }

    public function index()
    {
        $json = $this->request->getJSON(true);
        if (!$json) {
            $json = $this->request->getPost();
        }

        if (empty($json)) {
            // Mercado Pago pode enviar query params (ex: ?id=xxx&topic=subscription_preapproval)
            $json = [
                'id'    => $this->request->getGet('id'),
                'topic' => $this->request->getGet('topic'),
                'type'  => $this->request->getGet('type'),
            ];
        }

        $res = $this->mpService->processarWebhook($json);

        return $this->response->setJSON($res)->setStatusCode(200);
    }
}
