<?php

namespace Niwee\Trident\Controller\Dashboard\Settings;

use Niwee\Trident\Core\Env;
use Niwee\Trident\Core\Logger;

final class LogsController extends \Niwee\Trident\Controller\Dashboard\ViewController
{

    public function __construct(string $title, string $template, bool $content = false, bool $entry = false)
    {
        $this->logger = new Logger();

        $this->access_lines = $this->logger->countAccessLines();
        $this->error_lines = $this->logger->countErrorLines();

        $max_lines = max($this->access_lines, $this->error_lines);

        $this->paginate($max_lines);
        $this->access_log = $this->logger->getAccessLog($this->pagination['min'], $this->pagination['max']);
        $this->error_log = $this->logger->getErrorLog($this->pagination['min'], $this->pagination['max']);

        parent::__construct(
            $template,
            [
                'title' => $title,
                'access_log' => $this->access_log,
                'error_log' => $this->error_log,
                'access_log_lines' => $this->access_lines,
                'error_log_lines' => $this->error_lines,
                'pagination' => $this->pagination
            ],
            $content,
            $entry
        );
    }
}
