<?php

namespace Niwee\Trident\API;


final class TranslationController extends ApiController
{
    public function __construct()
    {
    }

    public function getTranslations()
    {
        if (!isset($_POST['lang']))
        {
            $lang = $_POST['lang'];
        }
    }
}
