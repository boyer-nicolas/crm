<?php

namespace Niwee\Trident\Controller\Landing;

use Niwee\Trident\Core\Config;
use Niwee\Trident\Core\Auth;
use Niwee\Trident\Core\Utils;

final class LoginController extends ViewController
{
    public function __construct(string $title, string $template, bool $content = false, bool $entry = false)
    {
        $this->auth = new Auth();
        $this->routes = Config::get('routes');
        $this->config = Config::get('app');

        if ($this->auth->is_logged_in())
        {
            Utils::redirect($this->routes->dashboard->base);
        }

        if (Utils::getDevice() == 'mobile')
        {
            $this->viewfolder = "Mobile/";
        }
        else
        {
            $this->viewfolder = "Desktop/";
        }

        parent::__construct(
            $template,
            [],
            $content,
            $entry
        );
    }
}
