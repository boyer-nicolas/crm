<?php

namespace Niwee\Trident\Core;

use Niwee\Trident\Core\Database;
use Niwee\Trident\Core\Utils;

final class Admins
{
    /**
     * @var \Niwee\Trident\Core\Database
     */
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public static function count()
    {
        $db = new Database();
        return $db->count(
            'users',
            [
                'role_id' => '1'
            ]
        );
    }

    public function getAll(int $rangeStart, int $rangeEnd): ?array
    {
        return $this->db->select(
            'users',
            [
                "[>]roles" => ["role_id" => "id"],
                "[>]companies" => ["company_id" => "id"]
            ],
            [
                'users.id(user_id)',
                'users.avatar',
                'users.first_name',
                'users.last_name',
                'users.phone',
                'users.created_at',
                'users.updated_at',
                'users.email',
                'users.company_id',
                'roles.id(role_id)',
                'companies.name(company)'
            ],
            [
                'roles.id' => '1',
                "LIMIT" => [$rangeStart, $rangeEnd],
                'ORDER' => ['users.id' => 'ASC'],
            ]
        );
    }

    public function get(int $id)
    {
        $admin = $this->db->select(
            'users',
            [
                "[>]roles" => ["role_id" => "id"],
                "[>]companies" => ["company_id" => "id"]
            ],
            [
                'users.id(user_id)',
                'users.avatar',
                'users.first_name',
                'users.last_name',
                'users.phone',
                'users.created_at',
                'users.updated_at',
                'users.email',
                'users.company_id',
                'roles.id(role_id)',
                'companies.name(company)'
            ],
            [
                'users.id' => $id
            ]
        );

        return Utils::flatten($admin);
    }
}
