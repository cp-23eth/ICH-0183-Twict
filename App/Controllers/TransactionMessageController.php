<?php

declare(strict_types=1);

namespace App\Controllers;


use App\Models\User;
use App\Models\TransactionMessage;
use App\Models\FinancialTransaction;

class TransactionMessageController extends AppController
{
    public function index(): void
    {
        if (!empty($_GET['idTransaction']))
            $this->view['transactionMessages'] = TransactionMessage::findByIdTransaction((int)$_GET['idTransaction']);
        else
            $this->view['transactionMessages'] = TransactionMessage::getAll();
    }

    public function details(): void
    {
        $transactionMessageId = (int)$_GET['id'];
        $this->view['transactionMessage'] = TransactionMessage::find($transactionMessageId);
    }

    public function add(): void
    {
        $this->view['availableAuthors'] = User::getAll();
        $this->view['availableTransactions'] = FinancialTransaction::getAll();

        $this->csrfSecurityHandler->create();
    }

    public function add_post(): void
    {
        $transactionMessage = $_POST['transactionMessage'];
        TransactionMessage::add($transactionMessage);

        $this->flash->success('Message de transaction ajouté');
        $this->redirect('/transactionmessage/index');
    }

    public function edit(): void
    {
        $transactionMessageId = (int)$_GET['id'];
        $this->view['transactionMessage'] = TransactionMessage::find($transactionMessageId);

        $this->view['availableAuthors'] = User::getAll();
        $this->view['availableTransactions'] = FinancialTransaction::getAll();

        $this->csrfSecurityHandler->create();
    }

    public function edit_post(): void
    {
        $transactionMessage = $_POST['transactionMessage'];
        TransactionMessage::update($transactionMessage);

        $this->flash->success('Message de transaction sauvegardé');
        $this->redirect('/transactionmessage/index');
    }

    public function remove(): void
    {
        $transactionMessageId = (int)$_GET['id'];
        $this->view['transactionMessage'] = TransactionMessage::find($transactionMessageId);

        $this->csrfSecurityHandler->create();
    }

    public function remove_post(): void
    {
        $transactionMessage = $_POST['transactionMessage'];
        TransactionMessage::remove($transactionMessage);

        $this->flash->success('Message de transaction supprimé');
        $this->redirect('/transactionMessage/index');
    }
}
