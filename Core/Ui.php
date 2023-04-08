<?php

namespace Niwee\Trident\Core;

use \Niwee\Trident\Core\Utils;
use \Niwee\Trident\Core\Database;

final class Ui
{
    public static function setDarkMode()
    {
        $theme = $_POST['theme'];
        $db = new Database();

        if (!isset($_SESSION['theme']))
        {
            $_SESSION['theme'] = 'light';
        }

        if ($theme)
        {
            try
            {
                $_SESSION['theme'] = $_POST['theme'];
                $db->update(
                    'users',
                    [
                        'ui_theme' => $_SESSION['theme']
                    ],
                    [
                        'id' => $_SESSION['id']
                    ]
                );
                Utils::ajax_message('Ui theme set to ' . $_SESSION['theme']);
            }
            catch (\Exception $e)
            {
                Utils::ajax_message('Cannot set theme to ' . $_SESSION['theme'] . ': ' . $e->getMessage());
                throw new \Exception($e->getMessage());
            }
        }
    }
}
