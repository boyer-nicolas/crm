<?php

namespace Niwee\Trident\Core;

use Niwee\Trident\Core\Database;
use \Exception;

final class Session
{
    public function __construct()
    {
        $this->db = new Database();
    }

    public static function start()
    {
        if (!isset($_SESSION))
        {
            session_start();
        }
    }

    public function populate(string $email)
    {
        try
        {
            // If the email and password are valid, set the session id to the user's id.
            $user_data = $this->db->get(
                'users',
                [
                    '[>]roles' => [
                        'role_id' => 'id'
                    ]
                ],
                [
                    'users.id(id)',
                    'users.first_name(first_name)',
                    'users.ui_theme(theme)',
                    'users.last_name(last_name)',
                    'users.email(email)',
                    'users.avatar(avatar)',
                    'roles.name(role)'
                ],
                [
                    'email' => $email
                ],
                null
            );

            if ($user_data)
            {
                foreach ($user_data as $key => $data)
                {
                    $_SESSION["$key"] = $data;
                }

                $_SESSION['logged_in'] = true;
            }
        }
        catch (Exception $e)
        {
            // If the session_active could not be set, log the error.
            Utils::ajax_message($e->getMessage(), 'error');
            throw new Exception($e->getMessage());
        }

        try
        {
            // Set the session_active to true in the database.
            $this->db->update(
                'users',
                [
                    'session_active' => 1
                ],
                [
                    'id' => $_SESSION['id']
                ]
            );
        }
        catch (Exception $e)
        {
            // If the session_active could not be set, log the error.
            $this->logger->error($e->getMessage(), false);
            Utils::ajax_message($e->getMessage(), 'error');
        }
    }
}
