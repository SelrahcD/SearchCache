<?php

namespace SelrahcD\SearchCache\Tests\KeyGenerators;


use SelrahcD\SearchCache\KeyGenerators\HashKeyGenerator;

class HashKeyGeneratorTest extends \PHPUnit_Framework_TestCase
{
    private $keyGenerator;

    protected function setUp()
    {
        $this->keyGenerator = new HashKeyGenerator();
    }

    public function testGeneratePrivateKeyReturnsSameKeyIfSearchParametersAndResultsAreTheSame()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $results = [1, 'AA', 3, "HUG76767"];

        $key1 = $this->keyGenerator->generatePrivateKey($params, $results);
        $key2 = $this->keyGenerator->generatePrivateKey($params, $results);

        $this->assertEquals($key1, $key2);
    }

    public function testGeneratePrivateKeyReturnsDifferentKeyIfSearchParametersArentTheSame()
    {
        $params1 = [
            'name' => 'test',
            'age'  => 12,
        ];

        $params2 = [
            'name' => 'test2',
            'age'  => 12,
        ];

        $results = [1, 'AA', 3, "HUG76767"];

        $key1 = $this->keyGenerator->generatePrivateKey($params1, $results);
        $key2 = $this->keyGenerator->generatePrivateKey($params2, $results);

        $this->assertNotEquals($key1, $key2);
    }

    public function testGeneratePrivateKeyReturnsDifferentKeyIfSearchResultArentTheSame()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $results1 = [1, 'AA', 3, "HUG76767"];
        $results2 = [1, 'AA'];

        $key1 = $this->keyGenerator->generatePrivateKey($params, $results1);
        $key2 = $this->keyGenerator->generatePrivateKey($params, $results2);

        $this->assertNotEquals($key1, $key2);
    }

    public function testGeneratePrivateKeyReturnsDifferentKeyIfSearchResultsArentInTheSameOrder()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $results1 = [1, 'AA', 3, "HUG76767"];
        $results2 = [1, 'AA', "HUG76767", 3];

        $key1 = $this->keyGenerator->generatePrivateKey($params, $results1);
        $key2 = $this->keyGenerator->generatePrivateKey($params, $results2);


        $this->assertNotEquals($key1, $key2);
    }

    public function testGenerateSharedKeyReturnsSameKeyIfParamsAreEquals()
    {
        $params = [
            'name' => 'test',
            'age'  => 12,
        ];

        $key1 = $this->keyGenerator->generateSharedKey($params);
        $key2 = $this->keyGenerator->generateSharedKey($params);

        $this->assertEquals($key1, $key2);
    }

    public function testGenerateSharedKeyReturnsDifferentKeysIfParamsArentEquals()
    {
        $params1 = [
            'name' => 'test',
            'age'  => 12,
        ];

        $params2 = [
            'name' => 'test',
            'age'  => 20,
        ];

        $key1 = $this->keyGenerator->generateSharedKey($params1);
        $key2 = $this->keyGenerator->generateSharedKey($params2);

        $this->assertNotEquals($key1, $key2);
    }
}
