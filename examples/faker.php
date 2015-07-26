<?php

require __DIR__ . '/../vendor/autoload.php';

$faker = Faker\Factory::create();

$people = array();
$countries = array();

for ($i = 0; $i < 10000; $i++) {
    $country = $faker->country;

    $person = [
        'name'    => $faker->name,
        'age'     => $faker->numberBetween(18, 80),
        'country' => $country,
    ];

    $people[] = $person;
    if(!in_array($country, $countries)) {
        $countries[] = $country;
    }
}


$data = '<?php' . PHP_EOL;
$data .= '$data[\'people\'] = ' . var_export($people, true) . ';' . PHP_EOL;
$data .= '$data[\'countries\'] = ' . var_export($countries, true) . ';'.PHP_EOL;
$data .= 'return $data;';

file_put_contents('stubs.php', $data);