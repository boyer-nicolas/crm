<?php

namespace Niwee\Trident\API;
# Api Endpoint: /api/get-page-content

use Niwee\Trident\Controller\DashboardController;
use Niwee\Trident\Core\Config;
use Niwee\Trident\Core\Security;
use Niwee\Trident\Core\Router;

final class PageController extends ApiController
{
    public function __construct()
    {
        $this->routes = Config::get('routes');
    }

    public function get_contents()
    {
        $post = Security::filter($_POST);
        extract($post);

        if (!isset($uri))
        {
            parent::__construct('Missing URI', 'error');
        }

        // Loop over each route.
        foreach ($this->routes as $route)
        {
            // If the route has a base, it's probably a dashboard route.
            if (isset($route->base))
            {
                // Loop over each route child.
                foreach ($route->children as $child)
                {
                    if ($uri === $route->base . $child->uri)
                    {
                        return $child;
                    }
                    // If the child has sub pages, loop over them.
                    if (isset($child->sub_pages))
                    {
                        // Loop over the sub pages

                        foreach ($child->sub_pages as $sub_page)
                        {
                            if ($uri === $route->base . $child->uri . $sub_page->uri)
                            {
                                return $sub_page;
                            }
                        }
                    }
                }
            }
            else
            {
                // This is a simple route, just get it.
                if ($uri === $route->uri)
                {
                    return $route;
                }
            }
        }
    }

    public function getPageContent()
    {
        $page = $this->get_contents();
        $this->get($page, true, false);
    }

    public function get($route, $content, $entry)
    {
        $namespace = "\Niwee\Trident\Controller\\";
        $controller = $route->controller;
        $controller = $namespace . $controller;
        die(new $controller($route->title, $route->template, $content, $entry));
    }

    public function getEntryContent()
    {
        $page = $this->get_contents();
        $this->get($page, false, true);
    }

    public function getRoutes()
    {
        die(json_encode([
            'routes' => $this->routes->getAll()
        ]));
    }
}
