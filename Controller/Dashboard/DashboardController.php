<?php

namespace Niwee\Trident\Controller\Dashboard;

use Niwee\Trident\Core\Customers;
use Niwee\Trident\Core\Companies;
use Niwee\Trident\Core\Quotes;
use Niwee\Trident\Core\Invoices;
use Niwee\Trident\Core\Deposits;

final class DashboardController extends \Niwee\Trident\Controller\Dashboard\ViewController
{
    public function __construct(string $title, string $template, bool $content = false, bool $entry = false)
    {
        parent::__construct(
            $template,
            [
                'title' => $title,
                'customers' => Customers::count(),
                'companies' => Companies::count(),
                'quotes' => Quotes::count(),
                'invoices' => Invoices::count(),
                'deposits' => Deposits::count(),
            ],
            $content,
            $entry
        );
    }
}
