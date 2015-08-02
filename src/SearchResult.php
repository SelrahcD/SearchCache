<?php

namespace SelrahcD\SearchCache;

final class SearchResult
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var array
     */
    private $result;

    /**
     * SearchResult constructor.
     * @param string $key
     * @param array $result
     */
    public function __construct($key, array $result)
    {
        $this->key = $key;
        $this->result = $result;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}