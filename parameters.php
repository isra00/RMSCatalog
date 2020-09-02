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

];