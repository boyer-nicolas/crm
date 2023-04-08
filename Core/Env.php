<?php

namespace Niwee\Trident\Core;

use Symfony\Component\Dotenv\Dotenv;
use Niwee\Trident\Core\Config;

final class Env
{
    /**
     * @var
     */
    private $dotenv;

    /**
     *
     */
    public function __construct()
    {
        $dotenv = new Dotenv();
        $this->config = Config::get('app');

        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/.env'))
        {
            $dotenv->load($_SERVER['DOCUMENT_ROOT'] . '/.env');
        }

        if ($this->config->env == 'development')
        {
            $dotenv->load($_SERVER['DOCUMENT_ROOT'] . '/.db.local.env');
        }
        else
        {
            $dotenv->load($_SERVER['DOCUMENT_ROOT'] . '/.db.env');
        }
    }

    /**
     * @param  string $param
     * @return mixed
     */
    public static function get(string $param)
    {
        new self();
        return $_ENV["$param"];
    }

    /**
     * @return array
     */
    public static function getAll(): array
    {
        new self();
        return $_ENV;
    }

    /**
     * @param  string $param
     * @param  string $value
     * @return void
     */
    public static function set(string $param, string $value)
    {
        new self();
        $_ENV[$param] = $value;
    }

    /**
     * @param  string $param
     * @return mixed
     */
    public static function db(string $param)
    {
        $self = new self();
        $dotenv = new Dotenv();
        $environment = $self->config->env;

        if ($environment == 'development')
        {
            $dotenv->load($_SERVER['DOCUMENT_ROOT'] . '/.db.local.env');
        }
        else
        {
            $dotenv->load($_SERVER['DOCUMENT_ROOT'] . '/.db.env');
        }

        return $_ENV[$param];
    }
}
