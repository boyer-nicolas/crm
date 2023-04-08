<?php

namespace Niwee\Trident\Core;

use Niwee\Trident\Core\Database;
use Niwee\Trident\Core\Utils;
use Niwee\Trident\Core\Security;
use Exception;

/**
 * Companies
 */
final class Users
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
     * Delete a user in the database.
     */
    public function deleteUser()
    {
        $post = Security::filter($_POST);
        extract($post);

        if (empty($user_id))
        {
            throw new Exception("L'identifiant unique de l'utilisateur est obligatoire.");
            return false;
        }

        $request = $this->db->delete(
            'users',
            [
                'id' => $user_id
            ]
        );

        if ($request)
        {
            return true;
        }
        else
        {
            throw new Exception("Une erreur est survenue lors de la suppression de l'utilisateur.");
            return false;
        }
    }

    public function deleteMultipleUsers()
    {
        $post = Security::filter($_POST);
        extract($post);

        if (empty($user_ids))
        {
            throw new Exception("L'identifiant unique de l'utilisateur est obligatoire.");
            return false;
        }

        $user_id_array = explode(',', $user_ids);

        try
        {
            foreach ($user_id_array as $user_id)
            {
                $this->db->delete(
                    'users',
                    [
                        'id' => $user_id
                    ]
                );
            }

            $this->db->query("ALTER TABLE users AUTO_INCREMENT = 1;");
            return true;
        }
        catch (Exception $e)
        {
            throw new Exception($e->getMessage());
            return false;
        }
    }
}
