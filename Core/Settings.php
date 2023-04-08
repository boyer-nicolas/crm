<?php

namespace Niwee\Trident\Core;

use Niwee\Trident\Core\Database;
use Niwee\Trident\Core\Utils;

final class Settings
{
    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all customers
     */
    public static function getAll()
    {
        $db = new Database();

        $select = $db->select(
            'settings',
            [
                '[>]companies' => ['default_pdf_from_company' => 'id'],
            ],
            [
                'settings' => [
                    'settings.default_pdf_accent_color',
                    'settings.default_pdf_tz',
                    'settings.default_pdf_invoice_due_after_weeks',
                    'settings.default_pdf_logo',
                    'settings.default_pdf_reference_prefix',
                    'settings.default_pdf_from_company',
                    'settings.admin_api_token',
                ],
                'linked_company' => [
                    'companies.id',
                    'companies.name',
                ]
            ]
        );

        // Flatten the array to get the right keys later
        $settings = Utils::flatten($select);

        $settings['companies_available'] = $db->select(
            'companies',
            [
                'id',
                'name',
            ],
            [
                'id[!]' => $settings['linked_company']['id'],
            ]
        );

        return $settings;
    }

    /**
     * Get a single customer
     *
     * @param int $id The customer id
     */
    public static function get(string $param)
    {
        $db = new Database();
        return $db->select(
            'services',
            [
                $param,
            ]
        );
    }
}
