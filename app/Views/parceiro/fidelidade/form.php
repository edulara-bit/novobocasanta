<?= $this->extend('parceiro/layouts/partner') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
            <h3 class="fw-bold text-dark mb-4"><i class="fa-solid fa-id-card text-danger me-2"></i> <?= esc($title) ?></h3>

            <form action="<?= !empty($cartao) ? base_url('parceiro/fidelidade/atualizar/' . $cartao['car_id']) : base_url('parceiro/fidelidade/salvar') ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nome da Campanha / Título do Cartão <span class="text-danger">*</span></label>
                    <input type="text" name="car_nome" class="form-control" value="<?= esc($cartao['car_nome'] ?? old('car_nome') ?? '') ?>" placeholder="Ex: Cartão Fidelidade Almoço, Fidelidade Lavagem..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Oferta / Produto Vinculado</label>
                    <select name="car_oferta" class="form-select">
                        <option value="0">Todos os produtos / Programa Geral da Empresa</option>
                        <?php if (!empty($ofertas)): ?>
                            <?php foreach ($ofertas as $of): ?>
                                <option value="<?= $of->pro_id ?>" <?= (!empty($cartao['car_oferta']) && (int)$cartao['car_oferta'] === (int)$of->pro_id) ? 'selected' : '' ?>>
                                    Oferta: <?= esc($of->pro_titulo) ?> (R$ <?= number_format((float)$of->pro_preco, 2, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <small class="text-muted">Selecione uma oferta específica ou deixe para Todos os Produtos da sua empresa.</small>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Total de Pontos / Carimbos Necessários</label>
                        <input type="number" name="car_pontos" class="form-control" min="1" max="100" value="<?= esc($cartao['car_pontos'] ?? old('car_pontos') ?? 10) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Validade da Campanha (Dias)</label>
                        <input type="number" name="car_validade" class="form-control" min="1" max="730" value="<?= esc($cartao['car_validade'] ?? old('car_validade') ?? 90) ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Regras & Recompensa do Cartão</label>
                    <textarea name="car_regras" rows="4" class="form-control" placeholder="Descreva o que o cliente ganha ao completar a cartela (Ex: A cada 10 compras ganhe 1 almoço grátis)"><?= esc($cartao['car_regras'] ?? old('car_regras') ?? '') ?></textarea>
                </div>

                <div class="d-flex justify-content-between pt-3 border-top">
                    <a href="<?= base_url('parceiro/fidelidade') ?>" class="btn btn-secondary rounded-pill px-4">Voltar</a>
                    <button type="submit" class="btn btn-danger rounded-pill fw-bold px-5">Salvar Cartão</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
