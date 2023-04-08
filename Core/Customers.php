<?php

namespace Niwee\Trident\Core;

use Niwee\Trident\Core\Database;
use Niwee\Trident\Core\Utils;
use Niwee\Trident\Core\Admins;
use Niwee\Trident\Core\Security;

/**
 * Companies
 */
final class Customers
{
    /**
     * @var \Niwee\Trident\Core\Database
     */
    private $db;

    public function __construct()
    {
        $this->db = new Database();
        $this->users = new Users();
    }

    /**
     * Get all customers
     */
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
                'users.first_name',
                'users.last_name',
                'users.avatar',
                'users.phone',
                'users.facebook',
                'users.instagram',
                'users.twitter',
                'users.linkedin',
                'users.bio',
                'users.created_at',
                'users.updated_at',
                'users.email',
                'roles.id',
                'roles.name(role)',
                'companies.id(company_id)',
                'companies.name(company)',
            ],
            [
                'roles.id' => '2',
                "LIMIT" => [$rangeStart, $rangeEnd],
                'ORDER' => ['users.id' => 'ASC'],
            ]
        );
    }

    /**
     * Get a single customer
     *
     * @param int $id The customer id
     */
    public function get(int $id): ?array
    {
        $request = $this->db->select(
            'users',
            [
                "[>]roles" => ["role_id" => "id"],
                "[>]companies" => ["company_id" => "id"]
            ],
            [
                'users.id(user_id)',
                'users.first_name',
                'users.last_name',
                'users.phone',
                'users.facebook',
                'users.instagram',
                'users.twitter',
                'users.linkedin',
                'users.bio',
                'users.avatar',
                'users.created_at',
                'users.updated_at',
                'users.email',
                'roles.id',
                'roles.name(role)',
                'companies.id(company_id)',
                'companies.name(company)',
            ],
            [
                'roles.id' => '2',
                'users.id' => $id
            ]
        );

        return Utils::flatten($request);
    }

    /**
     * Get the number of customers
     */
    public static function count(): ?int
    {
        $db = new Database();
        return $db->count(
            'users',
            [
                'role_id' => '2'
            ]
        );
    }

    /**
     * Add a customer
     */
    public function addCustomer()
    {
        $post = Security::filter($_POST);
        extract($post);

        foreach ($post as $key => $value)
        {
            if (empty($value))
            {
                Utils::ajax_message($key, 'Ce champ est obligatoire');
            }
        }

        $user_exists = $this->db->has(
            'users',
            [
                'email' => $email
            ]
        );

        if ($user_exists)
        {
            Utils::ajax_message("Cette adresse email est déjà utilisée.", 'error');
            exit;
        }

        try
        {
            $request = $this->db->insert(
                'users',
                [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'password' => $password,
                    'phone' => $phone,
                    'email' => $email,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'role_id' => '2',
                    'company_id' => '1'
                ]
            );

            if ($request)
            {
                Utils::ajax_message("Le client a bien été ajouté.", 'success', null, true);
            }
            else
            {
                Utils::ajax_message("Le client n'a pas pu être ajouté.", 'error', null, false);
            }
        }
        catch (\Exception $e)
        {
            Utils::ajax_message($e->getMessage(), 'error', null, false);
        }
    }

    /**
     * Delete a single customer
     */
    public function deleteCustomer()
    {
        try
        {
            $this->users->deleteUser();
            $this->updateCustomerIds();

            Utils::ajax_message("Le client a bien été supprimé.", 'success', null, true);
        }
        catch (\Exception $e)
        {
            Utils::ajax_message($e->getMessage(), 'error', null, false);
        }
    }

    /**
     * Delete multiple Customers
     */
    public function deleteMultipleCustomers()
    {
        try
        {
            $this->users->deleteMultipleUsers();
            $this->updateCustomerIds();

            Utils::ajax_message("Les clients ont bien été supprimés.", 'success', null, true);
        }
        catch (\Exception $e)
        {
            Utils::ajax_message($e->getMessage(), 'error', null, false);
        }
    }

    /**
     * Update users IDs when deleting a customer
     */
    public function updateCustomerIds()
    {
        $all_customers = $this->getAll(0, 1000000);

        $admin_max_id = Admins::count();

        $new_id = $admin_max_id + 1;

        foreach ($all_customers as $customer)
        {
            $this->db->update(
                'users',
                [
                    'id' => $new_id
                ],
                [
                    'id' => $customer['user_id']
                ]
            );
            $new_id++;
        }

        $this->db->query("ALTER TABLE users AUTO_INCREMENT = $new_id");
    }
}
