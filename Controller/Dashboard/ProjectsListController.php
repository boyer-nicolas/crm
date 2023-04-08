<?php

namespace Niwee\Trident\Controller\Dashboard;

use Niwee\Trident\Core\Projects;

final class ProjectsListController extends \Niwee\Trident\Controller\Dashboard\ViewController
{
    public function __construct(string $title, string $template, bool $content = false, bool $entry = false)
    {
        parent::__construct(
            $template,
            [
                'title' => $title,
                'projects' => Projects::getAll(),
            ],
            $content,
            $entry
        );
    }
}
