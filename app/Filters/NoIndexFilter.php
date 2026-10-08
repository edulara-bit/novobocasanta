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
        // Garante cabeçalho HTTP noindex em todas as respostas (HTML, JSON, downloads, etc.)
        $response->setHeader('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');
        return $response;
    }
}
