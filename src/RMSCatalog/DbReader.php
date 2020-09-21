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
	protected $flags;

	public function __construct(Application $app)
	{
		$this->app = $app;
		$this->flags = array_merge(...array_values($this->app['config']['flagsFromLanguage']));
	}

	public function readDb()
	{
		if (!empty($this->dbCatalog))
		{
			return $this->dbCatalog;
		}

		$this->dbCatalog = $this->readCacheFile(
			$this->app['config']['cacheCatalog'], 
			'Catalog'
		);

		return $this->dbCatalog;
	}

	public function readCacheFile($file, $cacheNameForErrorMessage)
	{
		if (file_exists($file))
		{
			if (!$result = unserialize(file_get_contents($file)))
			{
				throw new \Exception('The cache file does not contain valid serialized PHP');
			}

			return $result;
		}
		else
		{
			throw new \Exception("There is no cache file for $cacheNameForErrorMessage! Please upload the Catalog Excel file again");
		}
	}


	public function cookRecord($record)
	{
		if (strlen($record['subtitle']) < 3)
		{
			$record['subtitle'] = null;
		}

		if (!empty($record['language']))
		{
			$record['flag'] = [];

			foreach ($record['language'] as $lang)
			{
				$record['flag'][] = isset($this->flags[mb_strtoupper($lang)]) ? $this->flags[mb_strtoupper($lang)] : null;
			}
		}

		$record['classLabel'] = $this->app['classificationReader']->readClassification()[$record['class']];

		return $record;
	}
}