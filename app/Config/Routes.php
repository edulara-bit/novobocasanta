<?php

declare(strict_types=1);

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ROTA RAIZ PADRÃO
$routes->get('/', 'Home::index/piracicaba');

// SITEMAP XML DINÂMICO
$routes->get('sitemap.xml', 'Sitemap::index');

// ANUNCIE CONOSCO & CONTRATAÇÃO ONLINE (ITEM 13)
$routes->get('anuncie', 'Conteudo::anuncie');
$routes->get('anuncie-no-boca-santa-ofertas', 'Conteudo::anuncie');
$routes->post('anuncie/enviar', 'Conteudo::enviarProposta');

// PAINEL DO PARCEIRO (ÁREA RESTRITA)
$routes->get('parceiro', 'Parceiro\Auth::login');
$routes->get('parceiro/login', 'Parceiro\Auth::login');
$routes->post('parceiro/login', 'Parceiro\Auth::autenticar');
$routes->get('parceiro/logout', 'Parceiro\Auth::logout');

$routes->group('parceiro', static function ($routes) {
    $routes->get('dashboard', 'Parceiro\Dashboard::index');
    
    // Perfil & Dados
    $routes->get('perfil', 'Parceiro\Dashboard::perfil');
    $routes->post('perfil/salvar', 'Parceiro\Dashboard::salvarPerfil');

    // Ofertas do Parceiro
    $routes->get('ofertas', 'Parceiro\Dashboard::ofertas');
    $routes->get('ofertas/criar', 'Parceiro\Dashboard::criarOferta');
    $routes->post('ofertas/salvar', 'Parceiro\Dashboard::salvarOferta');
    $routes->get('ofertas/editar/(:num)', 'Parceiro\Dashboard::editarOferta/$1');
    $routes->post('ofertas/atualizar/(:num)', 'Parceiro\Dashboard::atualizarOferta/$1');
    $routes->get('ofertas/excluir/(:num)', 'Parceiro\Dashboard::excluirOferta/$1');

    // Fidelidade do Parceiro
    $routes->get('fidelidade', 'Parceiro\Dashboard::fidelidade');
    $routes->get('fidelidade/criar', 'Parceiro\Dashboard::criarCartao');
    $routes->post('fidelidade/salvar', 'Parceiro\Dashboard::salvarCartao');
    $routes->get('fidelidade/editar/(:num)', 'Parceiro\Dashboard::editarCartao/$1');
    $routes->post('fidelidade/atualizar/(:num)', 'Parceiro\Dashboard::atualizarCartao/$1');
    $routes->get('fidelidade/excluir/(:num)', 'Parceiro\Dashboard::excluirCartao/$1');

    // Apontamento de Compras & Pontos do Cliente
    $routes->get('fidelidade/lancamentos', 'Parceiro\Dashboard::lancamentos');
    $routes->post('fidelidade/salvar-lancamento', 'Parceiro\Dashboard::salvarLancamento');
    $routes->get('fidelidade/excluir-lancamento/(:num)', 'Parceiro\Dashboard::excluirLancamento/$1');
    $routes->post('fidelidade/validar-resgate', 'Parceiro\Dashboard::validarResgate');
    $routes->get('fidelidade/buscar-cliente', 'Parceiro\Dashboard::buscarClienteAjax');
});

// LGPD & POLÍTICAS
$routes->get('politica-privacidade', 'Lgpd::politica');
$routes->get('termos', 'Lgpd::politica');
$routes->get('lgpd/solicitar-dados', 'Lgpd::solicitarDados');
$routes->post('lgpd/processar-solicitacao', 'Lgpd::processarSolicitacao');
$routes->post('lgpd/consent', 'Lgpd::salvarConsentimento');

// API & LINEFAST WEBHOOKS
$routes->group('api', static function ($routes) {
    $routes->post('linefast/webhook', 'Api\LinefastWebhook::index');
});

// COMPATIBILIDADE LEGADA CI3 (301 Permanent Redirect para preservar SEO histórico)
$routes->get('oferta/mostra/(:num)', 'Ofertas::mostra/$1');
$routes->get('oferta/mostra/(:any)', 'Ofertas::mostra/$1');

// PAINEL ADMINISTRATIVO (AdminLTE)
$routes->get('admin/login', 'Admin\Auth::login');
$routes->post('admin/login', 'Admin\Auth::autenticar');
$routes->get('admin/logout', 'Admin\Auth::logout');

