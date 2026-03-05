<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\FinancialTransaction;
use App\Models\BankAccount;

class FinancialTransactionController extends AppController
{
    public function index(): void
    {
        if (!empty($_GET['idSender']))
            $this->view['financialTransactions'] = FinancialTransaction::findByIdSender((int)$_GET['idSender']);
        else if (!empty($_GET['idRecipient']))
            $this->view['financialTransactions'] = FinancialTransaction::findByIdRecipient((int)$_GET['idRecipient']);
        else
            $this->view['financialTransactions'] = FinancialTransaction::getAll();
    }

    public function details(): void
    {
        $idFinancialTransaction = (int)$_GET['id'];
        $this->view['financialTransaction'] = FinancialTransaction::find($idFinancialTransaction);
    }

    public function add(): void
    {
        $this->view['availableBankAccounts'] = BankAccount::getAll();
    }

    public function add_post(): void
    {
        $financialTransaction = $_POST['financialTransaction'];
        FinancialTransaction::add($financialTransaction);

        $this->flash->success('Transaction financière ajoutée');
        $this->redirect('/financialTransaction/index');
    }

    public function edit(): void
    {
        $financialTransactionId = (int)$_GET['id'];
        $this->view['financialTransaction'] = FinancialTransaction::find($financialTransactionId);

        $this->view['availableBankAccounts'] = BankAccount::getAll();
    }

    public function edit_post(): void
    {
        $financialTransaction = $_POST['financialTransaction'];
        FinancialTransaction::update($financialTransaction);

        $this->flash->success('Transaction financière sauvegardée');
        $this->redirect('/financialTransaction/index');
    }

    public function remove(): void
    {
        $financialTransactionId = (int)$_GET['id'];
        $this->view['financialTransaction'] = FinancialTransaction::find($financialTransactionId);
    }

    public function remove_post(): void
    {
        $financialTransaction = $_POST['financialTransaction'];
        FinancialTransaction::remove($financialTransaction);

        $this->flash->success('Transaction financière supprimée');
        $this->redirect('/financialTransaction/index');
    }
}
