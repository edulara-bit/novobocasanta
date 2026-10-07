<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <div class="text-center mb-4">
                    <i class="fa-solid fa-user-shield text-danger display-4 mb-3"></i>
                    <h1 class="h2 fw-bold text-dark mb-2">Canal de Atendimento ao Titular de Dados</h1>
                    <p class="text-muted">Exerça seus direitos garantidos pela LGPD (Lei nº 13.709/2018)</p>
                </div>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success rounded-3 mb-4">
                        <i class="fa-solid fa-circle-check me-2"></i> <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger rounded-3 mb-4">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('lgpd/processar-solicitacao') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipo de Solicitação <span class="text-danger">*</span></label>
                        <select name="tipo" class="form-select rounded-3 py-2" required>
                            <option value="exportar_dados">📥 Exportar todos os meus dados (Portabilidade - Art. 18, V)</option>
                            <option value="excluir_dados">🗑️ Excluir minha conta e dados (Direito ao Esquecimento - Art. 18, VI)</option>
                            <option value="revogar_consentimento">⛔ Revogar consentimento para comunicações/marketing</option>
                            <option value="correcao_dados">✏️ Correção de dados pessoais incompletos ou inexatos</option>
                            <option value="outros">❓ Outra dúvida ou requisição sobre privacidade</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Seu Nome Completo <span class="text-danger">*</span></label>
                            <input type="text" name="nome" class="form-control rounded-3 py-2" placeholder="Ex: João da Silva" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Seu E-mail Cadastrado <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control rounded-3 py-2" placeholder="seuemail@exemplo.com" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Detalhes / Observações adicionais</label>
                        <textarea name="mensagem" class="form-control rounded-3" rows="4" placeholder="Descreva sua solicitação com detalhes para agilizar o atendimento pelo nosso Encarregado de Dados (DPO)..."></textarea>
                    </div>

                    <div class="p-3 bg-light rounded-3 small text-muted mb-4 border">
                        <i class="fa-solid fa-circle-info text-primary me-1"></i>
                        Sua solicitação será analisada pelo nosso Encarregado de Dados (DPO) e respondida no prazo legal de até 15 dias úteis (Art. 19, II da LGPD) no e-mail informado.
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger btn-lg fw-bold rounded-pill shadow-sm">
                            <i class="fa-solid fa-paper-plane me-2"></i> Enviar Solicitação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
