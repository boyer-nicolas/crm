<?php

namespace Niwee\Trident\Controller;

use Niwee\Trident\Core\Config;

final class ISEController extends ViewController
{
    public function __construct(string $message)
    {
        $config = Config::get('app');
        if ($config->debug === true)
        {
            parent::__construct(
                'Pages/ISE',
                [
                    'title' => 'Erreur 500',
                    'message' => $message,
                ]
            );
        }
        else
        {
            parent::__construct(
                'Pages/ISE',
                [
                    'title' => 'Erreur 500',
                ]
            );
        }
    }
}
