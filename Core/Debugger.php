<?php

namespace Niwee\Trident\Core;

use Whoops\Handler\Handler;
use Niwee\Trident\Core\Config;
use Niwee\Trident\Core\Logger;

/**
 * Debugger
 *
 * @uses Whoops
 * @uses Env
 * @uses Logger
 * @uses Config
 */
final class Debugger
{
    /**
     * @var \Niwee\Trident\Core\Logger
     */
    private $log;

    /**
     * Init the debug process depending on environment
     */
    public function __construct()
    {
        date_default_timezone_set('Europe/Paris');

        // Init the used classes
        $whoops = new \Whoops\Run();
        $this->log = new Logger();
        $config = Config::get('app');

        // Retrieve environment

        // Handle depending on environment
        if ($config->env === 'production' || $config->debug  === false)
        {
            /**
             * Just show ISE when on production and write to log
             */
            $whoops->pushHandler(
                function ($exception, $inspector, $run)
                {
                    new \Niwee\Trident\Controller\ISEController($exception->getMessage());
                    $this->log->error(date('d/m/Y, H:i:s'), $exception->getMessage(), $exception->getFile(), $exception->getLine());
                    return Handler::DONE;
                }
            );
        }
        else
        {
            /**
             * Show the full debug in development (and log), return json if is Ajax request
             */
            if (\Whoops\Util\Misc::isAjaxRequest())
            {
                $whoops->pushHandler(new \Whoops\Handler\JsonResponseHandler());
            }
            else
            {
                $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
            }

            $whoops->pushHandler(
                function ($exception, $inspector, $run)
                {
                    $this->log->error(date('d/m/Y \à H:i:s'), $exception->getMessage(), $exception->getFile(), $exception->getLine());
                }
            );
        }

        /**
         * Register the handler
         */
        $whoops->register();
    }
}
