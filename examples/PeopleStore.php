<?php

final class PeopleStore
{
    private $people;

    public function __construct()
    {
        $data = require __DIR__ . '/stubs.php';
        $this->people = $data['people'];
    }

    public function getAllIds()
    {
        return array_keys($this->people);
    }

    public function getByIds(array $ids)
    {
        return array_intersect_key($this->people, array_flip($ids));
    }

    public function search(array $search = array()) {
        sleep(1);
        return array_keys(array_filter($this->people, function($person) use($search) {
            if(isset($search['age'])) {
                return $person['age'] == $search['age'];
            }
        }));
    }
}