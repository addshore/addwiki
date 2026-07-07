<?php

declare( strict_types = 1 );

use Addwiki\Mediawiki\Api\Client\Action\ActionApi;
use Addwiki\Mediawiki\Api\MediawikiFactory;

require_once __DIR__ . '/vendor/autoload.php';

/*
Get one recent public log event from a MediaWiki site.

Default API endpoint:
- https://en.wikipedia.org/w/api.php

Usage:
php mediawiki-first-log.php
php mediawiki-first-log.php https://www.mediawiki.org/w/api.php
*/

$apiUrl = $argv[1] ?? 'https://en.wikipedia.org/w/api.php';

try {
	$api = new ActionApi( $apiUrl );
	$factory = new MediawikiFactory( $api );
	$logList = $factory->newLogListGetter()->getLogList( [ 'lelimit' => 1 ] );
	$log = $logList->getLatest();

	if ( $log === null ) {
		fwrite( STDERR, "NOT_OK: No logs found.\n" );
		exit( 1 );
	}

	$title = $log->getPageIdentifier()->getTitle();

	echo sprintf(
		"%s %s/%s %s\n",
		$log->getTimestamp(),
		$log->getType(),
		$log->getAction(),
		$title ? $title->getText() : '(no title)'
	);
} catch ( Throwable $throwable ) {
	$message = $throwable->getMessage();
	fwrite( STDERR, 'NOT_OK: ' . ( $message !== '' ? $message : 'Unknown error while fetching logs.' ) . "\n" );
	exit( 1 );
}
