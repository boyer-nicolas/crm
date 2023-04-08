<?php

namespace Niwee\Trident\Core;

use Niwee\Trident\Core\Database;
use Niwee\Trident\Core\Utils;

/**
 *
 */
final class Profile
{
    /**
     * @var \Niwee\Trident\Core\Database
     */
    private $db;

    /**
     * @var \Niwee\Trident\Core\Utils
     */
    public function __construct()
    {
        $this->db = new Database();
        $db = $this->db;
    }

    /**
     * Get all customers
     */
    public static function get(int $id)
    {
        $db = new Database();

        $select = $db->select(
            'users',
            [
                '[>]companies' => ['company_id' => 'id'],
            ],
            [
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.avatar',
                'users.password',
                'users.phone',
                'users.company_id',
                'users.role_id',
                'linked_company' => [
                    'companies.id',
                    'companies.name',
                ]
            ],
            [
                'users.id' => $id
            ]
        );

        // Flatten the array to get the right keys later
        return Utils::flatten($select);
    }
}
