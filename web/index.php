<?php

require '../vendor/autoload.php';

use \Symfony\Component\HttpFoundation\Request;

$app = new \Silex\Application;

$app['config'] = require __DIR__ . '/../config.php';
$app['debug']  = $app['config']['debug'];

/* 
 * SILEX PROVIDERS 
 */

$app->register(new \Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => $app['config']['templates_dir']
));


/*
 * CUSTOM PROVIDERS
 */
$app['dbReader'] = function() use ($app) {
	return new \RMSCatalog\DbReader($app);
};
$app['classificationReader'] = function() use ($app) {
	return new \RMSCatalog\ClassificationReader($app);
};


/*
 * CONTROLLERS
 */

$app->get('/', function(Request $req) use ($app)
{
	return $app['twig']->render('home.twig', [
		'classificationTree' => $app['classificationReader']->getHtmlTree($req->getBasePath())
	]);
});

$app->get('/search', function(Request $req) use ($app)
{
	$searchString = trim($req->get('searchBooks'));
	
	if (empty($searchString))
	{
		return $app->redirect($req->getBasePath() . '/');
	}

	$searchEngine = new \RMSCatalog\SearchEngine($app['dbReader']);
	$results 	  = $searchEngine->search($searchString);

	return $app['twig']->render('searchResults.twig', [
		'searchTerm' => $searchString,
		'results' 	 => $results,
	]);
});

$app->get('/record/{id}', function(Request $req, int $id) use ($app)
{
	$record = $app['dbReader']->cookRecord(
		$app['dbReader']->readDb()[$id]
	);

	$record['classTree'] = $app['classificationReader']->getParents($record['class']);

	return $app['twig']->render('record.twig', [
		'record' => $record,
		'whereToFindTemplate' => $app['config']['whereToFindTemplate']
	]);
});

$app->get('/db',  "RMSCatalog\\Controllers\\DbManagement::get");
$app->post('/db', "RMSCatalog\\Controllers\\DbManagement::post");

$app->get('/match', "RMSCatalog\\Controllers\\Match::get");


$app->run();