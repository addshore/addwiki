<?php

declare( strict_types = 1 );

use Addwiki\Mediawiki\Api\Client\Action\ActionApi;
use Addwiki\Mediawiki\Api\MediawikiFactory;

require_once __DIR__ . '/vendor/autoload.php';

/*
Get random pages via PageListGetter (single request).

Defaults:
- API endpoint: https://www.mediawiki.org/w/api.php
- Count: 3

Usage:
php mediawiki-random-pages.php
php mediawiki-random-pages.php 5
php mediawiki-random-pages.php 5 https://www.mediawiki.org/w/api.php
*/

$count = (int)( $argv[1] ?? 3 );
$apiUrl = $argv[2] ?? 'https://www.mediawiki.org/w/api.php';

try {
	$factory = new MediawikiFactory( new ActionApi( $apiUrl ) );
	$pages = $factory->newPageListGetter()->getRandom( [ 'rnlimit' => max( 1, min( $count, 10 ) ) ] );

	foreach ( $pages->toArray() as $page ) {
		echo $page->getPageIdentifier()->getTitle()->getText() . "\n";
	}
} catch ( Throwable $throwable ) {
	$message = $throwable->getMessage();
	fwrite( STDERR, 'NOT_OK: ' . ( $message !== '' ? $message : 'Unknown error while fetching random pages.' ) . "\n" );
	exit( 1 );
}
