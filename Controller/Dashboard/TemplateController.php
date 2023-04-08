<?php

namespace Niwee\Trident\Controller\Dashboard;

final class TemplateController extends \Niwee\Trident\Controller\Dashboard\ViewController
{
    public function __construct(string $title, string $template, bool $content = false, bool $entry = false)
    {
        parent::__construct(
            $template,
            [
                'title' => $title,
            ],
            $content,
            $entry
        );
    }
}
