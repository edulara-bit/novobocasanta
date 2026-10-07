<?php

declare(strict_types=1);

namespace App\Controllers\Parceiro;

use App\Controllers\BaseController;
use App\Models\ParceiroModel;

class Auth extends BaseController
{
    protected ParceiroModel $parceiroModel;

    public function __construct()
    {
        $this->parceiroModel = new ParceiroModel();
    }

    public function login()
    {
        if (session()->get('parceiro_id')) {
            return redirect()->to(base_url('parceiro/dashboard'));
        }

        return view('parceiro/login', [
            'title' => 'Login do Parceiro | Boca Santa Ofertas',
        ]);
    }

    public function autenticar()
    {
        $email = trim((string)$this->request->getPost('email'));
        $senha = trim((string)$this->request->getPost('senha'));

        if (empty($email) || empty($senha)) {
            return redirect()->back()->withInput()->with('error', 'Por favor, preencha o e-mail e a senha.');
        }

        $parceiro = $this->parceiroModel->where('par_email', $email)->first();

        if ($parceiro) {
            // Suporte a hash moderno ou md5 legado
            $valido = password_verify($senha, (string)$parceiro->par_senha) || ($parceiro->par_senha === md5($senha)) || ($parceiro->par_senha === $senha);

            if ($valido) {
                session()->set([
                    'parceiro_id'    => $parceiro->par_id,
                    'parceiro_nome'  => $parceiro->getNome(),
                    'parceiro_email' => $parceiro->par_email,
                    'parceiro_logado'=> true,
                ]);

                return redirect()->to(base_url('parceiro/dashboard'))->with('success', "Bem-vindo de volta, {$parceiro->getNome()}!");
            }
        }

        return redirect()->back()->withInput()->with('error', 'E-mail ou senha incorretos.');
    }

    public function logout()
    {
        session()->remove(['parceiro_id', 'parceiro_nome', 'parceiro_email', 'parceiro_logado']);
        return redirect()->to(base_url('parceiro/login'))->with('success', 'Sessão encerrada com sucesso.');
    }
}
