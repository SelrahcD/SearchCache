<?php

namespace SelrahcD\SearchCache\SearchResultStores;

interface SearchResultsStore
{
    public function store($key, array $results);

    public function getResult($key);
}