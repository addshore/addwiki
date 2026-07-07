<?php

declare( strict_types = 1 );

use Addwiki\Mediawiki\Api\Client\Action\ActionApi;
use Addwiki\Mediawiki\Api\Client\Action\Request\ActionRequest;

require_once __DIR__ . '/vendor/autoload.php';

/*
Fetch the first batch of pages in a category (single API request).

Defaults:
- API endpoint: https://www.mediawiki.org/w/api.php
- Category: Category:Help
- Limit: 5

Usage:
php mediawiki-category-members.php
php mediawiki-category-members.php "Category:Extensions"
php mediawiki-category-members.php "Category:Extensions" 10 https://www.mediawiki.org/w/api.php
*/

$categoryTitle = $argv[1] ?? 'Category:Help';
$limit = (int)( $argv[2] ?? 5 );
$apiUrl = $argv[3] ?? 'https://www.mediawiki.org/w/api.php';

try {
	$api = new ActionApi( $apiUrl );
	$result = $api->request( ActionRequest::simpleGet( 'query', [
		'list' => 'categorymembers',
		'cmtitle' => $categoryTitle,
		'cmlimit' => max( 1, $limit ),
	] ) );

	foreach ( $result['query']['categorymembers'] ?? [] as $member ) {
		echo $member['title'] . "\n";
	}
} catch ( Throwable $throwable ) {
	$message = $throwable->getMessage();
	fwrite( STDERR, 'NOT_OK: ' . ( $message !== '' ? $message : 'Unknown error while fetching category members.' ) . "\n" );
	exit( 1 );
}
