<?php

declare( strict_types = 1 );

use Addwiki\Mediawiki\Api\Client\Action\ActionApi;
use Addwiki\Mediawiki\Api\MediawikiFactory;

require_once __DIR__ . '/vendor/autoload.php';

/*
List namespaces from a public MediaWiki installation.

Default API endpoint:
- https://www.mediawiki.org/w/api.php

Usage:
php mediawiki-namespaces.php
php mediawiki-namespaces.php https://www.mediawiki.org/w/api.php
*/

$apiUrl = $argv[1] ?? 'https://www.mediawiki.org/w/api.php';

try {
	$factory = new MediawikiFactory( new ActionApi( $apiUrl ) );
	$namespaces = $factory->newNamespaceGetter()->getNamespaces();

	foreach ( $namespaces as $namespace ) {
		echo $namespace->getId() . ': ' . $namespace->getLocalName() . "\n";
	}
} catch ( Throwable $throwable ) {
	$message = $throwable->getMessage();
	fwrite( STDERR, 'NOT_OK: ' . ( $message !== '' ? $message : 'Unknown error while listing namespaces.' ) . "\n" );
	exit( 1 );
}
