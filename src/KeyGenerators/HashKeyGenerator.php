<?php

namespace SelrahcD\SearchCache\KeyGenerators;

final class HashKeyGenerator implements KeyGenerator
{
    /**
     * Generates the key based on search params and results
     * @param array $params
     * @param array $results
     * @return string
     */
    public function generatePrivateKey($params, $results)
    {
        return md5(serialize($params) . serialize($results));
    }
}