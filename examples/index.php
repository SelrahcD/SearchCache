<?php

require __DIR__ . '/../vendor/autoload.php';

use SelrahcD\SearchCache\SearchCache;
use SelrahcD\SearchCache\SearchResultStores\PredisSearchResultStore;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

$redisConf = include __DIR__ . '/redisconf.php';

$client = new Predis\Client($redisConf);

$predisSearchStore = new PredisSearchResultStore($client);
$searchCache = new SearchCache($predisSearchStore);
$peopleStore = new PeopleStore();

if (!empty($_GET['search'])) {
    $search = $_GET['search'];
    $peopleIds = $searchCache->getResult($_GET['search']);
} elseif (!empty($_POST['submit']) && !empty($_POST['age'])) {

    $searchParams = [
      'age' => $_POST['age']
    ];

    $peopleIds = $peopleStore->search($searchParams);

    $search = $searchCache->store($searchParams, $peopleIds);
} else {
    $peopleIds = $peopleStore->getAllIds();
}


$adapter = new ArrayAdapter($peopleIds);
$pagerfanta = new Pagerfanta($adapter);

if (isset($_GET['page'])) {
    $pagerfanta->setCurrentPage($_GET['page']);
}

$peopleIds = $pagerfanta->getCurrentPageResults();

$people = $peopleStore->getByIds($peopleIds);


# DISPLAY
echo '<h1>People (' . $pagerfanta->getNbResults() . ')</h1>';
?>
    <form action="index.php" method="POST">
        <label>Age: <input type="number" name="age"></label>
        <input type="submit" value="Search" name="submit">
    </form>
<?php
echo '<table>';
foreach ($people as $person) {
    echo '<tr><td>' . $person['name'] . '</td><td>' . $person['age'] . '</td><td>' . $person['country'] . '</td></tr>';
}
echo '</table>';

if ($pagerfanta->hasPreviousPage()) {
    $params = '?page=' . $pagerfanta->getPreviousPage();
    if(isset($search)) {
        $params .= '&search=' . $search;
    }
    echo '<a href="index.php' .$params. '">Previous Page</a><br />';
}

if ($pagerfanta->hasNextPage()) {
    $params = '?page=' . $pagerfanta->getNextPage();
    if(isset($search)) {
        $params .= '&search=' . $search;
    }
    echo '<a href="index.php' .$params. '">Next Page</a>';
}