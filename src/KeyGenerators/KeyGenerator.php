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
}