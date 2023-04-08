<?php

namespace Niwee\Trident\API;

use Niwee\Trident\Core\Search;
use Niwee\Trident\Core\Utils;

final class SearchController extends ApiController
{
    public function __construct()
    {
        $this->search = new Search();
    }

    public function reIndex()
    {
        try
        {
            $this->search->clearObjects();
            $this->search->index();
            Utils::ajax_message('L\'index a été réinitialisé', "success");
        }
        catch (\Exception $e)
        {
            return Utils::ajax_message($e->getMessage(), 'error');
        }
    }

    public function clearIndex()
    {
        try
        {
            $this->search->clearObjects();
            Utils::ajax_message('L\'index a été vidé', "success");
        }
        catch (\Exception $e)
        {
            return Utils::ajax_message($e->getMessage(), 'error');
        }
    }

    public function fillIndex()
    {
        try
        {
            $this->search->index();
            Utils::ajax_message('L\'index a été rempli', "success");
        }
        catch (\Exception $e)
        {
            return Utils::ajax_message($e->getMessage(), 'error');
        }
    }
}
