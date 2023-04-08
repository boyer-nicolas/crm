<?php

namespace Niwee\Trident\API;

use Niwee\Trident\Core\API;
use Niwee\Trident\Core\Utils;
use Niwee\Trident\Core\Security;
use Niwee\Trident\Core\Config;

abstract class ApiController
{
    public function __construct(string $message, bool $success)
    {
        $this->config = Config::get('app');
        $this->security = new Security();
        if ($_SERVER['HTTP_HOST'] != $this->config->url)
        {
            $token = $this->security->getBearerToken();
            // If there is an API token, set it in a variable, else throw 403.
            if ($token === null)
            {
                header('HTTP/1.0 403 Forbidden');
            }
            else
            {
                // Init the API auth check with the provided token.
                $api_check = new API($token);

                // If the API auth check fails, end output and throw 403.
                if (!$api_check)
                {
                    ob_end_clean();
                    header('HTTP/1.0 403 Forbidden');
                }
            }
        }
        Utils::ajax_message($message, $success);
    }
}
