<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\BankAccount;
use App\Models\User;

class BankAccountController extends AppController
{
    public function index(): void
    {
        $this->view['bankAccounts'] = !empty($_GET['idOwner'])
            ? BankAccount::findByIdOwner((int)$_GET['idOwner'])
            : BankAccount::getAll();
    }

    public function details(): void
    {
        $id = (int)$_GET['id'];
        $this->view['bankAccount'] = BankAccount::find($id);
    }

    public function add(): void
    {
        $this->view['availableOwners'] = User::getAll();
    }

    public function add_post(): void
    {
        $bankAccount = $_POST['bankAccount'];
        BankAccount::add($bankAccount);

        $this->flash->success('Compte bancaire ajouté');

        $this->redirect('/bankaccount/index');
    }

    public function edit(): void
    {
        $bankAccountId = (int)$_GET['id'];
        $this->view['bankAccount'] = BankAccount::find($bankAccountId);
        $this->view['availableOwners'] = User::getAll();
    }

    public function edit_post(): void
    {
        $bankAccount = $_POST['bankAccount'];
        BankAccount::update($bankAccount);

        $this->flash->success('Compte bancaire sauvegardé');
        $this->redirect('/bankaccount/index');
    }

    public function remove(): void
    {
        $bankAccountId = (int)$_GET['id'];
        $this->view['bankAccount'] = BankAccount::find($bankAccountId);
    }

    public function remove_post(): void
    {
        $bankAccount = $_POST['bankAccount'];
        BankAccount::remove($bankAccount);

        $this->flash->success('Compte bancaire supprimé');
        $this->redirect('/bankaccount/index');
    }
}
