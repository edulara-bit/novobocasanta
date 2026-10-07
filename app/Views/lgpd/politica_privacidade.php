<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <span class="badge bg-danger-subtle text-danger align-self-start px-3 py-2 rounded-pill fw-bold mb-3">LGPD COMPLIANT</span>
                <h1 class="display-6 fw-bold text-dark mb-4">Política de Privacidade & Proteção de Dados</h1>
                <p class="text-muted small mb-4">Última atualização: <?= date('d/m/Y') ?> &bull; Versão 1.0.0</p>

                <div class="lh-lg text-secondary">
                    <h4 class="fw-bold text-dark mt-4 mb-3">1. Introdução e Compromisso</h4>
                    <p>
                        O portal <strong>Boca Santa Ofertas</strong> respeita a privacidade de seus usuários e tem total compromisso com a proteção de seus dados pessoais, em estrita observância à <strong>Lei Geral de Proteção de Dados Pessoais (Lei Federal nº 13.709/2018 - LGPD)</strong>.
                    </p>

                    <h4 class="fw-bold text-dark mt-4 mb-3">2. Dados Pessoais Coletados e Finalidades</h4>
                    <p>
                        Coletamos o mínimo de dados necessários para garantir uma experiência personalizada e segura de busca de ofertas, resgate de cupons e direcionamento para compras locais:
                    </p>
                    <ul>
                        <li><strong>Dados de Navegação e Cookies:</strong> Endereço IP (anonimizado), tipo de navegador, páginas visualizadas e preferências de cidades para aprimorar a relevância dos anúncios.</li>
                        <li><strong>Dados de Cadastro (Clientes/Usuários):</strong> Nome completo, e-mail, telefone e cidade para permitir o resgate de cupons exclusivos e participação no programa de fidelidade.</li>
                        <li><strong>Dados de Estabelecimentos Parceiros:</strong> Razão social, nome fantasia, CNPJ, endereço comercial, telefones e identificadores de integração com o Linefast.</li>
                    </ul>

                    <h4 class="fw-bold text-dark mt-4 mb-3">3. Integração e Compartilhamento com o Linefast</h4>
                    <p>
                        Para parceiros integrados com a plataforma <strong>Linefast</strong>, o Boca Santa Ofertas atua como vitrine de produtos e canal de redirecionamento. Ao clicar no botão <em>"Comprar no Linefast"</em>, o usuário é direcionado para a finalização da compra no ambiente seguro do parceiro, onde aplicam-se também os termos específicos daquela transação.
                    </p>

                    <h4 class="fw-bold text-dark mt-4 mb-3">4. Direitos do Titular de Dados (Art. 18 da LGPD)</h4>
                    <p>
                        Conforme a legislação brasileira, você possui o direito de:
                    </p>
                    <ul>
                        <li>Confirmar a existência de tratamento dos seus dados;</li>
                        <li>Acessar e exportar seus dados em formato legível;</li>
                        <li>Corrigir dados incompletos, inexatos ou desatualizados;</li>
                        <li>Solicitar a anonimização, bloqueio ou eliminação de seus dados pessoais (Direito ao Esquecimento);</li>
                        <li>Revogar consentimentos previamente concedidos.</li>
                    </ul>

                    <div class="my-4 p-4 rounded-4 bg-light border text-center">
                        <h5 class="fw-bold mb-2">Deseja exercer seus direitos como titular?</h5>
                        <p class="text-muted small mb-3">Acesse nosso canal exclusivo de autoatendimento da LGPD:</p>
                        <a href="<?= base_url('lgpd/solicitar-dados') ?>" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                            <i class="fa-solid fa-user-shield me-2"></i> Canal do Titular de Dados
                        </a>
                    </div>

                    <h4 class="fw-bold text-dark mt-4 mb-3">5. Contato do Encarregado de Proteção de Dados (DPO)</h4>
                    <p>
                        Para dúvidas, esclarecimentos ou requisições formais sobre o tratamento de dados pessoais, entre em contato diretamente com nosso Encarregado pelo e-mail: <a href="mailto:privacidade@bocasanta.com.br" class="text-danger fw-semibold">privacidade@bocasanta.com.br</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
