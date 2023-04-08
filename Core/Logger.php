<?php

namespace Niwee\Trident\Core;

use Niwee\Trident\Core\Config;
use Niwee\Trident\Core\Database;

/**
 *
 */
final class Logger
{
    /**
     * @var mixed
     */
    private $error_log;
    /**
     * @var mixed
     */
    private $access_log;
    /**
     * @var mixed
     */
    private $app_title;

    private $log;

    /**
     *
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->config = Config::get('app');
        $this->error_log = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->config->error_log;
        $this->access_log = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->config->access_log;
        $this->app_title = $this->config->name;
        date_default_timezone_set('Europe/Paris');
        $this->max_lines = 150;
    }

    public function write(string $log_file, string $message, string $file, string $line)
    {
        $log = "The " . date("d m Y") . " at " . date("H:i:s") . " - " . $message . " in " . $file . " on line " . $line . "\n";

        $map = array_filter(array_map("trim", file($log_file)));

        // Make Sure you always have maximum number of lines
        $map = array_slice($map, 0, $this->max_lines);

        // Remove any extra line
        count($map) >= $this->max_lines and array_shift($map);

        // Add new Line
        array_push($map, $log);

        // Save Result
        file_put_contents($log_file, implode(PHP_EOL, array_filter($map)));
    }


    /**
     * @param  string $message
     * @return void
     */
    public function info(string $message)
    {
        $this->log->info($message);
    }

    /**
     * @param  string $message
     * @return void
     */
    public function error(string $date, string $message, string $file, string $line)
    {
        $this->write($this->error_log, $message, $file, $line);
        $this->insert($date, 'ERROR', $message, $file, $line);
    }

    /**
     * Insert log to database
     *
     * @param string $date
     * @param string $level
     * @param string $message
     * @param string $file
     * @param string $line
     */
    public function insert(string $date, string $level, string $message, string $file, string $line)
    {
        $this->db->insert(
            'logs',
            [
                'date' => $date,
                'level' => $level,
                'message' => $message,
                'file' => $file,
                'line' => $line
            ]
        );
    }

    public function getErrorLog(int $rangeStart, int $rangeEnd)
    {
        return $this->db->select(
            'logs',
            [
                'id',
                'date',
                'level',
                'message',
                'file',
                'line'
            ],
            [
                'level' => 'ERROR',
                "LIMIT" => [$rangeStart, $rangeEnd],
                "ORDER" => ["id" => "DESC"]
            ]
        );
    }

    public function getAccessLog(int $rangeStart, int $rangeEnd)
    {
        return $this->db->select(
            'logs',
            [
                'id',
                'date',
                'level',
                'message',
                'file',
                'line'
            ],
            [
                'level' => 'ACCESS',
                "LIMIT" => [$rangeStart, $rangeEnd],
                "ORDER" => ["id" => "DESC"]
            ]
        );
    }

    public function countErrorLines()
    {
        return $this->db->count(
            'logs',
            [
                'level' => 'ERROR'
            ]
        );
    }

    public function countAccessLines()
    {
        return $this->db->count(
            'logs',
            [
                'level' => 'ACCESS'
            ]
        );
    }

    public function lastError()
    {
        $lastLine = $this->db->select(
            'logs',
            [
                'id',
                'date',
                'level',
                'message',
                'file',
                'line'
            ],
            [
                'level' => 'ERROR',
                "ORDER" => ["id" => "DESC"],
                'LIMIT' => 1
            ]
        );

        return Utils::flatten($lastLine);
    }
}