$routes->group('admin', static function ($routes) {
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->get('dashboard', 'Admin\Dashboard::index');

    // Ofertas
    $routes->get('ofertas', 'Admin\Ofertas::index');
    $routes->get('ofertas/criar', 'Admin\Ofertas::criar');
    $routes->post('ofertas/salvar', 'Admin\Ofertas::salvar');
    $routes->get('ofertas/editar/(:num)', 'Admin\Ofertas::editar/$1');
    $routes->post('ofertas/atualizar/(:num)', 'Admin\Ofertas::atualizar/$1');
    $routes->get('ofertas/excluir/(:num)', 'Admin\Ofertas::excluir/$1');

    // Parceiros
    $routes->get('parceiros', 'Admin\Parceiros::index');
    $routes->get('parceiros/criar', 'Admin\Parceiros::criar');
    $routes->post('parceiros/salvar', 'Admin\Parceiros::salvar');
    $routes->get('parceiros/editar/(:num)', 'Admin\Parceiros::editar/$1');
    $routes->post('parceiros/atualizar/(:num)', 'Admin\Parceiros::atualizar/$1');
    $routes->get('parceiros/excluir/(:num)', 'Admin\Parceiros::excluir/$1');
    $routes->get('parceiros/login-as/(:num)', 'Admin\Parceiros::loginAs/$1');
    $routes->get('parceiros/zerar-acessos/(:num)', 'Admin\Parceiros::zerarAcessos/$1');

    // Categorias
    $routes->get('categorias', 'Admin\Categorias::index');
    $routes->get('categorias/criar', 'Admin\Categorias::criar');
    $routes->post('categorias/salvar', 'Admin\Categorias::salvar');
    $routes->get('categorias/editar/(:num)', 'Admin\Categorias::editar/$1');
    $routes->post('categorias/atualizar/(:num)', 'Admin\Categorias::atualizar/$1');
    $routes->get('categorias/excluir/(:num)', 'Admin\Categorias::excluir/$1');

    // Banners
    $routes->get('banners', 'Admin\Banners::index');
    $routes->get('banners/criar', 'Admin\Banners::criar');
    $routes->post('banners/salvar', 'Admin\Banners::salvar');
    $routes->get('banners/editar/(:num)', 'Admin\Banners::editar/$1');
    $routes->post('banners/atualizar/(:num)', 'Admin\Banners::atualizar/$1');
    $routes->get('banners/excluir/(:num)', 'Admin\Banners::excluir/$1');

    // Cidades
    $routes->get('cidades', 'Admin\Cidades::index');
    $routes->get('cidades/criar', 'Admin\Cidades::criar');
    $routes->post('cidades/salvar', 'Admin\Cidades::salvar');
    $routes->get('cidades/editar/(:num)', 'Admin\Cidades::editar/$1');
    $routes->post('cidades/atualizar/(:num)', 'Admin\Cidades::atualizar/$1');
    $routes->get('cidades/excluir/(:num)', 'Admin\Cidades::excluir/$1');

    // Estados
    $routes->get('estados', 'Admin\Estados::index');
    $routes->get('estados/criar', 'Admin\Estados::criar');
    $routes->post('estados/salvar', 'Admin\Estados::salvar');
    $routes->get('estados/editar/(:num)', 'Admin\Estados::editar/$1');
    $routes->post('estados/atualizar/(:num)', 'Admin\Estados::atualizar/$1');
    $routes->get('estados/excluir/(:num)', 'Admin\Estados::excluir/$1');

    // Administradores
    $routes->get('administradores', 'Admin\Administradores::index');
    $routes->get('administradores/criar', 'Admin\Administradores::criar');
    $routes->post('administradores/salvar', 'Admin\Administradores::salvar');
    $routes->get('administradores/editar/(:num)', 'Admin\Administradores::editar/$1');
    $routes->post('administradores/atualizar/(:num)', 'Admin\Administradores::atualizar/$1');
    $routes->get('administradores/excluir/(:num)', 'Admin\Administradores::excluir/$1');

    // Redirecionamentos SEO
    $routes->get('redirecionamentos', 'Admin\Redirecionamentos::index');
    $routes->get('redirecionamentos/criar', 'Admin\Redirecionamentos::criar');
    $routes->post('redirecionamentos/salvar', 'Admin\Redirecionamentos::salvar');
    $routes->get('redirecionamentos/editar/(:num)', 'Admin\Redirecionamentos::editar/$1');
    $routes->post('redirecionamentos/atualizar/(:num)', 'Admin\Redirecionamentos::atualizar/$1');
    $routes->get('redirecionamentos/excluir/(:num)', 'Admin\Redirecionamentos::excluir/$1');

    // Linefast
    $routes->get('linefast', 'Admin\Linefast::index');
    $routes->get('linefast/sync/(:num)', 'Admin\Linefast::sync/$1');

    // LGPD
    $routes->get('lgpd', 'Admin\Lgpd::index');
    $routes->post('lgpd/salvar-pagina', 'Admin\Lgpd::salvarPagina');
    $routes->get('lgpd/exportar/(:num)', 'Admin\Lgpd::exportar/$1');
    $routes->get('lgpd/anonimizar/(:num)', 'Admin\Lgpd::anonimizar/$1');
    $routes->get('lgpd/concluir/(:num)', 'Admin\Lgpd::concluir/$1');
});

// ROTAS DINÂMICAS DO PORTAL POR CIDADE
$routes->get('(:segment)/busca', 'Ofertas::busca/$1');
$routes->get('(:segment)/categoria/(:segment)', 'Ofertas::categoria/$1/$2');
$routes->get('(:segment)/parceiros', 'Parceiros::index/$1');
$routes->get('(:segment)/parceiro/(:segment)', 'Parceiros::detalhes/$1/$2');
$routes->get('(:segment)/(:segment)/(:segment)', 'Ofertas::detalhes/$1/$2/$3');
$routes->get('(:segment)', 'Home::index/$1');
