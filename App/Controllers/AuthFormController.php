<?php

declare(strict_types=1);

namespace App\Controllers;

use \App\Models\User;

class AuthFormController extends AppController
{
    public function login(): void
    {
        if (isset($_SESSION['user'])) {
            $this->flash->warning('Vous êtes déjà connecté.');
            $this->redirect('/auth');
        }
    }

    public function login_post(): void
    {
        $user = $_POST['user'];


        $user = User::findByMailAddressAndPassword($user['mailAddress'], $user['password']);

        if ($user == null) {
            $this->flash->danger('Le nom d\'utilisateur est invalide');
            $this->redirect('/authForm/login');
        }


        $this->sessionSecurityHandler->regenerateSession();
        $_SESSION['user'] = $user;
        $this->flash->success('Le processus de connexion a réussi');
        $this->redirect('/auth');
    }


    public function logout(): void
    {
        if (empty($_SESSION['user'])) {
            $this->flash->warning('Vous n\'êtes pas connecté.');
            $this->redirect('/auth');
        }
    }

    public function logout_post(): void
    {
        $this->sessionSecurityHandler->destroySession();

        $this->flash->success('Le processus de déconnexion a réussi');
        $this->redirect('/auth');
    }
}
