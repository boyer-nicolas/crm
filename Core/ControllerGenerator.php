<?php

namespace Niwee\Trident\Core;

final class ControllerGenerator
{
    /**
     * Generate a controller from template
     *
     * @param string $controller The name of the controller to generate
     */
    public function __construct(string $controller)
    {
        ob_start();

        $template = $_SERVER['DOCUMENT_ROOT'] . '/Controller/TemplateController.php';
        $newControllerPath = $_SERVER['DOCUMENT_ROOT'] . '/Controller/' . $controller . '.php';

        copy($template, $newControllerPath);

        $controllerPathContents = file_get_contents($newControllerPath);
        file_put_contents($newControllerPath, str_replace('TemplateController', $controller, $controllerPathContents));

        ob_end_clean();
        header("Refresh:1");
    }
}
