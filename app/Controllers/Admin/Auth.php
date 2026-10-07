<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Config\Database;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('admin_logged')) {
            return redirect()->to(base_url('admin/dashboard'));
        }

        return view('admin/login', [
            'title' => 'Painel Administrativo | Boca Santa Ofertas',
        ]);
    }

    public function autenticar()
    {
        $login = trim((string)$this->request->getPost('login'));
        $senha = trim((string)$this->request->getPost('senha'));

        if (empty($login) || empty($senha)) {
            return redirect()->back()->withInput()->with('error', 'Por favor, informe seu usuário/e-mail e senha.');
        }

        $db = Database::connect();
        $admin = $db->table('tb_administradores')
            ->groupStart()
                ->where('adm_login', $login)
                ->orWhere('adm_email', $login)
            ->groupEnd()
            ->get()->getRowArray();

        if ($admin) {
            $senhaValida = password_verify($senha, (string)$admin['adm_senha'])
                || ($admin['adm_senha'] === md5($senha))
                || ($admin['adm_senha'] === $senha);

            if ($senhaValida) {
                session()->set([
                    'admin_logged' => true,
                    'admin_id'     => $admin['adm_id'],
                    'admin_nome'   => $admin['adm_nome'] ?? $admin['adm_login'],
                    'admin_email'  => $admin['adm_email'] ?? '',
                    'admin_nivel'  => $admin['adm_nivel_acesso'] ?? 1,
                ]);

                return redirect()->to(base_url('admin/dashboard'))->with('success', "Bem-vindo ao painel, {$admin['adm_nome']}!");
            }
        }

        return redirect()->back()->withInput()->with('error', 'Usuário ou senha incorretos.');
    }

    public function logout()
    {
        session()->remove(['admin_logged', 'admin_id', 'admin_nome', 'admin_email', 'admin_nivel']);
        return redirect()->to(base_url('admin/login'))->with('success', 'Sessão administrativa encerrada.');
    }
}
