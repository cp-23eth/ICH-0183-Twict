<?php

declare(strict_types=1);

namespace App\Controllers;

use \App\Helpers\FlashNotificationHelper;
use \App\Libs\SessionSecurityHandler;
use \Core\Controller;
use \Core\View;

abstract class AppController extends Controller
{
    protected \Core\View $view;
    protected \App\Helpers\FlashNotificationHelper $flash;
    protected \App\Libs\SessionSecurityHandler $sessionSecurityHandler;

    protected function before(): bool
    {
        $this->view = new View(templatePath: dirname(__DIR__) . '/views/');
        $this->view->setFilename(strtolower($this->routeParams['controller']) . DIRECTORY_SEPARATOR . strtolower($this->routeParams['action']));

        $this->flash = new FlashNotificationHelper();

        $this->sessionSecurityHandler = new SessionSecurityHandler();
        $this->sessionSecurityHandler->startSession();

        if ($this->sessionSecurityHandler->verifySecurityToken() === false) {
            http_response_code(401);
            return false;
        }

        return parent::before();
    }

    protected function after(): void
    {
        $this->view['debug.session'] = $_SESSION;
        $this->view['debug.cookie'] = $_COOKIE;

        $this->flash->flush($this->view);
        $this->view->render();

        parent::after();
    }

    protected function redirect(string $location, int $code = 302): void
    {
        header("Location: {$location}", true, $code);
        exit;
    }
}
