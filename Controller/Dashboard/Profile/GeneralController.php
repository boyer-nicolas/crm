<?php

namespace Niwee\Trident\Controller\Dashboard\Profile;

use Niwee\Trident\Core\Profile;
use Niwee\Trident\Core\Session;

final class GeneralController extends \Niwee\Trident\Controller\Dashboard\ViewController
{
    public function __construct(string $title, string $template, bool $content = false, bool $entry = false)
    {
        Session::start();


        $id = $_SESSION['id'];
        parent::__construct(
            $template,
            [
                'title' => $title,
                'user' => Profile::get($id),
            ],
            $content,
            $entry
        );
    }
}
