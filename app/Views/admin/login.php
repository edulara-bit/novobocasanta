<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Login Administrativo') ?> | Boca Santa</title>

    <!-- Google Font: Source Sans Pro & Inter -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- AdminLTE 3.2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <style>
        body.login-page {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            width: 420px;
            max-width: 92%;
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 2px solid #dc2626;
        }
        .btn-admin-login {
            background-color: #dc2626;
            border-color: #dc2626;
            color: #ffffff;
            font-weight: 700;
            padding: 10px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .btn-admin-login:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
            color: #ffffff;
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="card card-outline card-danger shadow-lg rounded-4 overflow-hidden border-0">
        <div class="card-header text-center py-4">
            <a href="<?= base_url() ?>" class="d-inline-block">
                <img src="<?= base_url('assets/images/logo.png') ?>" alt="Boca Santa Ofertas" style="height: 52px; width: auto; object-fit: contain;">
            </a>
            <div class="mt-2 text-muted small font-weight-bold text-uppercase letter-spacing-1">Painel Administrativo</div>
        </div>
        <div class="card-body login-card-body p-4">
            <p class="login-box-msg text-secondary">Acesse com suas credenciais de gestão</p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                    <i class="fas fa-exclamation-circle mr-1"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show small" role="alert">
                    <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/login') ?>" method="post">
                <?= csrf_field() ?>
                <div class="input-group mb-3">
                    <input type="text" name="login" class="form-control" placeholder="Usuário ou E-mail" value="<?= old('login') ?>" required autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user text-muted"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-4">
                    <input type="password" name="senha" class="form-control" placeholder="Senha de Acesso" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock text-muted"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-admin-login btn-block shadow-sm">
                            <i class="fas fa-sign-in-alt mr-2"></i> Entrar no Gerenciador
                        </button>
                    </div>
                </div>
            </form>

            <div class="text-center mt-4 pt-3 border-top">
                <a href="<?= base_url() ?>" class="text-secondary small">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar para o Portal
                </a>
            </div>
        </div>
    </div>
</div>

<!-- jQuery & Bootstrap 4 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
