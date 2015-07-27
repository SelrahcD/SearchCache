<?php namespace SelrahcD\SearchCache\KeyGenerators;

interface KeyGenerator
{
    /**
     * Generates the key based on search params and results
     * @param array $params
     * @param array $results
     * @return string
     */
    public function generatePrivateKey($params, $results);

    /**
     * Generate the key based on search params
     * @param $params
     * @return string
     */
    public function generateSharedKey($params);

    /**
     * Creates a copy of key
     * @param $key
     * @return mixed
     */
    public function createCopyOfKey($key);
}