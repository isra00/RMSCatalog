<?php

namespace RMSCatalog;

use \Silex\Application;

class SearchEngine
{
	/**
	 * @var \Silex\Application
	 */
	protected $app;

	/**
	 * Matches in different fields are multiplied by this coefficient.
	 */
	const SEARCH_FIELDS = [
		'title'		=> 1, 
		'subtitle'	=> 0.8, 
		'author'	=> 0.9,
		'subject'	=> 0.8
	];

	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	public function possibleSearchFields()
	{
		return array_intersect(
			$this->app['config']['dbColumns'], 
			array_keys(self::SEARCH_FIELDS)
		);
	}

	/**
	 * Word by word search. Full sentences are scored the most, then full words. 
	 * Accented characters are matched by "flattening" (á => a) both the 
	 * searched words and the fields contents, and therefore the search results 
	 * are shown with the flattened characters. It's not a perfect solution, but
	 * it's been incredibly fast to code it and it works ;-)
	 */
	public function search($searchString, $searchOnFields=null)
	{
		//Some fields may not exist in the Excel file
		$possibleSearchFields = $this->possibleSearchFields();

		$searchFields = empty($searchOnFields)
			? $possibleSearchFields
			: array_intersect($possibleSearchFields, $searchOnFields);

		$allRecords = $this->app['dbReader']->readDb();

		$results = [];

		$searchWords = preg_split(
			'/(\s|,|\.|\:|\!|\?|\(|\)|\[|\])/', 
			$this->flattenLetters(strtolower($searchString))
		);

		foreach ($allRecords as &$record)
		{
			foreach ($this->app['config']['multipleValueFields'] as $multipleField) {
				if (!empty($record[$multipleField]))
				{
					$record[$multipleField] = implode(' & ', $record[$multipleField]);
				}
			}

			foreach ($searchFields as $field)
			{
				//Necessary because a record may be passed through twice or more
				if (is_array($record[$field]))
				{
					$record[$field] = implode(' & ', $record[$field]);
				}

				//Each searched word is matched against each word in the fields.
				$record["$field-words"] = explode(' ', $record[$field]);

				$score = 0;

				$wordsMatched = [];
				foreach ($record["$field-words"] as $word)
				{
					$word = $this->flattenLetters($word);

					foreach ($searchWords as $searchWord)
					{
						if (preg_match("/(.*)$searchWord(.*)/i", $word, $match))
						{
							//1 point if word matched (not exact full word)
							$score += 1 * self::SEARCH_FIELDS[$field];

							if (empty($match[1][0]) && empty($match[2][0]))
							{
								//+2 points for full word
								$score += 2 * self::SEARCH_FIELDS[$field];
							}

							$wordsMatched[] = $searchWord;
						}
					}

					if ($score)
					{
						// If did not match ALL the words, discard the result.
						if (array_diff($searchWords, $wordsMatched))
						{
							$score = 0;
						}
						else
						{
							if (count($searchWords) > 1 && preg_match("/(.*)$searchString(.*)/i", $record[$field], $match))
							{
								// +2 points for exact phrase
								$score += 2 * self::SEARCH_FIELDS[$field];

								if (empty($match[1][0]) && empty($match[2][0]))
								{
									//+2 points if exact phrase is exact field
									$score += 2 * self::SEARCH_FIELDS[$field];
								}
							}

							$this->highlight($record[$field], $searchWords);
						}
					}
				}

				if ($score)
				{
					foreach ($this->app['config']['multipleValueFields'] as $field) {
						if (!is_array($record[$field]))
						{
							$record[$field] = explode('&', $record[$field]);
							
							foreach ($record[$field] as &$scalarValue)
							{
								$scalarValue = trim($scalarValue);
							}
						}
					}

					$record = $this->app['dbReader']->cookRecord($record);

					$results[] = [
						'score'  => $score,
						'record' => $record
					];
				}
			}
		}

		$this->removeDuplicates($results);
		$this->sortResults($results);
		
		return $results;
	}

	protected function highlight(&$originalText, $highlighted)
	{
		$originalText = preg_replace("/(" . implode('|', $highlighted) . ")/i", "<em>$1</em>", $originalText);
	}

	/**
	 * Removes the duplicate books (exemplars of same call number) from search results
	 * considering duplicates the books with the same Class, call n. and volume.
	 *
	 * @param array $results Results array to be processed (by reference)
	 */
	protected function removeDuplicates(array &$results)
	{
		$filtered = [];
		$callNumbersAlreadyPresent = [];

		foreach ($results as $result)
		{
			$callNumber = implode('-', [$result['record']['class'], $result['record']['call'], $result['record']['volume']]);

			if (array_search($callNumber, $callNumbersAlreadyPresent) === false)
			{
				$callNumbersAlreadyPresent[] = $callNumber;
				$filtered[] = $result;
			}
		};
		
		$results = $filtered;
	}


	/**
	 * Sort by score descending and if same score, by title ascending.
	 * 
	 * @param array $results Results array to be sorted (by reference)
	 */
	protected function sortResults(array &$results)
	{
		usort($results, function($a, $b) {

			if ($a['score'] == $b['score'])
			{
				//If same score, sort alphabetically by title (?)
				$emTags = ['<em>', '</em>'];
				$titles = [
					str_replace($emTags, '', $a['record']['title']), 
					str_replace($emTags, '', $b['record']['title'])
				];
				
				$titlesSorted = $titles;
				natcasesort($titlesSorted);

				return ($titles == $titlesSorted) ? 1 : -1;
			}

			return ($a['score'] > $b['score']) ? -1 : 1;
		});
	}

	protected function flattenLetters($string)
	{
		//La ñ la conservamos
		$flattenLetters = array(
			'Á' => 'A',
			'À' => 'A',
			'Â' => 'A',
			'Ã' => 'A',
			'Ä' => 'A',
			'á' => 'a',
			'à' => 'a',
			'â' => 'a',
			'ã' => 'a',
			'ä' => 'a',
			'É' => 'E',
			'È' => 'E',
			'Ê' => 'E',
			'Ë' => 'E',
			'é' => 'e',
			'è' => 'e',
			'ê' => 'e',
			'ë' => 'e',
			'Í' => 'I',
			'Ì' => 'I',
			'Î' => 'I',
			'Ï' => 'I',
			'í' => 'i',
			'ì' => 'i',
			'î' => 'i',
			'ï' => 'i',
			'Ó' => 'O',
			'Ò' => 'O',
			'Ò' => 'O',
			'Õ' => 'O',
			'Ö' => 'O',
			'Ô' => 'O',
			'ò' => 'o',
			'ó' => 'o',
			'ô' => 'o',
			'ö' => 'o',
			'õ' => 'o',
			'Ú' => 'U',
			'Ù' => 'U',
			'Û' => 'U',
			'Ü' => 'U',
			'ú' => 'u',
			'ù' => 'u',
			'û' => 'u',
			'ü' => 'u',
			'ª' => 'a',
			'º' => 'o',
		);

		return str_replace(
			array_keys($flattenLetters), 
			array_values($flattenLetters), 
			$string
		);
	}
}