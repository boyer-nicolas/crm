<?php

namespace Niwee\Trident\Core;

use Niwee\Trident\Core\Config;

final class Routes
{
    public function __construct()
    {
        $this->routes = Config::get('routes');
    }

    public function getAll(bool $array = false)
    {
        return Config::get('routes', $array);
    }
}
