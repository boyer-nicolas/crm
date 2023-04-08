<?php

namespace Niwee\Trident\Core;

use Bramus\Router\Router as Bramus;
use Exception;
use Niwee\Trident\Core\Config;
use Niwee\Trident\Core\Auth;

final class Router
{
    private $router;
    /**
     * @var mixed
     */
    private $routes;
    /**
     * @var mixed
     */
    private $api_routes;

    public $content;

    /**
     * Router constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->auth = new Auth();
        $this->config = Config::get('app');
        Session::start();
        $this->router = new Bramus();
        $this->setNotFound();
        $this->get_routes();
        $this->router->setNamespace('\Niwee\Trident\Controller');
        $this->route_pages();
        $this->route_api();
        $this->router->run();
    }

    private function setNotFound()
    {
        $this->router->set404(
            function ()
            {
                new \Niwee\Trident\Controller\NotFoundController();
            }
        );
    }

    public function get_routes()
    {
        $this->routes = Config::get('routes');
    }

    public function route_pages()
    {
        // Loop over each route.
        foreach ($this->routes as $route)
        {
            // If the route has a base, it's probably a dashboard route.
            if (isset($route->base))
            {
                $this->router->mount($route->base, function () use ($route)
                {
                    // Loop over each route child.
                    foreach ($route->children as $child)
                    {
                        // Tell the router to get the route URI.
                        $this->set_page($child);

                        // If the child has sub pages, loop over them.
                        if (isset($child->sub_pages))
                        {
                            // Loop over the sub pages

                            $this->router->mount($child->uri, function () use ($child)
                            {
                                foreach ($child->sub_pages as $sub_page)
                                {
                                    $this->set_page($sub_page);
                                }
                            });
                        }
                    }
                });
            }
            else
            {
                // This is a simple route, just get it.
                $this->set_page($route);
            }
        }
    }

    /**
     * Get a page and send to init_controller
     */
    public function set_page(object $page)
    {
        $this->router->get($page->uri, function () use ($page)
        {
            $this->init_controller($page);
        });
    }

    /**
     * Check if user has access
     */
    private function check_access(object $route)
    {
        if (isset($route->public) && $route->public == true)
        {
            return true;
        }
        else if (isset($_SESSION['role']) && in_array($_SESSION['role'], $route->has_access))
        {
            return true;
        }
        else
        {
            if (!$this->is($this->routes->login->uri))
            {
                Utils::redirect($this->routes->login->uri);
                exit;
            }
        }
    }

    /**
     * Check if user has access, then launch __construct on controller
     */
    private function init_controller(object $route)
    {
        $this->check_access($route);
        $namespace = $this->router->getNameSpace();
        $controller = $namespace . '\\' . $route->controller;
        if (!new $controller($route->title, $route->template))
        {
            $this->router->trigger404();
        }
    }

    /**
     * Check if current uri is the requested term
     */
    private function is($uri)
    {
        if ($this->router->getCurrentUri() == $uri)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Route API requests
     */
    private function route_api()
    {
        // Get all API routes
        $this->api_routes = Config::get('api');
        $base = $this->api_routes->base;

        $this->router->set404('/api(/.*)?', function ()
        {
            header('HTTP/1.1 404 Not Found');
            header('Content-Type: application/json');

            $jsonResponse = array();
            $jsonResponse['status'] = "404";
            $jsonResponse['message'] = "This endpoint does not exist.";

            echo json_encode($jsonResponse);
        });

        $this->router->mount($base, function ()
        {
            $this->router->get('/', function ()
            {
                header('HTTP/1.1 200 OK');
                header('Content-Type: application/json');

                $jsonResponse = array();
                $jsonResponse['status'] = "200";
                $jsonResponse['message'] = "API is running.";

                echo json_encode($jsonResponse);
            });

            // Loop over API routes
            foreach ($this->api_routes->endpoints as $api_route)
            {
                $method = strtolower($api_route->method);
                $uri = $api_route->uri;
                $controller = $api_route->controller;
                $action = $api_route->action;

                $this->router->$method($uri, function () use ($controller, $action)
                {
                    $controllerpath = '\Niwee\Trident\API\\' . $controller;
                    $controller = new $controllerpath();
                    $controller->$action();
                });
            }
        });
    }
}
