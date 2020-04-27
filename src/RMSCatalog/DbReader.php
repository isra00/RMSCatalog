<?php

namespace RMSCatalog;

use \Silex\Application;

class DbReader
{
	/**
	 * @var \Silex\Application
	 */
	protected $app;

	protected $dbCatalog;

	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	public function readDb()
	{
		if (!empty($this->dbCatalog))
		{
			return $this->dbCatalog;
		}

		if (file_exists($this->app['config']['cacheCatalog']))
		{
			if (!$dbCatalog = unserialize(file_get_contents($this->app['config']['cacheCatalog'])))
			{
				throw new \Exception('The cache file does not contain valid serialized PHP');
			}
		}
		else
		{
			throw new \Exception("There is not cache file for Classification! Please upload the Catalog Excel file again");
		}

		return $this->dbCatalog = $dbCatalog;
	}


	public function cookRecord($record)
	{
		foreach ($record as &$column)
		{
			if (!is_array($column))
			{
				$column = trim($column);
			}
		}

		if (strlen($record['subtitle']) < 3)
		{
			$record['subtitle'] = null;
		}

		$flags = [
			'ENGLISH' 		=> 'gb',
			'SPANISH' 		=> 'es',
			'ITALIAN' 		=> 'it',
			'ITALIANO' 		=> 'it',
			'GERMAN' 		=> 'de',
			'PORTUGUESE'	=> 'pt',
			'GREEK' 		=> 'gr',
			'LATIN' 		=> 'va',
			'SWAHILI' 		=> 'tz',
			'ARABIC' 		=> 'sa',
			'HEBREW' 		=> 'il',
		];

		if (!empty($record['language']))
		{
			$record['flag'] = isset($flags[$record['language']]) ? $flags[$record['language']] : null;
		}

		/** Library-specific */
		$record['section'] = ($record['class'][0] == 'T') ? 'Theology' : 'Philosophy';

		$record['classLabel'] = $this->app['classificationReader']->readClassification()[$record['class']];

		return $record;
	}
}