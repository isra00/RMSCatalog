<?php

namespace RMSCatalog;

use \Silex\Application;

class DbReader
{
	/**
	 * @var \Silex\Application
	 */
	protected $app;

	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	public function readDb()
	{
		$columns = [
			"id",
			"call",
			"class",
			"title",
			"subtitle",
			"author",
			"year",
			"location",
			"volume",
			"series",
			"language",
			"exemplar",
			"borrower",
			"date",
			"checkedSeries",
			"isbn",
			"mainrecord",
			"labelLine1",
			"labelLine2",
		];

		$db = [];

		$lines = file($this->app['config']['dbFile']);

		foreach ($lines as $line)
		{
			$line = array_combine($columns, str_getcsv($line));
			$db[$line['id']] = $line;
		}

		return $db;
	}

	public function cookRecord($record)
	{
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
			'ARABIC' 		=> 'ar',
		];

		if (!empty($record['language']))
		{
			$record['flag'] = $flags[$record['language']] ?? $flags[$record['language']];
		}

		$record['section'] = ($record['class'][0] == 'T') ? 'Theology' : 'Philosophy';

		return $record;
	}
}