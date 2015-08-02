<?php

require __DIR__ . '/../vendor/autoload.php';

use SelrahcD\SearchCache\KeyGenerators\UniqidKeyGenerator;
use SelrahcD\SearchCache\SearchCache;
use SelrahcD\SearchCache\SearchResultStores\PredisSearchResultStore;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

$redisConf = include __DIR__ . '/redisconf.php';

$client = new Predis\Client($redisConf);

$predisSearchStore = new PredisSearchResultStore($client);
$keyGenerator = new UniqidKeyGenerator();
$searchCache = new SearchCache($predisSearchStore, $keyGenerator);
$peopleStore = new PeopleStore();

if (!empty($_GET['search'])) {
    $search = $_GET['search'];
    $peopleIds = $searchCache->getResult($_GET['search']);
} elseif (!empty($_POST['submit']) && !empty($_POST['age'])) {

    $searchParams = [
        'age' => $_POST['age']
    ];

    if($search = $searchCache->getCopyOfSharedResult($searchParams)) {
        $peopleIds = $searchCache->getResult($search);
    }
    else {
        $peopleIds = $peopleStore->search($searchParams);
        $searchCache->storeSharedResult($searchParams, $peopleIds);
        $search = $searchCache->getCopyOfSharedResult($searchParams);
    }

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
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
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
    echo '<a href="' . $_SERVER['PHP_SELF'] . $params . '">Previous Page</a><br />';
}

if ($pagerfanta->hasNextPage()) {
    $params = '?page=' . $pagerfanta->getNextPage();
    if(isset($search)) {
        $params .= '&search=' . $search;
    }
    echo '<a href="' . $_SERVER['PHP_SELF'] . $params . '">Next Page</a>';
}