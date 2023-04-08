<?php

namespace Niwee\Trident\Core;

use Niwee\Trident\Core\Database;

final class Services
{
    /**
     * @var \Niwee\Trident\Core\Database
     */
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all customers
     */
    public function getAll()
    {
        return $this->db->select(
            'services',
            [
                'services.id',
                'services.name',
                'services.description',
                'services.price',
            ],
            null,
            null
        );
    }

    /**
     * Get a single customer
     *
     * @param int $id The customer id
     */
    public function get(int $id): ?array
    {
        return $this->db->select(
            'services',
            [
                'services.id',
                'services.name',
                'services.description',
                'services.price',
            ],
            [
                'services.id' => $id
            ]
        );
    }

    /**
     * Get the number of customers
     */
    public function count(): ?int
    {
        return $this->db->count(
            'services',
            [
                'services.id'
            ]
        );
    }
}
