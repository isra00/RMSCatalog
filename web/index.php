<?php

require '../vendor/autoload.php';

use \Symfony\Component\HttpFoundation\Request;
use \RMSCatalog\DbReader;

$app = new \Silex\Application;

$app['config'] = require __DIR__ . '/../config.php';
$app['debug']  = $app['config']['debug'];

$app->register(new \Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => $app['config']['templates_dir']
));


$app['dbReader'] = function() use ($app) {
	return new DbReader($app);
};

$app->get('/', function(Request $req) use ($app)
{
	return $app['twig']->render('home.twig');
});

$app->get('/search', function(Request $req) use ($app)
{
	$searchTerm = $req->get('searchBooks');
	$allRecords = $app['dbReader']->readDb();

	$found = [];

	foreach ($allRecords as $record)
	{
		if (stripos($record['title'], $searchTerm) || stripos($record['subtitle'], $searchTerm) || stripos($record['author'], $searchTerm))
		{
			$record['title'] 	= str_replace($searchTerm, "<em>$searchTerm</em>", $record['title']);
			$record['subtitle'] = str_replace($searchTerm, "<em>$searchTerm</em>", $record['subtitle']);
			$record['author'] 	= str_replace($searchTerm, "<em>$searchTerm</em>", $record['author']);

			$found[] = $app['dbReader']->cookRecord($record);
		}
	}

	return $app['twig']->render('searchResults.twig', [
		'searchTerm' => $searchTerm,
		'records' 	 => $found,
	]);
});

$app->get('/book/{id}', function(Request $req, int $id) use ($app)
{
	$record = $app['dbReader']->cookRecord(
		$app['dbReader']->readDb()[$id]
	);

	return $app['twig']->render('book.twig', ['record' => $record]);
});


$app->run();