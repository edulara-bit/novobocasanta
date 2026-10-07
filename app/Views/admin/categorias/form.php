<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-folder mr-1"></i> <?= esc($title) ?>
                </h3>
            </div>
            
            <form action="<?= !empty($categoria) ? base_url('admin/categorias/atualizar/' . $categoria->cat_id) : base_url('admin/categorias/salvar') ?>" method="post">
                <?= csrf_field() ?>
                <div class="card-body">
                    <div class="form-group">
                        <label for="cat_titulo">Título da Categoria <span class="text-danger">*</span></label>
                        <input type="text" name="cat_titulo" id="cat_titulo" class="form-control" value="<?= esc($categoria->cat_titulo ?? old('cat_titulo') ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="cat_categoria_pai">Categoria Pai (Deixe "Nenhuma" para Principal)</label>
                        <select name="cat_categoria_pai" id="cat_categoria_pai" class="form-control">
                            <option value="0">Nenhuma (Categoria Principal)</option>
                            <?php foreach ($principais as $p): ?>
                                <?php if (empty($categoria) || (int)$categoria->cat_id !== (int)$p->cat_id): ?>
                                    <option value="<?= $p->cat_id ?>" <?= (!empty($categoria) && (int)$categoria->cat_categoria_pai === (int)$p->cat_id) ? 'selected' : '' ?>>
                                        <?= esc($p->cat_titulo) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- CAMPO DE ÍCONE COM SELETOR EM MODAL -->
                    <div class="form-group">
                        <label for="cat_classe">Ícone da Categoria</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white" id="iconPreviewWrapper">
                                    <i class="fas <?= esc($categoria->cat_classe ?? old('cat_classe') ?? 'fa-tag') ?> text-danger fs-5" id="liveIconPreview"></i>
                                </span>
                            </div>
                            <input type="text" name="cat_classe" id="cat_classe" class="form-control font-weight-bold" value="<?= esc($categoria->cat_classe ?? old('cat_classe') ?? 'fa-tag') ?>" placeholder="fa-tag" readonly>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger font-weight-bold" data-toggle="modal" data-target="#modalIconPicker">
                                    <i class="fas fa-icons mr-1"></i> Escolher Ícone
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">Clique em "Escolher Ícone" para abrir a galeria de ícones visuais para esta categoria.</small>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="<?= base_url('admin/categorias') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Voltar</a>
                    <button type="submit" class="btn btn-danger font-weight-bold px-4"><i class="fas fa-save mr-1"></i> Salvar Categoria</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL SELETOR DE ÍCONES FONTAWESOME -->
