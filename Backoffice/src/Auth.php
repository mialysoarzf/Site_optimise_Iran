<?php

declare(strict_types=1);

namespace App;

use App\Repositories\UserRepository;

final class Auth
{
    public function __construct(private UserRepository $users)
    {
    }

    public function check(): bool
    {
        return !empty($_SESSION['admin_user_id']);
    }

    public function id(): ?int
    {
        return isset($_SESSION['admin_user_id']) ? (int) $_SESSION['admin_user_id'] : null;
    }

    public function attempt(string $username, string $password): bool
    {
        $user = $this->users->findByUsername($username);
        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['admin_user_id'] = (int) $user['id'];
        $_SESSION['admin_username'] = (string) $user['username'];

        return true;
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
        }

        session_destroy();
    }
}
