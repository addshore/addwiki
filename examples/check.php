<?php

declare( strict_types = 1 );

use Addwiki\Mediawiki\Api\Client\MediaWiki;

require_once __DIR__ . '/vendor/autoload.php';

/*
Checks whether a given homepage URL looks like a MediaWiki site.

Default URL:
- `https://www.mediawiki.org/`

## Example usage
```sh
php check.php
php check.php https://www.mediawiki.org/
```

## Output
- Prints `OK` and exits with code `0` if the site appears to be MediaWiki.
- Prints `NOT_OK` and exits with code `1` otherwise.

## Detection strategy
The script uses `MediaWiki::newFromPage(...)` from `addwiki/addwiki`,
then calls `action()->getVersion()`. If that succeeds, it prints `OK`.
*/

$homepage = $argv[1] ?? 'https://www.mediawiki.org/';

try {
	MediaWiki::newFromPage( $homepage )->action()->getVersion();
	echo "OK\n";
	exit( 0 );
} catch ( Throwable $throwable ) {
	$message = $throwable->getMessage();
	fwrite( STDERR, 'NOT_OK: ' . ( $message !== '' ? $message : 'Unknown error while checking MediaWiki endpoint.' ) . "\n" );
	exit( 1 );
}
