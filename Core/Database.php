<?php

namespace Niwee\Trident\Core;

use Medoo\Medoo;
use Niwee\Trident\Core\Env;
use Niwee\Trident\Core\Utils;
use Niwee\Trident\Core\Config;
use Exception;

/**
 * Class Database
 */
final class Database extends Medoo
{

    /**
     * @var Medoo
     */
    private $db;
    /**
     * @var mixed
     */
    private $db_host;
    /**
     * @var mixed
     */
    private $db_name;
    /**
     * @var mixed
     */
    private $db_user;
    /**
     * @var mixed
     */
    private $db_pass;

    /**
     *
     */
    public function __construct()
    {
        $this->db_host = Env::db('MYSQL_HOST');
        $this->db_name = Env::db('MYSQL_DATABASE');
        $this->db_user = Env::db('MYSQL_USER');
        $this->db_pass = Env::db('MYSQL_PASSWORD');
        $this->config = Config::get('app');

        try
        {
            $this->db = parent::__construct(
                [
                    'type' => 'mariadb',
                    'host' => $this->db_host,
                    'database' => $this->db_name,
                    'username' => $this->db_user,
                    'password' => $this->db_pass,
                    'port' => 3306,
                ]
            );
        }
        catch (Exception $e)
        {
            if (Utils::getDevice() == 'mobile')
            {
                $this->viewfolder = "Mobile/";
            }
            else
            {
                $this->viewfolder = "Desktop/";
            }

            $loader = new \Twig\Loader\FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . '/View/' . $this->viewfolder);
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

            die($this->twig->render('Pages/ISE.twig', [
                'title' => 'Erreur 500',
                'config' => $this->config,
                'message' => 'Erreur de connexion à la base de données.',
            ]));
        }
    }
}
