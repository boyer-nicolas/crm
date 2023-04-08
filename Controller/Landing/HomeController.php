<?php

namespace Niwee\Trident\Controller\Landing;

final class HomeController extends ViewController
{
    public function __construct(string $title, string $template, bool $content = false, bool $entry = false)
    {
        parent::__construct(
            $template,
            [],
            $content,
            $entry
        );
    }
}
