<?php

declare( strict_types = 1 );

use Addwiki\Mediawiki\Api\Client\Action\ActionApi;
use Addwiki\Mediawiki\Api\Client\Action\Request\ActionRequest;

require_once __DIR__ . '/vendor/autoload.php';

/*
Minimal mediawiki-api-base example: fetch siteinfo.

Default API endpoint:
- https://www.mediawiki.org/w/api.php

Usage:
php mediawiki-siteinfo.php
php mediawiki-siteinfo.php https://www.mediawiki.org/w/api.php
*/

$apiUrl = $argv[1] ?? 'https://www.mediawiki.org/w/api.php';

try {
	$api = new ActionApi( $apiUrl );
	$result = $api->request( ActionRequest::simpleGet( 'query', [
		'meta' => 'siteinfo',
		'siprop' => 'general',
		'continue' => '',
	] ) );

	echo ( $result['query']['general']['generator'] ?? 'Unknown generator' ) . "\n";
} catch ( Throwable $throwable ) {
	$message = $throwable->getMessage();
	fwrite( STDERR, 'NOT_OK: ' . ( $message !== '' ? $message : 'Unknown error while fetching siteinfo.' ) . "\n" );
	exit( 1 );
}
