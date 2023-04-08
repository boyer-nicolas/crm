<?php

namespace Niwee\Trident\Controller\Dashboard\Accounting;

use Niwee\Trident\Core\Quotes;

final class QuotesListController extends \Niwee\Trident\Controller\Dashboard\ViewController
{
    public function __construct(string $title, string $template, bool $content = false, bool $entry = false)
    {
        parent::__construct(
            $template,
            [
                'title' => $title,
                'quotes' => Quotes::getAll()
            ],
            $content,
            $entry
        );
    }
}
