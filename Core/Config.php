<?php

namespace Niwee\Trident\Core;

use Symfony\Component\Yaml\Yaml;

final class Config
{
    private $yaml;
    private $config;

    /**
     * Get a single parameter
     *
     * @param string $file  The file to get the parameters from in the config folder
     * @param bool   $array If true, the result will be an array, else it will be a json object
     */
    public static function get(string $file, bool $array = false)
    {
        $yaml = Yaml::parseFile($_SERVER['DOCUMENT_ROOT'] . '/config/' . $file . '.yml');

        return json_decode(json_encode($yaml), $array);
    }
}
