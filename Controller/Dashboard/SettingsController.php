<?php

namespace Niwee\Trident\Controller\Dashboard;

use Niwee\Trident\Core\Settings;

final class SettingsController extends \Niwee\Trident\Controller\Dashboard\ViewController
{
    public function __construct(string $title, string $template, bool $content = false, bool $entry = false)
    {
        parent::__construct(
            $template,
            [
                'title' => $title,
                'params' => Settings::getAll(),
            ],
            $content,
            $entry
        );
    }
}
