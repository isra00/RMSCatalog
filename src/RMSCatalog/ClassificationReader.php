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

		$this->classification = $this->app['dbReader']->readCacheFile(
			$this->app['config']['cacheClassification'],
			'Classification'
		);

		return $this->classification;
	}


	/** 
	 * Classification is expected in the format LN[.N][.N][...] where L is one 
	 * or more letters (main section) and N numbers, e.g. E4, TS4.1.4
	 */
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

			/** Library-specific Tanzania */
			//There is one unlucky exception of a class without parent -_-
			//(What is library-specific is only the "if", the rest is necessary)
			if (isset($this->readClassification()[$class]))
			{
				$parents[] = $class;
			}
		}

		//Before the first dot: get the section
		if (preg_match('/([a-z]+)/i', $class, $match))
		{
			$parents[] = $match[1];
		}

		$parents = array_reverse($parents);

		$parentsWithLabels = [];

		foreach ($parents as $parent)
		{
			$parentsWithLabels[$parent] = $this->readClassification()[$parent];
		}

		return $parentsWithLabels;
	}

	function getHtmlTree($baseUrl)
	{
		$classes = $this->readClassification();
		$codes = array_keys($classes);

		$output = "<ul class='b-class-tree'>";
		foreach ($codes as $code)
		{
			/** Library-specific */
			/*if (preg_match('/^[a-z]+$/i', $code))
			{
				if ('T' == $code)
				{
					/** @todo To be translated */
					/*$output .= "<h3>Theology</h3>\n";
				}
				else
				{
					$output .= "<h3>$code " . $classes[$code] . "</h3>\n";*/
			/*		continue;
				}
			}*/

			$level = count(explode('.', $code));

			if (preg_match('/^[a-z]+$/i', $code))
			{
				$level = 0;
			}

			$output .= "<li class='level-$level'>"
						. "<a title='Find all books in this class' href='$baseUrl/match?class=$code'>"
						. "<span class='b-class'>$code</span> "
						. "<span class='b-class-label'>" . $classes[$code] . "</span>"
						. "</a></li>\n";
		}

		return $output . "</ul>";
	}
}
			