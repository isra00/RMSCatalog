<?php

return [

	/*
	 * CONFIGURING THE WEB INTERFACE
	 */

	//Website language
	'locale'				=> 'it',

	//Website title
	'siteTitle'				=> 'Our Library',

	//Leave blank or null to hide the "Where to Find" module
	'whereToFindTemplate'	=> 'mamre.twig',

	//Leave blank or null not to show the "Go to E-Library" link on the header
	'showLinkELibrary'			=> '//our-e-library.com'

	//Fetch additional book metadata from Google Books
	'fetchDataFromInternet'	=> true,

	//Only for developers
	'debug' 				=> false,


	/*
	 * CONFIGURING THE EXCEL CATALOG
	 */

	//Whether the first row of the CLASSIFICATION sheet contains only headings
	'classificationFirstRowIsHeading'	=> true,

	'multipleValueFields'				=> ['author', 'language'],

	//Mapping of Excel columns in CATALOG sheet to OPAC fields 
	//Columns in CLASSIFICATION sheet cannot be customized.
	'dbColumns' => [

		'id',
		'exemplar',
		'class',
		'title',
		'subtitle',
		'author',
		'language',
		'year',
		'volume',
		'seriesIndex',
		'isbn',
		'order',
		'location',
		'label'
	],

];