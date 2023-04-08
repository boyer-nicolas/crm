<?php

namespace Niwee\Trident\API;

use Niwee\Trident\Core\Logger;

final class ErrorController extends ApiController
{
    public function __construct()
    {
        $this->logger = new Logger();
    }

    public function lastError()
    {
        die(json_encode($this->logger->lastError()));
    }
}
