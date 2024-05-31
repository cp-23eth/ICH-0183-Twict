<?php

declare(strict_types=1);

namespace App\Libs;

use App\Libs\LogHandler;

class CSRFSecurityHandler
{
    protected string $csrfTokenName = '';
    private LogHandler $logger;

    public function __construct(string $csrfTokenName = 'csrf')
    {
        $this->csrfTokenName = $csrfTokenName;

        $this->logger = LogHandler::get(get_class($this));
    }

    public function create(): void
    {
        $_SESSION[$this->csrfTokenName] = self::generateToken();
    }

    public function get(): ?string
    {
        return $_SESSION[$this->csrfTokenName] ?? null;
    }

    public function clear(): void
    {
        unset($_SESSION[$this->csrfTokenName]);
    }

    public function flush(\ArrayAccess &$container): void
    {
        $container['csrf'] = [
            'key' => $this->csrfTokenName,
            'value' => $this->get()
        ];
    }

    public function verifyCsrfToken(array $container): bool
    {
        $actualToken = $container[$this->csrfTokenName] ?? null;
        $expectedToken = $this->get();

        $success = false;

        if ($actualToken !== null && $expectedToken !== null) {
            $success = $expectedToken === $actualToken;
        }

        $this->clear();

        if (!$success) {
            $this->logger->warning('CSRF Token is invalid', ['idUser' => $_SESSION['user']['idUser'] ?? null, 'GET' => $_GET ?? null]);
        }

        return $success;
    }

    private static function generateToken(): string
    {
        $csrfBin = random_bytes(32);
        $csrfToken = bin2hex($csrfBin);

        return $csrfToken;
    }
}