<div class="modal fade" id="modalIconPicker" tabindex="-1" role="dialog" aria-labelledby="modalIconPickerLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-weight-bold" id="modalIconPickerLabel"><i class="fas fa-icons mr-2"></i> Selecione o Ícone da Categoria</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="searchIconsInput" class="form-control" placeholder="Buscar ícone por nome... (ex: pizza, pet, carro, livro, saude, celular)">
                </div>

                <?php
                $gruposIcones = [
                    'Alimentação & Gastronomia' => [
                        'fa-utensils' => 'Restaurante / Talheres',
                        'fa-burger' => 'Hambúrguer / Lanches',
                        'fa-pizza-slice' => 'Pizzaria',
                        'fa-mug-hot' => 'Café / Bebidas',
                        'fa-beer-mug-empty' => 'Cervejaria / Bar',
                        'fa-cake-candles' => 'Doces / Bolos',
                        'fa-ice-cream' => 'Sorveteria',
                        'fa-bowl-food' => 'Marmitex / Pratos',
                        'fa-fish' => 'Frutos do Mar / Peixaria',
                        'fa-drumstick-bite' => 'Carnes / Churrasco',
                    ],
                    'Animais & Pets' => [
                        'fa-paw' => 'Pet Shop / Geral',
                        'fa-dog' => 'Cachorro / Canil',
                        'fa-cat' => 'Gato / Felinos',
                        'fa-bone' => 'Ração / Acessórios',
                    ],
                    'Informática & Tecnologia' => [
                        'fa-laptop' => 'Notebook / Computadores',
                        'fa-mobile-screen-button' => 'Smartphones / Celulares',
                        'fa-desktop' => 'Computadores / Telas',
                        'fa-print' => 'Impressoras & Cartuchos',
                        'fa-headset' => 'Acessórios / Games',
                        'fa-wifi' => 'Internet & Redes',
                        'fa-tv' => 'Eletrônicos & Áudio',
                    ],
                    'Moda, Beleza & Saúde' => [
                        'fa-shirt' => 'Vestuário & Roupas',
                        'fa-spa' => 'Estética & Beleza',
                        'fa-scissors' => 'Salão & Barbearia',
                        'fa-heart-pulse' => 'Saúde & Clínicas',
                        'fa-pills' => 'Farmácia & Medicamentos',
                        'fa-glasses' => 'Ótica',
                        'fa-gem' => 'Jóias & Acessórios',
                    ],
                    'Veículos & Automotivo' => [
                        'fa-car' => 'Carros & Veículos',
                        'fa-motorcycle' => 'Motos & Oficinas',
                        'fa-truck' => 'Caminhões & Transporte',
                        'fa-gas-pump' => 'Postos de Combustível',
                        'fa-oil-can' => 'Troca de Óleo',
                        'fa-screwdriver-wrench' => 'Mecânica & Reparos',
                    ],
                    'Casa, Construção & Serviços' => [
                        'fa-house' => 'Imóveis & Casa',
                        'fa-couch' => 'Móveis & Decoração',
                        'fa-hammer' => 'Construção Civil',
                        'fa-paint-roller' => 'Pintura & Tintas',
                        'fa-lightbulb' => 'Elétrica & Iluminação',
                        'fa-wrench' => 'Serviços Gerais',
                        'fa-briefcase' => 'Escritórios & Negócios',
                    ],
                    'Educação, Esportes & Lazer' => [
                        'fa-dumbbell' => 'Academia & Fitness',
                        'fa-futbol' => 'Futebol & Esportes',
                        'fa-book' => 'Livraria & Papelaria',
                        'fa-graduation-cap' => 'Cursos & Escolas',
                        'fa-baby' => 'Bebês & Crianças',
                        'fa-palette' => 'Artes & Artesanato',
                        'fa-guitar' => 'Música & Instrumentos',
                        'fa-gift' => 'Presentes & Utilidades',
                    ]
                ];
                ?>

                <?php foreach ($gruposIcones as $grupo => $icones): ?>
                    <div class="icon-group-section mb-4">
                        <h6 class="font-weight-bold text-secondary border-bottom pb-2 mb-3"><?= esc($grupo) ?></h6>
                        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-2">
                            <?php foreach ($icones as $iconClass => $label): ?>
                                <div class="col mb-2 icon-item" data-name="<?= mb_strtolower($label . ' ' . $iconClass) ?>">
                                    <button type="button" class="btn btn-outline-light text-dark border w-100 p-2 text-left d-flex align-items-center gap-2 select-icon-btn" data-icon="<?= $iconClass ?>">
                                        <i class="fas <?= $iconClass ?> text-danger fs-5" style="width: 24px; text-align: center;"></i>
                                        <span class="small font-weight-bold text-truncate"><?= esc($label) ?></span>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Filtro de busca de ícones
    const searchInput = document.getElementById('searchIconsInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            document.querySelectorAll('.icon-item').forEach(item => {
                const name = item.getAttribute('data-name');
                if (!query || name.includes(query)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Seleção de ícone
    document.querySelectorAll('.select-icon-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const icon = btn.getAttribute('data-icon');
            document.getElementById('cat_classe').value = icon;
            const preview = document.getElementById('liveIconPreview');
            preview.className = 'fas ' + icon + ' text-danger fs-5';
            
            // Fechar modal
            $('#modalIconPicker').modal('hide');
        });
    });
});
</script>
<?= $this->endSection() ?>
