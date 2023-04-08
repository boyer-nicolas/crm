<?php

namespace Niwee\Trident\Controller\Dashboard;

use Niwee\Trident\Core\Companies;

final class CompaniesController extends \Niwee\Trident\Controller\Dashboard\ViewController
{
    public function __construct(string $title, string $template, bool $content = false, bool $entry = false)
    {
        $companies = new Companies();

        $this->paginate($companies::count());

        $data = $companies->getAll($this->pagination['min'], $this->pagination['max']);

        if (empty($data))
        {
            $data = "Aucune société n'a été trouvée";
        }

        if (isset($_GET['id']))
        {
            $data = $companies->get($_GET['id']);
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
                'companies' => $data,
                'pagination' => $this->pagination,
                'single' => $single,
            ],
            $content,
            $entry
        );
    }
}
