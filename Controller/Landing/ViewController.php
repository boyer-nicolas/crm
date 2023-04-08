<?php

namespace Niwee\Trident\Controller\Landing;

use Niwee\Trident\Core\Database;
use Niwee\Trident\Core\Env;
use Niwee\Trident\Core\Config;
use Niwee\Trident\Core\Auth;
use Niwee\Trident\Core\Utils;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use \Exception;

/**
 * Abstract final class ViewController
 */
abstract class ViewController
{
    private $twig;
    /**
     * @var mixed
     */
    private $routes;
    /**
     * @var Env
     */
    private $env;

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function __construct(string $template, array $data, bool $content = false, bool $entry = false)
    {
        $this->env = new Env();
        $this->auth = new Auth();
        $this->routes = Config::get('routes', true);
        $this->config = Config::get('app');

        $this->defaults();
        $this->setDefault('config', $this->config);
        $this->setDefault('routes', $this->routes);
        $this->setDefault('session', $_SESSION);


        $loader = new \Twig\Loader\FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . '/View/Landing/');
        if ($this->config->env === 'production' || $this->config->debug === false)
        {
            $this->twig = new \Twig\Environment($loader);
        }
        else
        {
            $this->twig = new \Twig\Environment(
                $loader,
                [
                    'debug' => true,
                ]
            );
            $this->twig->addExtension(new \Twig\Extension\DebugExtension());
        }

        $this->data = $this->setData($data);

        die($this->twig->render($template . '.twig', $this->data));
    }

    private function defaults()
    {
        $this->env_map = [
            'env'
        ];
    }

    private function setDefault(string $key, mixed $value)
    {
        if (!isset($this->env_map[$key]))
        {
            $this->env_map[$key] = $value;
        }
    }

    private function setData(array $data)
    {
        return array_merge($this->env_map, $data);
    }
}
