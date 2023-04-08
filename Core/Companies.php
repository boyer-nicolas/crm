<?php

namespace Niwee\Trident\Core;

use Niwee\Trident\Core\Database;

/**
 * Companies
 */
final class Companies
{
    /**
     * @var \Niwee\Trident\Core\Database
     */
    private $db;

    public function __construct()
    {
        $this->db = new Database();
        $db = $this->db;
    }

    /**
     * Get all companies
     */
    public static function getAll(int $rangeStart, int $rangeEnd): ?array
    {
        $db = new Database();

        return $db->select(
            'companies',
            [
                'id',
                'name',
                'address_line_1',
                'address_line_2',
                'city',
                'logo',
                'state',
                'zip',
                'country',
            ],
            [
                "LIMIT" => [$rangeStart, $rangeEnd],
                'ORDER' => ['id' => 'ASC'],
            ],
            null,
            null
        );
    }

    /**
     * Get a single company
     */
    public static function get(int $id): ?array
    {
        $db = new Database();

        $request = $db->select(
            'companies',
            [
                'id',
                'name',
                'address_line_1',
                'address_line_2',
                'city',
                'logo',
                'state',
                'zip',
                'country',
            ],
            [
                'id' => $id,
            ],
            null,
            null
        );

        return Utils::flatten($request);
    }

    /**
     * Get the number of companies
     */
    public static function count(): ?int
    {
        $db = new Database();
        return $db->count('companies');
    }
}
