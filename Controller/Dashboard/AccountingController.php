<?php

namespace Niwee\Trident\Controller\Dashboard;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * final class AccountingController
 *
 * @route("/admin/compabilite")
 */
final class AccountingController extends \Niwee\Trident\Controller\Dashboard\ViewController
{
    /**
     * @param  string $title
     * @param  string $template
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
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
