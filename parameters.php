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
		'pt' => __DIR__ . '/translations/pt.php',
	],


	'flagsFromLanguage' => [

		'en' => [
			'ENGLISH' 		=> 'gb',
			'SPANISH' 		=> 'es',
			'ITALIAN' 		=> 'it',
			'GERMAN' 		=> 'de',
			'PORTUGUESE'	=> 'pt',
			'GREEK' 		=> 'gr',
			'LATIN' 		=> 'va',
			'SWAHILI' 		=> 'tz',
			'ARABIC' 		=> 'sa',
			'HEBREW' 		=> 'il',
			'FRENCH' 		=> 'fr',
		],

		'it' => [
			'INGLESE' 		=> 'gb',
			'SPAGNOLO' 		=> 'es',
			'ITALIANO' 		=> 'it',
			'TEDESCO' 		=> 'de',
			'PORTOGHESE'	=> 'pt',
			'GRECO' 		=> 'gr',
			'LATINO' 		=> 'va',
			'EBRAICO' 		=> 'il',
			'FRANCESE' 		=> 'fr',
			'CATALANO' 		=> 'catalonia',
			'SIRIACO' 		=> 'sy',
			'RUSSO' 		=> 'ru',
			'MACEDONE' 		=> 'mk',
			'ARABO' 		=> 'sa',
		],

		'es' => [
			'INGLÉS' 		=> 'gb',
			'ESPAÑOL' 		=> 'es',
			'ALEMÁN' 		=> 'de',
			'PORTUGUÉS'		=> 'pt',
			'GRIEGO' 		=> 'gr',
			'LATÍN' 		=> 'va',
			'ÁRABE' 		=> 'sa',
			'FRANCÉS' 		=> 'fr',
			'CATALÁN' 		=> 'catalonia',
			'SIRÍACO' 		=> 'sy',
			'RUSO' 			=> 'ru',
		],

		'pt' => [
			'INGLÊS'  		=> 'gb',
			'ESPANHOL'  	=> 'es',
			'ALEMÃO'  		=> 'de',
			'PORTUGUÊS' 	=> 'pt',
			'GREGO'  		=> 'gr',
			'HEBREO' 		=> 'il',
			'HEBRAICO'  	=> 'il',
			'FRANCÊS'  		=> 'fr',
		]
	]

];