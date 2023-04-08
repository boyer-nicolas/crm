<?php

namespace Niwee\Trident\API;

use Niwee\Trident\Core\Customers;

final class CustomerController extends ApiController
{
    public function __construct()
    {
        $this->customers = new Customers();
    }

    public function addCustomer()
    {
        $this->customers->addCustomer();
    }

    public function deleteCustomer()
    {
        $this->customers->deleteCustomer();
    }

    public function deleteMultipleCustomers()
    {
        $this->customers->deleteMultipleCustomers();
    }
}
