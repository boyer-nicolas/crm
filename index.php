<?php

namespace Niwee\Trident;

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

setlocale(LC_ALL, 'fr_FR.UTF-8');
header("Access-Control-Allow-Origin: *");

use \Niwee\Trident\Core\Debugger;
use \Niwee\Trident\Core\Router;

new Debugger();
new Router();
