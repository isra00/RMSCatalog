<?php

return [

	/**************************** INTERFACE CONFIG ****************************/

	'locale'				=> 'it',

	'siteTitle'				=> 'Domus Mamre',

	//Leave blank or null to hide the "Where to Find" module
	'whereToFindTemplate'	=> 'mamre.twig',

	'showLinkELibrary'		=> false,

	'fetchDataFromInternet'	=> true,


	/************************* DATABASE FORMAT CONFIG *************************/

	//Whether the first row of the CLASSIFICATION sheet contains only headings
	'classificationFirstRowIsHeading' 	=> true,

	'multipleValueFields'				=> ['author', 'language'],

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
		/*'id',
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
		'labelLine1',*/

		//Mamre
		'id',
		'exemplar',
		'class',
		'title',
		'subtitle',
		'author',
		'language',
		'year',
		'volume',
		'serie',
		'isbn',
		'call',
		'location',
		'label'
	],

	/************************** SYSTEM (DON'T TOUCH) **************************/

	'templates_dir'			=> __DIR__ . '/templates',
	'databaseFolder'		=> __DIR__ . '/database',
	'uploadedDb' 			=> __DIR__ . '/database/Catalog.xlsx',
	'cacheCatalog' 			=> __DIR__ . '/database/cache-catalog.serialize',
	'cacheClassification' 	=> __DIR__ . '/database/cache-classification.serialize',
	'cacheGBooks'		 	=> __DIR__ . '/database/cache-gbooks.serialize',

	'translations'			=> [
		'es' => __DIR__ . '/trans/es.php',
		'it' => __DIR__ . '/trans/it.php',
	],

	'debug' 				=> true,

];