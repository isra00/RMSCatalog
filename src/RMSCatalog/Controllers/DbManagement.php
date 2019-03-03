<?php

namespace RMSCatalog\Controllers;

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
	public function get(\Silex\Application $app, $twigBinds=[])
	{
		return $app['twig']->render('dbManagement.twig', array_merge($twigBinds, [
			'uploadedDb'  => [
				'name'  => basename($app['config']['uploadedDb']),
				'mTime' => filemtime($app['config']['uploadedDb']),
				'size'  => filesize($app['config']['uploadedDb']),
			],
			'cacheCatalog' => [
				'name'  => basename($app['config']['cacheCatalog']),
				'mTime' => filemtime($app['config']['cacheCatalog']),
				'size'  => filesize($app['config']['cacheCatalog']),
			],
			'cacheClassification' => [
				'name'  => basename($app['config']['cacheClassification']),
				'mTime' => filemtime($app['config']['cacheClassification']),
				'size'  => filesize($app['config']['cacheClassification']),
			],
		]));
	}

	public function post(\Silex\Application $app)
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

	protected function readCsvCatalog()
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

		$catalog = [];

		$lines = file($this->app['config']['databaseFolder'] . '/CATALOG.csv');
		unset($lines[0]); //Remove first row (headers)

		foreach ($lines as $line)
		{
			$line = array_combine($columns, str_getcsv($line));
			
			//Pre-catalogued books (=only title/author) are discarded
			if (!empty($line['class']))
			{
				$catalog[$line['id']] = $line;
			}
		}

		return $catalog;	
	}

	protected function readCsvClassification()
	{
		$lines = file($this->app['config']['databaseFolder'] . '/CLASSIFICATION.csv');

		$classification = [];

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
