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

		/** @todo Refactor this */

		$flags = [
			'ENGLISH' 		=> 'gb',
			'INGLESE' 		=> 'gb',
			'INGLÉS' 		=> 'gb',
			'SPANISH' 		=> 'es',
			'ESPAÑOL' 		=> 'es',
			'SPAGNOLO' 		=> 'es',
			'ITALIAN' 		=> 'it',
			'ITALIANO' 		=> 'it',
			'GERMAN' 		=> 'de',
			'TEDESCO' 		=> 'de',
			'ALEMÁN' 		=> 'de',
			'PORTUGUESE'	=> 'pt',
			'PORTOGHESE'	=> 'pt',
			'PORTUGUÉS'		=> 'pt',
			'GREEK' 		=> 'gr',
			'GRECO' 		=> 'gr',
			'GRIEGO' 		=> 'gr',
			'LATÍN' 		=> 'va',
			'LATIN' 		=> 'va',
			'LATINO' 		=> 'va',
			'SWAHILI' 		=> 'tz',
			'ARABIC' 		=> 'sa',
			'ÁRABE' 		=> 'sa',
			'ARABO' 		=> 'sa',
			'HEBREW' 		=> 'il',
			'HEBREO' 		=> 'il',
			'EBRAICO' 		=> 'il',
			'FRANCESE' 		=> 'fr',
			'FRANCÉS' 		=> 'fr',
			'CATALANO' 		=> 'catalonia',
			'CATALÁN' 		=> 'catalonia',
			'SIRIACO' 		=> 'sy',
			'SIRÍACO' 		=> 'sy',
			'RUSSO' 		=> 'ru',
			'RUSO' 			=> 'ru',
			'MACEDONE' 		=> 'mk',
		];

		if (!empty($record['language']))
		{
			$record['flag'] = [];

			foreach ($record['language'] as $lang)
			{
				$record['flag'][] = isset($flags[mb_strtoupper($lang)]) ? $flags[mb_strtoupper($lang)] : null;
			}
		}

		/** Library-specific */
		/** @deprecated */
		$record['section'] = ($record['class'][0] == 'T') ? 'Theology' : 'Philosophy';

		$record['classLabel'] = $this->app['classificationReader']->readClassification()[$record['class']];

		return $record;
	}
}