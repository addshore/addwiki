<?php

declare( strict_types = 1 );

use Addwiki\Wikibase\Query\WikibaseQueryService;
use GuzzleHttp\Client;

require_once __DIR__ . '/vendor/autoload.php';

/*
Run one tiny SPARQL query against Wikidata Query Service.

Default endpoint:
- https://query.wikidata.org/bigdata/namespace/wdq/sparql

Usage:
php wikidata-query-one.php
*/

$endpoint = $argv[1] ?? 'https://query.wikidata.org/bigdata/namespace/wdq/sparql';
$query = 'SELECT ?item WHERE { VALUES ?item { wd:Q2 } } LIMIT 1';

try {
	$service = new WikibaseQueryService(
		new Client( [
			'headers' => [
				'User-Agent' => 'addwiki-examples/1.0 (wikidata-query-one)',
			],
		] ),
		$endpoint
	);

	$result = $service->query( $query );
	$ids = $service->getConceptSuffixesFromQueryResult( $result, 'item' );

	if ( $ids === [] ) {
		fwrite( STDERR, "NOT_OK: No rows returned.\n" );
		exit( 1 );
	}

	echo $ids[0] . "\n";
} catch ( Throwable $throwable ) {
	$message = $throwable->getMessage();
	fwrite( STDERR, 'NOT_OK: ' . ( $message !== '' ? $message : 'Unknown error while running SPARQL query.' ) . "\n" );
	exit( 1 );
}
