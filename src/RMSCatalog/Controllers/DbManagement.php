<?php

namespace RMSCatalog\Controllers;

use \Silex\Application;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DbManagement
{
	/**
	 * @type  \Silex\Application
	 */
	protected $app;

	protected $requiredSheets = ['CATALOG', 'CLASSIFICATION'];

	/**
	 * A method
	 * 
	 * @param  \Silex\Application	$app		The Silex app
	 * @param  array				$twigBinds	Additional variables to bind in the Twig view
	 * @return string				The rendered view
	 */
	public function get(Application $app, $twigBinds=[])
	{
		$this->app = $app;

		$gBooksDataReader = new \RMSCatalog\GBooksDataReader($app);

		$this->checkFiles($app, $twigBinds);

		//If Excel file does not exist but caches do, notify and do nothing else
		if (empty($twigBinds['uploadedDb']['size']) && $twigBinds['cacheCatalog']['size'] && $twigBinds['cacheClassification']['size'])
		{
			$notification = 'The Excel database file has not been found, but the website can still work properly.';
		}

		//If Excel file exists but any of the caches do not, re-generate it
		if ($twigBinds['uploadedDb']['size'])
		{
			if (empty($twigBinds['cacheCatalog']['size']) || empty($twigBinds['cacheClassification']['size']))
			{
				$this->generateCacheFiles();
				return $app->redirect($_SERVER['REQUEST_URI'] . '?regeneratedCaches=1');
			}
		}

		if ($twigBinds['cacheCatalog']['size'])
		{
			$twigBinds['totalRecords'] = count($app['dbReader']->readDb());
			$twigBinds['gBooksStats'] = $gBooksDataReader->getStats();
		}

		if (!empty($_GET['regeneratedCaches']))
		{
			$twigBinds['success'] = $app['translator']->trans('The cache files have been automatically generated from Excel database because they were not found.');
		}

		return $app['twig']->render('dbManagement.twig', array_merge($twigBinds, [
			'topNotification'	=> $topNotification,
			'notification'		=> $notification,
			'loadJSFramework' 	=> true
		]));
	}

	protected function checkFiles(Application $app, &$twigBinds)
	{
		$files = [
			'uploadedDb',
			'cacheCatalog',
			'cacheClassification',
		];

		foreach ($files as $file)
		{
			if (file_exists($app['config'][$file]))
			{
				$twigBinds[$file] = [
					'name'  => basename($app['config'][$file]),
					'mTime' => filemtime($app['config'][$file]),
					'size'  => filesize($app['config'][$file]),
				];
			}
			else
			{
				$twigBinds[$file] = [
					'name'  => basename($app['config'][$file]),
					'mTime' => null,
					'size'  => null
				];
			}
		}
	}

	public function post(Application $app)
	{
		$this->app = $app;

		if (empty($_FILES['upload']['tmp_name']))
		{
			$error = 'Please select a file before clicking upload';
		}

		if (move_uploaded_file($_FILES['upload']['tmp_name'], $app['config']['uploadedDb'])) 
		{
			$finfo = new \finfo;

			if ('Microsoft Excel 2007+' != $finfo->file($app['config']['uploadedDb']))
			{
				unlink($app['config']['uploadedDb']);
				$error = 'The Library catalog must be an Excel 2007+ file (must be generated with Microsoft Excel software)';
			}
		}

		if (empty($error))
		{
			$this->generateCacheFiles();
			$success = 'Database updated succesfully!';
		}

		return $this->get($app, [
			'error'   => $error ?? null,
			'success' => $success ?? null,
		]);
	}

	protected function generateCacheFiles()
	{
		$reader 		= \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
		$spreadsheet 	= $reader->load($this->app['config']['uploadedDb']);
		$xlsSheets 		= $spreadsheet->getSheetNames();
		$writer 		= new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);

		//Step 1: convert each required sheet into CSV for easier reading
		foreach ($this->requiredSheets as $sheet)
		{
			if (false === ($sheetIndex = array_search($sheet, $xlsSheets)))
			{
				throw new \Exception("The sheet $sheet was not found in the Excel file!");
			}

			$writer->setSheetIndex($sheetIndex);
			$writer->save(
				$this->app['config']['databaseFolder'] . '/' . $sheet . '.csv'
			);
		}

		//Step 2: read the CSV and cached the PHP arrays in files
		$this->writeCache($this->app['config']['cacheCatalog'], $this->readCsvCatalog());
		$this->writeCache($this->app['config']['cacheClassification'], $this->readCsvClassification());

		//Step 3: delete the CSV files. The original XLSX is kept
		foreach ($this->requiredSheets as $sheet)
		{
			unlink($this->app['config']['databaseFolder'] . '/' . $sheet . '.csv');
		}
	}

	/**
	 * Columns are positional, according to the positions defined in the 
	 * config file.
	 */
	protected function readCsvCatalog()
	{
		$catalog = [];

		$lines = [];
		if (($handler = fopen($this->app['config']['databaseFolder'] . '/CATALOG.csv', "r")) !== FALSE) {
			while (($row = fgetcsv($handler)) !== FALSE) {
				$lines[] = $row;
			}
			fclose($handler);
		}

		unset($lines[0]); //Remove first row (headers)

		foreach ($lines as $line)
		{
			if (count($line) >= count($this->app['config']['dbColumns']))
			{
				$line = array_combine(
					$this->app['config']['dbColumns'],
					array_slice($line, 0, count($this->app['config']['dbColumns']))
				);
			}

			foreach ($this->app['config']['multipleValueFields'] as $field) {
				
				$line[$field] = explode('&', $line[$field]);
				
				foreach ($line[$field] as &$value)
				{
					$value = trim($value);
				}
			}

			$catalog[$line['id']] = $line;
		}


		return $this->reduceExemplars($catalog);
	}

	protected function reduceExemplars(array $allExemplars)
	{
		$records = [];
		foreach ($allExemplars as $exemplar)
		{

			$bid = implode('-', [$exemplar['title'], $exemplar['author'], $exemplar['year'], $exemplar['volume']]);

			if (isset($records[$bid]))
			{
				$records[$bid]['exemplars']++;
			}
			else
			{
				$records[$bid] = $exemplar;
				$records[$bid]['exemplars'] = 1;
			}
		}

		//Re-assign array keys as per book ID. Yes, this is somehow inconsistent.
		$recordsById = [];
		foreach ($records as $record)
		{
			$recordsById[$record['id']] = $record;
		}

		return $recordsById;
	}

	protected function readCsvClassification()
	{
		$lines = file($this->app['config']['databaseFolder'] . '/CLASSIFICATION.csv');

		$classification = [];

		if ($this->app['config']['classificationFirstRowIsHeading'])
		{
			array_shift($lines);
		}

		foreach ($lines as $line)
		{
			$line = str_getcsv($line);
			$classification[trim($line[0])] = trim($line[1]);
		}

		return $classification;
	}

	protected function writeCache($file, $data)
	{
		file_put_contents($file, serialize($data));
	}

}
