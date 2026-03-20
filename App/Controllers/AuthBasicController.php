<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;

class AuthBasicController extends AppController
{
    private $realm = 'sample-http-basic-auth-0183';

    public function index(): void
    {
        $this->view;
    }

    public function login(): void
    {
        if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
            header("WWW-Authenticate: Basic realm=\"{$this->realm}\"");
            header('HTTP/1.1 401 Unauthorized', true, 401);

            return;
        }

        $user = User::findByEmailAddressAndPassword($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);

        if (!$user) {
            header('HTTP/1.1 403 Forbidden');
            $this->flash->danger('Le nom d\'utilisateur est invalide');
            $this->redirect('/auth');
        }

        $_SESSION['user'] = $user;
        $this->flash->success('Le processus de connexion a réussi');
        $this->redirect('/auth');
    }

    public function loginCancel(): void
    {
        $this->flash->warning('Le processus de connexion a été annulé');
        $this->redirect('/auth');
    }

    public function logout(): void
    {
        session_destroy();
        session_start();

        // header('WWW-Authenticate: Basic realm="' . $this->realm . '"');
        header('HTTP/1.1 401 Unauthorized', true, 401);

        $this->flash->success('Le processus de déconnexion a réussi');
    }

    public function logoutCancel(): void
    {
        $this->flash->success('Le processus de déconnexion a réussi');
        $this->redirect('/auth');
    }
}
