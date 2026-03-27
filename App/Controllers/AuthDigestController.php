<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libs\HttpDigestAuthParser;
use \App\Models\User;

class AuthDigestController extends AppController
{
    private const REALM = 'oPh?\dRG>B413a;E:5';

    function login()
    {
        // Si les headers sont manquants, demande d'authentification
        if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
            $realm = self::REALM;
            $uniqid = 'uniqid';
            $md5 = 'md5';
            header('HTTP/1.1 401 Unauthorized');
            header("WWW-Authenticate: Digest realm=\"{$realm}\",qop=\"auth\",nonce=\"{$uniqid()}\",opaque=\"{$md5(self::REALM)}\"");

            return;
        };

        $data = HttpDigestAuthParser::parse($_SERVER['PHP_AUTH_DIGEST']);

        if ($data === false) {
            $this->flash->danger('La méthode d\'authentification n\'est pas supportée.');
            $this->redirect('/auth');
        }

        $user = User::findByMailAddress($data['username']);

        if ($user == null) {
            $this->flash->danger('Le nom d\'utilisateur est invalide');
            $this->redirect('/auth');
        }

        // création du hash Digest côté serveur
        $A1 = md5(implode(':', [
            $data['username'],
            self::REALM,
            $user['password']
        ]));

        $A2 = md5(implode(':', [
            $_SERVER['REQUEST_METHOD'],
            $data['uri']
        ]));

        $validResponse = md5(implode(':', [
            $A1,
            $data['nonce'],
            $data['nc'],
            $data['cnonce'],
            $data['qop'],
            $A2
        ]));

        // Comparaison du hash côté client et côté serveur
        if ($data['response'] !== $validResponse) {
            header('HTTP/1.1 403 Forbidden');
            die('Identifiants incorrects');
        }

        $_SESSION['user'] = $user;
        $this->flash->success('Le processus de connexion a réussi');
        $this->redirect('/auth');
    }

    function logout()
    {
        session_destroy();
        session_start();
        $_SERVER['PHP_AUTH_DIGEST'] = null;
        $this->flash->success('Le processus de déconnexion a réussi');
        header('HTTP/1.1 401 Unauthorized');
    }

    function loginCancel()
    {
        $this->flash->warning('Le processus de connexion a été annulé');
        $this->redirect('/auth');
    }

    function logoutCancel()
    {
        $this->flash->success('Le processus de déconnexion a réussi');
        $this->redirect('/auth');
    }
}
