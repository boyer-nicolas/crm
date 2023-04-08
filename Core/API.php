<?php

namespace Niwee\Trident\Core;

use Exception;
use Niwee\Trident\Core\Database;
use Niwee\Trident\Core\Utils;
use Niwee\Trident\Core\Env;

/**
 * Companies
 */
final class API
{
    private $db;

    /**
     * @throws Exception
     */
    public function __construct(string $token)
    {
        // If the API token is fewer than 40 characters, exit.
        if (empty($token) || strlen($token) != 40)
        {
            header('HTTP/1.1 401 Unauthorized');
            exit;
        }

        // Start the session
        session_start();

        // If the session started more than 30 minutes ago, kill it.
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
        if (!isset($_SESSION['api_connect_tries']))
        {
            $_SESSION['api_connect_tries'] = 0;
        }


        // If there are too many tries, prevent further connections.
        if ($_SESSION['api_connect_tries'] > 5)
        {
            throw new Exception('Too many tries, retry in 30 minutes.');
        }

        // Initialize the database.
        $this->db = new Database();

        // Check the token.
        $request = $this->db->select(
            'settings',
            [
                'admin_api_token',
            ],
            [
                'admin_api_token' => $token,
            ],
            null
        );

        $request_flattened = Utils::flatten($request);

        // If the token is valid, continue.
        if ($request_flattened['admin_api_token'] === $token)
        {
            return true;
        }
        else
        {
            // The token is invalid, increment the tries and throw 401.
            $_SESSION['api_connect_tries']++;
            header('HTTP/1.1 403 Forbidden');
            throw new Exception('Invalid token.');
        }
    }
}
