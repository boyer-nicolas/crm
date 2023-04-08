<?php

namespace Niwee\Trident\Controller\Dashboard;

use Niwee\Trident\Core\Customers;

final class CustomersController extends \Niwee\Trident\Controller\Dashboard\ViewController
{
    public function __construct(string $title, string $template, bool $content = false, bool $entry = false)
    {
        $customers = new Customers();

        $this->paginate($customers::count());
        $data = $customers->getAll($this->pagination['min'], $this->pagination['max']);

        if (empty($data))
        {

            $data = "Aucun client n'a été trouvé";
        }

        if (isset($_GET['id']))
        {
            $data = $customers->get($_GET['id']);
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
                'customers' => $data,
                'pagination' => $this->pagination,
                'single' => $single,
            ],
            $content,
            $entry
        );
    }
}
