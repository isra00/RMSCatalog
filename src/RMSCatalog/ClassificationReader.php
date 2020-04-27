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

	/** @todo Reducir código duplicado de DbReader::readDb */
	public function readClassification()
	{
		if (!empty($this->classification))
		{
			return $this->classification;
		}

		if (file_exists($this->app['config']['cacheClassification']))
		{
			if (!$classification = unserialize(file_get_contents($this->app['config']['cacheClassification'])))
			{
				throw new \Exception('The classification cache file does not contain valid serialized PHP');
			}
		}
		else
		{
			throw new \Exception("There is not cache file for Classification! Please upload the Catalog Excel file again");
		}

		return $this->classification = $classification;
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

			//There is one unlucky exception of a class without parent -_-
			if (isset($this->readClassification()[$class]))
			{
				$parents[] = $class;
			}
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
			if (1 == strlen($code))
			{
				if ('T' == $code)
				{
					/** @todo To be translated */
					$output .= "<h3>Theology</h3>\n";
				}
				else
				{
					$output .= "<h3>$code " . $classes[$code] . "</h3>\n";
					continue;
				}
			}

			$output .= "<li class='level-" . (count(explode('.', $code)) - 1) . "'>"
						. "<a title='Find all books in this class' href='$baseUrl/match?class=$code'>"
						. "<span class='b-class'>$code</span> "
						. "<span class='b-class-label'>" . $classes[$code] . "</span>"
						. "</a></li>\n";
		}

		return $output . "</ul>";
	}
}
			