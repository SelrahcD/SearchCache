<?php namespace SelrahcD\SearchCache\KeyGenerators;

interface KeyGenerator
{
    /**
     * Generates a search key
     * @return string
     */
    public function generateKey();
}