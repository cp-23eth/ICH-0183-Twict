<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;

class UserController extends AppController
{
    public function index(): void
    {
        $users = User::getAll();
        $this->view['users'] = $users;
    }

    public function add(): void
    {
    }

    public function add_post(): void
    {
        $user = $_POST['user'];
        User::add($user);

        $this->flash->success('Utilisateur ajouté');

        $this->redirect('/user/index');
    }

    public function edit(): void
    {
        $id = (int)$_GET['id'];
        $this->view['user'] = User::find($id);
    }

    public function edit_post(): void
    {
        $user = $_POST['user'];
        User::update($user);

        $this->flash->success('Utilisateur mise à jour');

        $this->redirect('/user/index');
    }

    public function remove(): void
    {
        $userId = (int)$_GET['id'];
        $this->view['user'] = User::find($userId);
    }

    public function remove_post(): void
    {
        $user = $_POST['user'];

        if (!User::remove($user)) {
            $this->flash->warning('Erreur lors de la suppression de l\'utilisateur');
            $this->redirect('/user/index');
        }
        
        User::remove($user);
        $this->flash->success('Utilisateur supprimé');
        $this->redirect('/user/index');
    }
}
