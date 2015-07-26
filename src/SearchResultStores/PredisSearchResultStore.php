<?php

namespace SelrahcD\SearchCache\SearchResultStores;

use Predis\Client;
use SelrahcD\SearchCache\SearchResultStores;

final class PredisSearchResultStore implements SearchResultsStore
{
    /**
     * @var Client
     */
    private $client;

    /**
     * PredisSearchResultStore constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function store($key, array $results)
    {
        $this->client->sadd($key, $results);
    }


    public function getResult($key)
    {
        return $this->client->smembers($key);
    }
}