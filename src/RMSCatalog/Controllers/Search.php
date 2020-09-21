<?php

namespace RMSCatalog\Controllers;

use \RMSCatalog\SearchEngine;

class Search
{
	public function get(\Silex\Application $app, \Symfony\Component\HttpFoundation\Request $req)
	{
		$searchString = trim($req->get('searchBooks'));

		if (empty($searchString))
		{
			return $app->redirect($req->getBasePath() . '/');
		}

		$searchEngine = new SearchEngine($app);

		$urlSearchOnFields = !empty($req->get('searchOnFields'))
			? array_keys($req->get('searchOnFields'))
			: null;

		$searchOnFields = array_intersect(
			$urlSearchOnFields ?? $searchEngine->possibleSearchFields(),
			$searchEngine->possibleSearchFields()
		);

		$results = $searchEngine->search($searchString, $searchOnFields);

		return $app['twig']->render('searchResults.twig', [
			'allFields'			=> $searchEngine->possibleSearchFields(),
			'searchOnFields'	=> $searchOnFields,
			'searchTerm'		=> $searchString,
			'results'			=> $results,
			'urlWithoutParams'	=> $app['url_generator']->generate($req->get('_route')),
			'showAdvancedSearch'=> true
		]);
	}
}