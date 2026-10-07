<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\LinefastService;

class LinefastWebhook extends BaseController
{
    protected LinefastService $linefastService;

    public function __construct()
    {
        $this->linefastService = new LinefastService();
    }

    public function index()
    {
        $payload = $this->request->getJSON(true);
        if (empty($payload)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid JSON payload']);
        }

        $signature = $this->request->getHeaderLine('X-Linefast-Signature');
        $result = $this->linefastService->handleWebhook($payload, $signature);

        return $this->response->setJSON($result);
    }
}
