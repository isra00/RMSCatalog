<?php

namespace RMSCatalog\Controllers;

class Match
{
	public function get(\Silex\Application $app, \Symfony\Component\HttpFoundation\Request $req)
	{
		$searchString 			= trim($req->get('searchBooks'));
		$allRecords 			= $app['dbReader']->readDb();
		$requestedFields 		= $req->query->all();
		$validFields 			= array_keys(reset($allRecords));
		$validRequestedFields 	= array_intersect(array_keys($requestedFields), $validFields);

		if (empty($validRequestedFields))
		{
			throw new \Exception("Please specify matching parameters in the URL");
		}

		foreach ($requestedFields as $reqField)
		{
			if (is_array($reqField))
			{
				throw new \Exception("Array search values are not supported");
			}
		}

		//Match multiple fields with an AND (conjunction) combination
		$results = array_filter($allRecords, function($record) use ($requestedFields, $validRequestedFields) {

			$match = true;
			foreach ($validRequestedFields as $field)
			{
				if (!is_array($record[$field]))
				{
					$record[$field] = [$record[$field]];
				}

				if (FALSE === array_search($requestedFields[$field], $record[$field]))
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
		if (false !== array_search('class', array_keys($requestedFields)))
		{
			if (empty($app['classificationReader']->readClassification()[$requestedFields['class']]))
			{
				$app->abort(404, 'Requested classification ' . $requestedFields['class'] . ' does not exist');
			}

			$requestedFields['class'] = $requestedFields['class'] . ' ' . $app['classificationReader']->readClassification()[$requestedFields['class']];
		}

		$translatedRequestedFields = [];

		foreach ($requestedFields as $field=>$value)
		{
			$translatedRequestedFields[$app['translator']->trans($field)] = $value;
		}

		return $app['twig']->render('searchResults.twig', [
			'results' 		=> $results,
			'matchFields' 	=> $translatedRequestedFields
		]);
	}
}