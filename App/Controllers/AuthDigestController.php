<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libs\HttpDigestAuthParser;
use App\Models\User;

class AuthDigestController extends AppController
{
    private const REALM = 'XiOXIvsHBuRMDBvMTF';

    public function login(): void
    {
        if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
            header('HTTP/1.1 401 Unauthorized');
            header('WWW-Authenticate: Digest realm="' . self::REALM . '",qop="auth",nonce="' . uniqid() . '",opaque="' . md5(self::REALM) . '"');

            return;
        }


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


        $a1 = md5(implode(':', [$user['mailAddress'], self::REALM, $user['password']]));
        $a2 = md5(implode(':', [$_SERVER['REQUEST_METHOD'], $data['uri']]));
        $response = md5(implode(':', [$a1, $data['nonce'], $data['nc'], $data['cnonce'], $data['qop'], $a2]));

        if ($data['response'] !== $response) {
            $this->flash->danger('Le mot de passe est invalide');
            $this->redirect('/auth');
        }


        $this->sessionSecurityHandler->regenerateSession();
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
        $this->sessionSecurityHandler->destroySession();

        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Digest realm="' . self::REALM . '",qop="auth",nonce="' . uniqid() . '",opaque="' . md5(self::REALM) . '"');
    }

    public function logoutCancel(): void
    {
        $this->flash->success('Le processus de déconnexion a réussi');
        $this->redirect('/auth');
    }
}
