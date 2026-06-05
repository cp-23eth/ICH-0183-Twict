<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\FinancialTransaction;
use App\Models\BankAccount;
use Override;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class FinancialTransactionController extends AppController
{

    #[Override]
    protected function before(): bool
    {
        parent::before();

        return true;
    }

    public function index(): void
    {
        $log = new Logger('name');
        $log->pushHandler(new StreamHandler('logs/.log'));

        if (!empty($_GET['idSender'])) {
            $log->info('Récupération des transactions financières via l\'id du sender.');
            $this->view['financialTransactions'] = FinancialTransaction::findByIdSender((int)$_GET['idSender']);
        } else if (!empty($_GET['idRecipient'])) {
            $log->notice('Récupération des transactions financières via l\'id du reciient (pas d\'id du sender).');
            $this->view['financialTransactions'] = FinancialTransaction::findByIdRecipient((int)$_GET['idRecipient']);
        } else {
            $log->warning('Récupération de toutes les transactions financières');
            $this->view['financialTransactions'] = FinancialTransaction::getAll();
        }
    }

    public function details(): void
    {
        $idFinancialTransaction = (int)$_GET['id'];
        $this->view['financialTransaction'] = FinancialTransaction::find($idFinancialTransaction);
    }

    public function add(): void
    {
        $this->view['availableBankAccounts'] = BankAccount::getAll();

        $this->csrfSecurityHandler->create();
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

        $this->csrfSecurityHandler->create();
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

        $this->csrfSecurityHandler->create();
    }

    public function remove_post(): void
    {
        $financialTransaction = $_POST['financialTransaction'];
        FinancialTransaction::remove($financialTransaction);

        $this->flash->success('Transaction financière supprimée');
        $this->redirect('/financialTransaction/index');
    }
}
