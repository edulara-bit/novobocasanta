<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-danger card-tabs">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="lgpdTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold" id="tab-requests" data-toggle="pill" href="#content-requests" role="tab">
                            <i class="fas fa-user-shield text-danger mr-1"></i> Solicitações de Titulares (Art. 18)
                            <span class="badge badge-danger ml-1"><?= count($solicitacoes) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-pages" data-toggle="pill" href="#content-pages" role="tab">
                            <i class="fas fa-file-contract text-info mr-1"></i> Editor de Políticas & Termos Dinâmicos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-consents" data-toggle="pill" href="#content-consents" role="tab">
                            <i class="fas fa-cookie-bite text-warning mr-1"></i> Logs de Consentimento de Cookies
                            <span class="badge badge-secondary ml-1"><?= count($consentimentos) ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="card-body">
                <div class="tab-content" id="lgpdTabsContent">
                    
                    <!-- TAB 1: SOLICITAÇÕES DOS TITULARES (ART. 18) -->
                    <div class="tab-pane fade show active" id="content-requests" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="font-weight-bold mb-0 text-dark">Portal do Titular &bull; Requisições Recebidas</h5>
                            <a href="<?= base_url('lgpd/solicitar-dados') ?>" target="_blank" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-external-link-alt mr-1"></i> Ver Formulário Público
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Data/Hora</th>
                                        <th>Titular</th>
                                        <th>E-mail</th>
                                        <th>Tipo de Solicitação</th>
                                        <th>Status</th>
                                        <th style="width: 200px;" class="text-center">Ações LGPD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($solicitacoes)): ?>
                                        <?php foreach ($solicitacoes as $s): ?>
                                            <tr>
                                                <td><?= $s['id'] ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($s['created_at'])) ?></td>
                                                <td><strong><?= esc($s['nome']) ?></strong></td>
                                                <td><?= esc($s['email']) ?></td>
                                                <td>
                                                    <span class="badge badge-info"><?= esc(ucfirst(str_replace('_', ' ', $s['tipo_solicitacao'] ?? 'Consulta'))) ?></span>
                                                </td>
                                                <td>
                                                    <?php if (($s['status'] ?? '') === 'concluido'): ?>
                                                        <span class="badge badge-success">Concluído</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning">Pendente</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if (!empty($s['user_id'])): ?>
                                                        <a href="<?= base_url('admin/lgpd/exportar/' . $s['user_id']) ?>" class="btn btn-info btn-xs mr-1" title="Exportar Dados (Art. 19 JSON)">
                                                            <i class="fas fa-file-download"></i> Exportar
                                                        </a>
                                                        <a href="<?= base_url('admin/lgpd/anonimizar/' . $s['user_id']) ?>" class="btn btn-warning btn-xs mr-1" title="Anonimizar Dados" onclick="return confirm('Deseja realmente anonimizar os dados pessoais deste usuário?');">
                                                            <i class="fas fa-user-secret"></i> Anonimizar
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="<?= base_url('admin/lgpd/concluir/' . $s['id']) ?>" class="btn btn-success btn-xs" title="Marcar como Concluído">
                                                        <i class="fas fa-check"></i> Concluir
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 2: EDITOR DINÂMICO DE PÁGINAS LGPD -->
                    <div class="tab-pane fade" id="content-pages" role="tabpanel">
                        <h5 class="font-weight-bold mb-3 text-dark">Gerenciar Conteúdo das Páginas de Políticas & Termos</h5>
                        
                        <div class="row">
                            <?php foreach ($paginas as $pag): ?>
                                <div class="col-lg-6 mb-4">
                                    <div class="card card-outline card-secondary shadow-sm">
                                        <div class="card-header bg-light">
                                            <h6 class="font-weight-bold mb-0 text-dark">
                                                <i class="fas fa-edit text-danger mr-1"></i> <?= esc($pag['con_titulo']) ?>
                                                <small class="text-muted d-block mt-1">Rota: <code>/<?= esc($pag['con_slug'] === 'politica-privacidade' ? 'politica-privacidade' : 'termos') ?></code></small>
                                            </h6>
                                        </div>
                                        <form action="<?= base_url('admin/lgpd/salvar-pagina') ?>" method="post">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="con_slug" value="<?= esc($pag['con_slug']) ?>">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label>Título da Página</label>
                                                    <input type="text" name="con_titulo" class="form-control font-weight-bold" value="<?= esc($pag['con_titulo']) ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Conteúdo HTML da Política / Termos</label>
                                                    <textarea name="con_conteudo" rows="10" class="form-control" style="font-size: 0.9rem; font-family: monospace;" required><?= esc($pag['con_conteudo']) ?></textarea>
                                                </div>
                                            </div>
                                            <div class="card-footer text-right bg-white">
                                                <button type="submit" class="btn btn-danger btn-sm font-weight-bold">
                                                    <i class="fas fa-save mr-1"></i> Salvar Alterações
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- TAB 3: LOGS DE CONSENTIMENTO DE COOKIES -->
                    <div class="tab-pane fade" id="content-consents" role="tabpanel">
                        <h5 class="font-weight-bold mb-3 text-dark">Logs de Consentimentos de Cookies (Auditoria LGPD)</h5>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width: 60px;">ID</th>
                                        <th>Data / Hora</th>
                                        <th>Identificador / IP</th>
                                        <th>Preferências Registradas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($consentimentos)): ?>
                                        <?php foreach ($consentimentos as $c): ?>
                                            <tr>
                                                <td><?= $c['id'] ?? '-' ?></td>
                                                <td><?= !empty($c['created_at']) ? date('d/m/Y H:i:s', strtotime($c['created_at'])) : date('d/m/Y H:i:s') ?></td>
                                                <td>
                                                    <code><?= esc($c['ip_address'] ?? $c['ip_hash'] ?? $c['visitor_id'] ?? 'Anônimo') ?></code>
                                                </td>
                                                <td>
                                                    <span class="badge badge-success mr-1"><i class="fas fa-check"></i> Essenciais</span>
                                                    <span class="badge <?= !empty($c['consent_analytics']) ? 'badge-primary' : 'badge-secondary' ?> mr-1">
                                                        Analíticos: <?= !empty($c['consent_analytics']) ? 'Sim' : 'Não' ?>
                                                    </span>
                                                    <span class="badge <?= !empty($c['consent_marketing']) ? 'badge-warning' : 'badge-secondary' ?>">
                                                        Marketing: <?= !empty($c['consent_marketing']) ? 'Sim' : 'Não' ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
