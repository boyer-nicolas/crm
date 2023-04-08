<?php

namespace Niwee\Trident\Core;

use Niwee\Trident\Core\Config;
use Algolia\AlgoliaSearch\SearchClient;

final class Search
{
    /**
     * @var \Niwee\Trident\Core\Database
     */
    private $db;

    public function __construct()
    {
        $this->pages = Config::get('routes');
        $this->search_params = Config::get('search');
        $this->client = SearchClient::create($this->search_params->app_id, $this->search_params->token);
        $this->index = $this->client->initIndex('pages');
        $this->search_index = [];
    }

    /**
     * Clear the index objects
     */
    public function clearObjects()
    {
        $this->index->clearObjects();
    }

    /**
     * Index all pages
     */
    public function index()
    {
        foreach ($this->pages as $page)
        {
            if (isset($page->children))
            {
                $i = 0;
                foreach ($page->children as $child)
                {
                    $this->search_index[] = [
                        'objectID' => $i,
                        'title' => $child->title,
                        'url' => $child->uri,
                        'category' => 'Page',
                    ];

                    if (isset($child->sub_pages))
                    {
                        foreach ($child->sub_pages as $grandchild)
                        {
                            $this->search_index[] = [
                                'objectID' => $i + 1,
                                'title' => $child->title . " / " . $grandchild->title,
                                'url' => $child->uri . $grandchild->uri,
                                'category' => 'Page',
                            ];
                            $i++;
                        }
                    }
                    $i++;
                }
            }
        }

        $this->index->saveObjects($this->search_index);

        $this->index->setSettings([
            'searchableAttributes' => [
                'title'
            ]
        ]);
    }
}
