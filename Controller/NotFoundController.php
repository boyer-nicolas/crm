<?php

namespace Niwee\Trident\Controller;

final class NotFoundController extends \Niwee\Trident\Controller\Landing\ViewController
{
    public function __construct()
    {
        parent::__construct(
            'Pages/404',
            [
                'title' => 'Page non trouvée',
            ],
            false
        );
    }
}
