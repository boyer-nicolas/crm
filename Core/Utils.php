<?php

namespace Niwee\Trident\Core;

use Niwee\Trident\Core\Database;
use Niwee\Trident\Core\Env;
use Niwee\Trident\Core\Device;

/**
 * Utils
 */
final class Utils
{
    /**
     * Flatten a multidimensional array
     *
     * @param  array $array
     * @return array
     */
    public static function flatten(array $array): array
    {
        /**
         * Flatten the array
         */
        $flattened = [];
        foreach ($array as $key => $value)
        {
            if (is_array($value))
            {
                $flattened = array_merge($flattened, $value);
            }
        }

        return $flattened;
    }

    /**
     * Redirect to a given url
     */
    public static function redirect(string $url)
    {
        $config = Config::get('app');
        $app_url = $config->url;
        $https = $config->https;

        if ($url[0] !== '/')
        {
            $url = '/' . $url;
        }

        if ($https === "true")
        {
            $url = "https://" . $app_url . $url;
        }
        else
        {
            $url = "http://" . $app_url . $url;
        }

        header("Location: $url", true);
        exit();
    }

    /**
     * Return an ajax message
     *
     * @param string $message
     * @param string $type    Can be 'success', 'error', 'info' or 'warning'
     */
    public static function ajax_message(mixed $message, string $type = "success", array $data = null, bool $reload = false, string $btnMessage = "", string $redirectTo = null)
    {
        switch ($type)
        {
            case 'success':
                $btnMessage = "Super !";
                $modalTitle = "Succès !";
                break;

            default:
                $btnMessage = "Okay.";
                $modalTitle = "Erreur.";
                break;
        }

        if ($redirectTo != null && $reload === false)
        {
            die(json_encode(
                [
                    'message' => $message,
                    'type' => $type,
                    'btnMessage' => $btnMessage,
                    'modalTitle' => $modalTitle,
                    'data' => $data,
                    'redirectTo' => $redirectTo
                ]
            ));
        }
        else
        {
            die(json_encode(
                [
                    'message' => $message,
                    'type' => $type,
                    'reload' => $reload,
                    'btnMessage' => $btnMessage,
                    'modalTitle' => $modalTitle,
                    'data' => $data
                ]
            ));
        }
    }

    public static function getDevice()
    {
        $device = new Device();
        if ($device->isMobile() || $device->isTablet())
        {
            return 'mobile';
        }
        else
        {
            return 'desktop';
        }
    }
}
