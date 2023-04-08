<?php

namespace Niwee\Trident\Controller\Dashboard;

use Niwee\Trident\Core\Services;

final class ServicesListController extends \Niwee\Trident\Controller\Dashboard\ViewController
{
    public function __construct(string $title, string $template, bool $content = false, bool $entry = false)
    {
        $customers = new Services();
        $customers = $customers->getAll();

        parent::__construct(
            $template,
            [
                'title' => $title,
                'services' => $customers,
            ],
            $content,
            $entry
        );
    }
}
