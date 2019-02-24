<?php

namespace RMSCatalog;

use \Silex\Application;

class ClassificationReader
{
	/**
	 * @var \Silex\Application
	 */
	protected $app;

	protected $classification;

	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	public function readClassification()
	{
		if (!empty($this->classification))
		{
			return $this->classification;
		}

		$lines = file($this->app['config']['classificationFile']);

		$classification = [];

		foreach ($lines as $line)
		{
			$line = str_getcsv($line);
			$classification[trim($line[0])] = trim($line[1]);
		}

		$this->classification = $classification;

		return $classification;
	}

	public function getParents($class)
	{
		if (false === array_search($class, array_keys($this->classification)))
		{
			throw new \Exception("The class `$class` does not exist");
		}

		$parents = [];
		while (strpos($class, '.'))
		{
			$class = substr($class, 0, strrpos($class, '.'));
			$parents[] = $class;
		}

		$parents = array_reverse($parents);

		$parentsWithLabels = [];

		foreach ($parents as $parent)
		{
			$parentsWithLabels[$parent] = $this->classification[$parent];
		}

		return $parentsWithLabels;
	}
}
			