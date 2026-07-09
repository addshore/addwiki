<?php

declare( strict_types = 1 );

use Addwiki\Mediawiki\Api\Client\Action\ActionApi;
use Addwiki\Wikibase\Api\Service\EntitySearcher;

require_once __DIR__ . '/vendor/autoload.php';

/*
Search Wikidata entities with wbsearchentities.

Defaults:
- API endpoint: https://www.wikidata.org/w/api.php
- Search term: earth

Usage:
php wikidata-search-earth.php
php wikidata-search-earth.php "moon"
php wikidata-search-earth.php "moon" https://www.wikidata.org/w/api.php
*/

$term = $argv[1] ?? 'earth';
$apiUrl = $argv[2] ?? 'https://www.wikidata.org/w/api.php';

try {
	$searcher = new EntitySearcher( new ActionApi( $apiUrl ) );
	$ids = $searcher->search( 'item', $term, 'en' );

	if ( $ids === [] ) {
		fwrite( STDERR, "NOT_OK: No matching entities found.\n" );
		exit( 1 );
	}

	echo $ids[0] . "\n";
} catch ( Throwable $throwable ) {
	$message = $throwable->getMessage();
	fwrite( STDERR, 'NOT_OK: ' . ( $message !== '' ? $message : 'Unknown error while searching entities.' ) . "\n" );
	exit( 1 );
}
