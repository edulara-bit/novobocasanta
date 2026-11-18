<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class NoIndexFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Nada a executar antes da requisição
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Aplica cabeçalho HTTP noindex apenas no subdomínio de testes/homologação
        $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
        if (str_starts_with($host, 'teste.')) {
            $response->setHeader('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');
        }
        return $response;
    }
}
