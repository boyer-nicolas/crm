<?php

namespace Niwee\Trident\API;

use Niwee\Trident\Core\Ui;

final class UiController extends ApiController
{
    public function __construct()
    {
        $this->ui = new Ui();
    }

    public function darkmode()
    {
        $this->ui->setDarkMode();
    }
}
