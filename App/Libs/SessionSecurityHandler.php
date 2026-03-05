<?php

declare(strict_types=1);

namespace App\Libs;

class SessionSecurityHandler
{
    protected string $sessionName;
    protected string $securityTokenName;

    public function __construct(string $sessionName = 'session-id', string $securityTokenName = 'session-secure')
    {
        $this->sessionName = $sessionName;
        $this->securityTokenName = $securityTokenName;
    }

    public function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_name($this->sessionName);
        session_start([
            'cookie_httponly' => true,
            'cookie_samesite' => 'strict',
            'use_cookies' => true,
            'use_only_cookies' => true,
            'use_strict_mode' => true,
            'use_trans_sid' => false
        ]);

        $this->setSecurityToken();
    }

    public function regenerateSession(): void
    {
        session_regenerate_id(true);
        $this->setSecurityToken(true);
    }

    public function destroySession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            return;
        }

        session_destroy();
    }


    public function verifySecurityToken(): bool
    {
        $actual = $this->computeSecurityToken();
        $expected = $this->getSecurityToken();

        $success = $actual === $expected;

        if ($success === false) {
            $this->destroySession();
        }

        return $success;
    }

    private function getSecurityToken(): ?string
    {
        return isset($_SESSION[$this->securityTokenName])
            ? $_SESSION[$this->securityTokenName]
            : null;
    }

    private function setSecurityToken(bool $force = false): void
    {
        if ($this->getSecurityToken() !== null && $force !== true) {
            return;
        }

        $_SESSION[$this->securityTokenName] = $this->computeSecurityToken();
    }

    private function computeSecurityToken(): string
    {
        $securityToken = md5(implode('', [
            session_id(),
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT']
        ]));

        return $securityToken;
    }
}
