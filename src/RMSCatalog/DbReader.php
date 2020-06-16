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

		/** @todo Refactor this */

		$flags = [
			'ENGLISH' 		=> 'gb',
			'INGLESE' 		=> 'gb',
			'SPANISH' 		=> 'es',
			'SPAGNOLO' 		=> 'es',
			'ITALIAN' 		=> 'it',
			'ITALIANO' 		=> 'it',
			'GERMAN' 		=> 'de',
			'TEDESCO' 		=> 'de',
			'PORTUGUESE'	=> 'pt',
			'PORTOGHESE'	=> 'pt',
			'GREEK' 		=> 'gr',
			'GRECO' 		=> 'gr',
			'LATIN' 		=> 'va',
			'LATINO' 		=> 'va',
			'SWAHILI' 		=> 'tz',
			'ARABIC' 		=> 'sa',
			'ARABO' 		=> 'sa',
			'HEBREW' 		=> 'il',
			'EBRAICO' 		=> 'il',
			'FRANCESE' 		=> 'fr',
			'CATALANO' 		=> 'catalonia',
			'SIRIACO' 		=> 'sy',
			'RUSSO' 		=> 'ru',
		];

		if (!empty($record['language']))
		{
			$record['flag'] = [];

			foreach ($record['language'] as $lang)
			{
				$record['flag'][] = isset($flags[strtoupper($lang)]) ? $flags[strtoupper($lang)] : null;
			}
		}

		/** Library-specific */
		/** @deprecated */
		$record['section'] = ($record['class'][0] == 'T') ? 'Theology' : 'Philosophy';

		$record['classLabel'] = $this->app['classificationReader']->readClassification()[$record['class']];

		return $record;
	}
}