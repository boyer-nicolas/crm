<?php

namespace Niwee\Trident\Controller\Dashboard;

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
        $this->data = $data;
        $this->content = $content;
        $this->entry = $entry;
        $this->template = $template;

        $this->set_defaults();
        $this->load();
    }

    private function load()
    {
        $loader = new \Twig\Loader\FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . '/View/Dashboard/' . $this->viewfolder);

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

        $this->data = array_merge($this->env_map, $this->data);

        if ($this->content)
        {
            $this->template = $this->template . '.twig';
            $page = $this->twig->load($this->template);
            die($page->renderBlock('content', $this->data));
        }
        else if ($this->entry)
        {
            $this->template = 'components/entry-content.twig';
            die($this->twig->render($this->template, $this->data));
        }
        else
        {
            $this->template = 'layouts/dashboard.twig';
            die($this->twig->render($this->template, $this->data));
        }
    }

    /**
     * Paginate pages
     *
     * @return array $pagination
     */
    protected function paginate(int $items_count): array
    {
        if (!isset($_COOKIE['max_items_per_page']))
        {
            setcookie('max_items_per_page', 20, time() + (86400 * 30), "/");
            $_COOKIE['max_items_per_page'] = 20;
        }

        $this->max_items_per_page = $_COOKIE['max_items_per_page'];

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $pages_count = ceil($items_count / $_COOKIE['max_items_per_page']);

        if ($page == 1)
        {
            $min = 0;
        }
        else
        {
            $min = ($page - 1) * $_COOKIE['max_items_per_page'];
        }

        $max = $page * $_COOKIE['max_items_per_page'];
        $current_page = strtok($_SERVER['REQUEST_URI'], '?');

        if ($page > $pages_count)
        {
            Utils::redirect($current_page . '?page=' . $pages_count);
        }

        return $this->pagination = [
            'min' => $min,
            'max' => $max,
            'pages_count' => $pages_count,
            'range' => range(1, $pages_count),
            'active' => $page,
            'next_page' => $page + 1,
            'prev_page' => $page - 1
        ];
    }

    private function goodDayMessage()
    {
        date_default_timezone_set("Europe/Paris");
        $h = date('G');

        if ($h >= 5 && $h <= 11)
        {
            $message = "Bonjour";
        }
        else if ($h >= 12 && $h <= 15)
        {
            $message = "Bon après-midi";
        }
        else
        {
            $message = "Bonsoir";
        }

        return $message;
    }

    private function set_defaults()
    {
        $this->env_map = [
            'env'
        ];

        $this->setDefault('env', $this->env->getAll());
        $this->setDefault('config', $this->config);
        $this->setDefault('routes', $this->routes);
        if (!isset($_POST['uri']))
        {
            $this->setDefault('current_page', strtok($_SERVER['REQUEST_URI'], '?'));
        }
        else
        {
            $this->setDefault('current_page', $_POST['uri']);
        }
        $this->setDefault('session', $_SESSION);
        $this->setDefault('good_day_message', $this->goodDayMessage());

        if (Utils::getDevice() == 'mobile')
        {
            $this->setDefault('device', 'mobile');
            $this->viewfolder = "Mobile/";
        }
        else
        {
            $this->setDefault('device', 'desktop');
            $this->viewfolder = "Desktop/";
        }
    }

    private function setDefault(string $key, mixed $value)
    {
        if (!isset($this->env_map[$key]))
        {
            $this->env_map[$key] = $value;
        }
    }
}
