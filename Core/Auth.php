<?php

namespace Niwee\Trident\Core;

use Exception;
use Niwee\Trident\Core\Database;
use Niwee\Trident\Core\Utils;
use Niwee\Trident\Core\Config;
use Niwee\Trident\Core\Logger;
use Niwee\Trident\Core\Session;
use Niwee\Trident\Core\Security;

/**
 * Auth
 */
final class Auth
{
    public function __construct()
    {
        $this->db = new Database();
        $this->logger = new Logger();
        $this->session = new Session();
        $this->routes = Config::get('routes');
    }

    public function is_logged_in(): ?bool
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        {
            return true;
        }
        if (!isset($_SESSION))
        {
            session_start();
        }

        // Check if the session user_id is set.
        if (!isset($_SESSION['id']))
        {
            // Session user_id not set, not logged in.
            return false;
        }

        // Session user_id is set, check if the session_active entry in the database is set to true.
        $check = $this->db->get(
            'users',
            [
                'id',
                'session_active'
            ],
            [
                'id' => $_SESSION['id']
            ]
        );

        // If true, the user is logged in, return true. Otherwise: not logged in due to timeout, return false.
        if ($check['session_active'] == 1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function login()
    {
        $post = Security::filter($_POST);
        extract($post);

        if (!isset($_SESSION))
        {
            session_start();
        }

        // If the session started more than 30 minutes ago, kill it (reset max attempts).
        if (isset($_SESSION['start']) && (time() - $_SESSION['start'] > 1800))
        {
            session_unset();
            session_destroy();
        }
        else
        {
            $_SESSION['start'] = time();
        }

        // Set the session max tries.
        if (!isset($_SESSION['login_max_tries']))
        {
            $_SESSION['login_max_tries'] = 0;
        }

        // If there are too many tries, prevent further connections (10 max).
        if ($_SESSION['login_max_tries'] > 10)
        {
            Utils::ajax_message("Vous avez essayé de vous connecter trop de fois avec des identifiants erronnés. Veuillez ré-essayer dans 30 minutes.", 'error');
            return false;
        }

        // Check if the user has submitted the login form.
        if (isset($email, $password))
        {
            // Check if the username and password are both set.
            if ($email != '' && $password != '')
            {
                // Check if the email and password are valid.
                if ($this->validate_email($email))
                {
                    if ($this->validate_password($email, $password))
                    {
                        // Check if the email and password are correct.
                        if ($this->validate_login($email, $password))
                        {
                            // Login successful, populate session.
                            $this->session->populate($email);

                            if (isset($remember))
                            {
                                // Set the session cookie to expire in one week.
                                setcookie('user_id', $_SESSION['id'], time() + 60 * 60 * 24 * 7);

                                try
                                {
                                    $this->db->update(
                                        'users',
                                        [
                                            'session_validity' => time() + 60 * 60 * 24 * 7
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

                            $_SESSION['login_max_tries'] = 0;
                            Utils::ajax_message("Redirection...", "success", null, false, "", $this->routes->dashboard->children->home->uri);
                        }
                        else
                        {
                            // Login failed.
                            $_SESSION['login_max_tries']++;
                            Utils::ajax_message("Cet identifiant n'existe pas.", "error");
                        }
                    }
                    else
                    {
                        // Password is invalid.
                        $_SESSION['login_max_tries']++;
                        Utils::ajax_message("Le mot de passe est erronné.", "error");
                    }
                }
                else
                {
                    // If the username and password are not valid, send failure message.
                    $_SESSION['login_max_tries']++;
                    Utils::ajax_message("Cet identifiant n'existe pas.", 'error');
                }
            }
            else
            {
                // If the username or password is not set, send failure message.
                $_SESSION['login_max_tries']++;
                Utils::ajax_message('Veuillez renseigner vos identifiants.', 'error');
            }
        }
        else
        {
            // If the username or password is not set, send failure message.
            $_SESSION['login_max_tries']++;
            Utils::ajax_message('Veuillez renseigner vos identifiants.', 'error');
        }
    }

    public function validate_login(string $email, string $password): bool
    {
        // Check if the email and password are valid.
        $check = $this->db->has(
            'users',
            [
                'AND' => [
                    'email' => $email,
                    'password' => $password
                ]
            ]
        );

        // If true, the email and password are valid, return true. Otherwise, return false.
        if ($check)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function validate_email(string $email): bool
    {
        // Check if the email is valid.
        if (filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $check_exists = $this->db->has(
                'users',
                [
                    'email' => $email
                ]
            );

            if ($check_exists)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    public function validate_password(string $email, string $password): bool
    {
        // Check if the password is valid.
        if (strlen($password) >= 8)
        {
            $check_validity = $this->db->has(
                'users',
                [
                    "AND" => [
                        "email" => $email,
                        "password" => $password
                    ]
                ]
            );

            if ($check_validity)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    public function logout()
    {
        $_SESSION['logged_in'] = false;
        if (!isset($_SESSION))
        {
            session_start();
        }
        session_destroy();
        setcookie('user_id', '', time() - 3600);
        Utils::ajax_message("A bientôt.", "success", null, false, "", $this->routes->login->uri);
    }
}
