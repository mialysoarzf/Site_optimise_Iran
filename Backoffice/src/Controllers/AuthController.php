<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Auth;
use App\Csrf;

final class AuthController
{
    public function __construct(private Auth $auth)
    {
    }

    public function loginForm(): void
    {
        if ($this->auth->check()) {
            redirect('/admin/dashboard');
        }

        view('auth/login', [
            'pageTitle' => 'Connexion admin',
            'metaDescription' => 'Connexion au backoffice',
            'csrf' => Csrf::token(),
        ]);
    }

    public function login(): void
    {
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            set_flash('error', 'Token CSRF invalide.');
            redirect('/admin/login');
        }

        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        with_old(['username' => $username]);

        if ($username === '' || $password === '') {
            set_flash('error', 'Identifiants requis.');
            redirect('/admin/login');
        }

        if (!$this->auth->attempt($username, $password)) {
            set_flash('error', 'Identifiants invalides.');
            redirect('/admin/login');
        }

        clear_old();
        set_flash('success', 'Connexion réussie.');
        redirect('/admin/dashboard');
    }

    public function logout(): void
    {
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            set_flash('error', 'Token CSRF invalide.');
            redirect('/admin/dashboard');
        }

        $this->auth->logout();
        session_start();
        set_flash('success', 'Déconnexion réussie.');
        redirect('/admin/login');
    }
}
