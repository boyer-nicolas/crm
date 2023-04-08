<?php

namespace Niwee\Trident\Controller\Dashboard;

use Niwee\Trident\Core\Admins;

/**
 * final class AdminsListController
 *
 * @route /admin/admins
 */
final class AdminsListController extends \Niwee\Trident\Controller\Dashboard\ViewController
{
    public function __construct(string $title, string $template, bool $content = false, bool $entry = false)
    {
        $admins = new Admins();

        $this->paginate($admins::count());

        $data = $admins->getAll($this->pagination['min'], $this->pagination['max']);

        if (empty($data))
        {
            $data = "Aucun administrateur n'a été trouvé";
        }

        if (isset($_GET['id']))
        {
            $data = $admins->get($_GET['id']);
            $single = true;
        }
        else
        {
            $single = false;
        }

        parent::__construct(
            $template,
            [
                'title' => $title,
                'admins' => $data,
                'pagination' => $this->pagination,
                'single' => $single,
            ],
            $content,
            $entry
        );
    }
}
