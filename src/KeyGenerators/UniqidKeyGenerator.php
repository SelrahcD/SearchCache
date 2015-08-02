<?php

namespace SelrahcD\SearchCache\KeyGenerators;

final class UniqidKeyGenerator implements KeyGenerator
{
    /**
     * Generates a search key
     * @return string
     */
    public function generateKey()
    {
        return uniqid();
    }
}