<?php

namespace Niwee\Trident\Core;

class Security
{
    /** 
     * Get header Authorization
     * */
    public function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization']))
        {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION']))
        { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        }
        elseif (function_exists('apache_request_headers'))
        {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization']))
            {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    /**
     * get access token from header
     * */
    public function getBearerToken()
    {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers))
        {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches))
            {
                return $matches[1];
            }
        }
        return null;
    }

    public static function filter(array $post)
    {
        $filtered_post = [];
        foreach ($post as $key => $value)
        {
            $filtered_post[$key] = htmlspecialchars($value);
            $filtered_post[$key] = htmlentities($value);
            $filtered_post[$key] = filter_input(INPUT_POST, $value, FILTER_SANITIZE_SPECIAL_CHARS);
            $filtered_post[$key] = strip_tags($value);
        }

        return $filtered_post;
    }
}
