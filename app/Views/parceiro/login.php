<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">
    <title>Área do Parceiro | Boca Santa Ofertas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/bocasanta-theme.css') ?>" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100 py-5">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="text-center mb-4">
                <a href="<?= base_url() ?>">
                    <img src="<?= base_url('assets/images/logo.png') ?>" alt="Boca Santa" height="48">
                </a>
            </div>

            <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 bg-white">
                <h4 class="fw-bold text-center mb-1"><i class="fa-solid fa-store text-danger me-1"></i> Painel do Parceiro</h4>
                <p class="text-muted small text-center mb-4">Acesse para gerenciar suas ofertas e cupons</p>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger small rounded-3 mb-3">
                        <i class="fa-solid fa-circle-exclamation me-1"></i> <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success small rounded-3 mb-3">
                        <i class="fa-solid fa-circle-check me-1"></i> <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('parceiro/login') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">E-mail Cadastrado</label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="seu@email.com" value="<?= old('email') ?>" required autofocus>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Senha de Acesso</label>
                        <input type="password" name="senha" class="form-control rounded-3" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-danger btn-lg fw-bold rounded-pill w-100 shadow-sm mb-3">
                        Entrar no Painel
                    </button>

                    <div class="text-center">
                        <a href="<?= base_url('anuncie') ?>" class="text-decoration-none small text-muted">
                            Ainda não é parceiro? <strong class="text-danger">Cadastre-se aqui</strong>
                        </a>
                    </div>
                </form>
            </div>
            
            <div class="text-center mt-4">
                <a href="<?= base_url() ?>" class="text-decoration-none small text-muted">
                    <i class="fa-solid fa-arrow-left me-1"></i> Voltar ao portal
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
