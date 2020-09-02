<?php

/**
 * App internal parameters. Don't change unless you are sure what you are doing.
 */

return [

	'templates_dir'			=> __DIR__ . '/templates',
	'databaseFolder'		=> __DIR__ . '/database',
	'uploadedDb' 			=> __DIR__ . '/database/Catalog.xlsx',
	'cacheCatalog' 			=> __DIR__ . '/database/cache-catalog.serialize',
	'cacheClassification' 	=> __DIR__ . '/database/cache-classification.serialize',
	'cacheGBooks'		 	=> __DIR__ . '/database/cache-gbooks.serialize',

	'translations'			=> [
		'es' => __DIR__ . '/translations/es.php',
		'it' => __DIR__ . '/translations/it.php',
	],


	'flagsFromLanguage' => [

		'ENGLISH' 		=> 'gb',
		'INGLESE' 		=> 'gb',
		'INGLÉS' 		=> 'gb',
		'SPANISH' 		=> 'es',
		'ESPAÑOL' 		=> 'es',
		'SPAGNOLO' 		=> 'es',
		'ITALIAN' 		=> 'it',
		'ITALIANO' 		=> 'it',
		'GERMAN' 		=> 'de',
		'TEDESCO' 		=> 'de',
		'ALEMÁN' 		=> 'de',
		'PORTUGUESE'	=> 'pt',
		'PORTOGHESE'	=> 'pt',
		'PORTUGUÉS'		=> 'pt',
		'GREEK' 		=> 'gr',
		'GRECO' 		=> 'gr',
		'GRIEGO' 		=> 'gr',
		'LATÍN' 		=> 'va',
		'LATIN' 		=> 'va',
		'LATINO' 		=> 'va',
		'SWAHILI' 		=> 'tz',
		'ARABIC' 		=> 'sa',
		'ÁRABE' 		=> 'sa',
		'ARABO' 		=> 'sa',
		'HEBREW' 		=> 'il',
		'HEBREO' 		=> 'il',
		'EBRAICO' 		=> 'il',
		'FRANCESE' 		=> 'fr',
		'FRANCÉS' 		=> 'fr',
		'CATALANO' 		=> 'catalonia',
		'CATALÁN' 		=> 'catalonia',
		'SIRIACO' 		=> 'sy',
		'SIRÍACO' 		=> 'sy',
		'RUSSO' 		=> 'ru',
		'RUSO' 			=> 'ru',
		'MACEDONE' 		=> 'mk',
	]

];