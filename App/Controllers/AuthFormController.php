<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;

class AuthFormController extends \App\Controllers\AppController
{
    public function login(): void {
        if (isset($_SESSION['user'])) {
            $this->flash->warning('Vous êtes déjà connecté');
            $this->redirect('/auth');
        }
    }

    public function loginPost(): void
    {
        $user = User::findByMailAddressAndPassword($_POST['user']['mailAddress'], $_POST['user']['password']);

        if ($user == null) {
            $this->flash->danger('Le nom d\'utilisateur ou le mot de passe est invalide');
            $this->redirect('./login');
        } else {
            $_SESSION['user'] = $user;
            $this->flash->success('Le processus de connexion a réussi');
            $this->redirect('/auth');
        }
    }

    public function loginCancel(): void {}

    public function logout(): void {
        session_destroy();
        session_start();

        $this->flash->success('Le processus de déconnexion a réussi');
        $this->redirect('/auth');
    }

    public function logoutCancel(): void {}
}
