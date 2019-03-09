<?php

namespace RMSCatalog\Controllers;

class Match
{
	public function get(\Silex\Application $app, \Symfony\Component\HttpFoundation\Request $req)
	{
		$searchString 			= trim($req->get('searchBooks'));
		$allRecords 			= $app['dbReader']->readDb();
		$requestedFields 		= $req->query->all();
		$validFields 			= array_keys($allRecords[1]);
		$validRequestedFields 	= array_intersect(array_keys($requestedFields), $validFields);

		if (empty($validRequestedFields))
		{
			throw new \Exception("Please specify matching parameters in the URL");
		}

		//Match multiple fields with AND combination
		$results = array_filter($allRecords, function($record) use ($requestedFields, $validRequestedFields) {

			$match = true;
			foreach ($validRequestedFields as $field)
			{
				if ($record[$field] != $requestedFields[$field])
				{
					$match = false;
				}
			}

			return $match;
		});

		//Prepare results
		foreach ($results as &$result)
		{
			$result = ['record' => $app['dbReader']->cookRecord($result)];
		}

		//Prepare results page title
		/** @todo Validate requested classes. If it does not exist, throw error */
		if (false !== array_search('class', array_keys($requestedFields)))
		{
			$requestedFields['class'] = $requestedFields['class'] . ' ' . $app['classificationReader']->readClassification()[$requestedFields['class']];
		}

		return $app['twig']->render('searchResults.twig', [
			'results' 		=> $results,
			'matchFields' 	=> $requestedFields
		]);
	}
}