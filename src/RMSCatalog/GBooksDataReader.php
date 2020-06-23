<?php

namespace RMSCatalog;

use \Silex\Application;

class GBooksDataReader
{
	/**
	 * @var \Silex\Application
	 */
	protected $app;

	/**
	 * @var array
	 */
	protected $gBooksCache = [];

	public function __construct(Application $app)
	{
		$this->app = $app;

		if (file_exists($this->app['config']['cacheGBooks']))
		{
			$this->gBooksCache = unserialize(file_get_contents($this->app['config']['cacheGBooks']));

			if (FALSE === $this->gBooksCache)
			{
				throw new \Exception('The cache file does not contain valid serialized PHP');
			}
		}
		else
		{
			file_put_contents($this->app['config']['cacheGBooks'], serialize([]));
		}
	}

	public function getStats()
	{
		$foundGBooksData = 0;

		foreach ($this->gBooksCache as $cacheEntry)
		{
			if (null !== $cacheEntry)
			{
				$foundGBooksData++;
			}
		}

		return [
			'total' 		=> count($this->app['dbReader']->readDb()),
			'withGBooks'	=> count($this->gBooksCache),
			'wighGBooksData'=> $foundGBooksData
		];
	}

	protected function queryGBooks($query)
	{
		$url = 'https://www.googleapis.com/books/v1/volumes?q=' . urlencode($query);
		$bookData = json_decode(file_get_contents($url), true);
		if ($bookData['kind'] != 'books#volumes')
		{
			throw new \Exception("Google Books API returned a resource that is not a book");
		}

		if (empty($bookData['items']))
		{
			throw new \Exception("Google Books API returned no items");
		}

		$bookData = json_decode(file_get_contents($bookData['items'][0]['selfLink']), true);

		return $bookData;
	}

	public function getBookData($record)
	{
		$query = empty($record['isbn'])
			? 'intitle:' . $record['title'] . ' inauthor:' . $record['author'][0]
			: $record['isbn'];
		
		if (!$gbooksResult = $this->gBooksCache[$query])
		{
			try 
			{
				$gbooksResult = $this->queryGBooks($query);
			} catch (\Exception $e)
			{
				$gbooksResult = false;
			}

			$this->gBooksCache[$query] = $gbooksResult;
			file_put_contents($this->app['config']['cacheGBooks'], serialize($this->gBooksCache));
		}

		//var_dump($gbooksResult);
		return $gbooksResult;
	}
}