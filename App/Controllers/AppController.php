<?php

declare(strict_types=1);

namespace App\Controllers;

use \App\Helpers\FlashNotificationHelper;
use \Core\Controller;
use \Core\View;

use function Core\Libs\str_lower_camel_case;

abstract class AppController extends Controller
{
    protected View $view;
    protected FlashNotificationHelper $flash;

    protected function before(): bool
    {
        $this->view = new View(templatePath: dirname(__DIR__) . '/Views/');
        $this->view->setFilename(str_lower_camel_case($this->routeParams['controller']) . DIRECTORY_SEPARATOR . str_lower_camel_case($this->routeParams['action']));

        $this->flash = new FlashNotificationHelper();

        return parent::before();
    }

    protected function after(): void
    {
        $this->view['debug.session'] = $_SESSION;

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
