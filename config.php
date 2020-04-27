<?php

return [

	'templates_dir'			=> __DIR__ . '/templates',

	'databaseFolder'		=> __DIR__ . '/database',
	'uploadedDb' 			=> __DIR__ . '/database/Catalog.xlsx',
	'cacheCatalog' 			=> __DIR__ . '/database/cache-catalog.serialize',
	'cacheClassification' 	=> __DIR__ . '/database/cache-classification.serialize',

	'translations'			=> [
		'es' => __DIR__ . '/trans/es.php',
	],

	'locale'				=> 'en',

	'debug' 				=> true,


	/**************************** INTERFACE CONFIG ****************************/

	//Leave blank to hide the "Where to Find" module
	'whereToFindTemplate'	=> 'saoPaulo.twig',

	'showLinkELibrary'		=> false,


	/************************* DATABASE FORMAT CONFIG *************************/

	//Whether the first row of the CLASSIFICATION sheet contains only headings
	'classificationFirstRowIsHeading' => true,

	/** 
	 * Mapping of Excel columns in CATALOG sheet to OPAC fields 
	 * Columns in CLASSIFICATION sheet cannot be customized as of now.
	 */

	'dbColumns'				=> [

		//Tanzania
		/*"id",
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
		"labelLine2",*/

		//Sao Paulo
		'id',
		'exemplar',
		'class',
		'location',
		'title',
		'subtitle',
		'author',
		'language',
		'year',
		'mainrecord',
		'volume',
		'series',
		'isbn',
		'labelLine1',
	]
];