# mediawiki-api

[![GitHub issue custom search in repo](https://img.shields.io/github/issues-search/addwiki/addwiki?label=issues&query=is%3Aissue%20is%3Aopen%20%5Bmediawiki-api%5D)](https://github.com/addwiki/addwiki/issues?q=is%3Aissue+is%3Aopen+%5Bmediawiki-api%5D+)

## Installation

Install the unified toolkit package:

```sh
composer require addwiki/addwiki
```

This module lives in `packages/mediawiki-api` and is autoloaded via the root package.

## Example Usage

```php
// Load all the stuff
require_once( __DIR__ . '/vendor/autoload.php' );

// Create an authenticated API and services
$auth = new \Addwiki\Mediawiki\Api\Client\Auth\UserAndPassword( 'username', 'password' )
$api = new \Addwiki\Mediawiki\Api\Client\Action\ActionApi( 'http://localhost/w/api.php', $auth );
$services = new \Addwiki\Mediawiki\Api\MediawikiFactory( $api );

// Get a page
$page = $services->newPageGetter()->getFromTitle( 'Foo' );

// Edit a page
$content = new \Addwiki\Mediawiki\DataModel\Content( 'New Text' );
$revision = new \Addwiki\Mediawiki\DataModel\Revision( $content, $page->getPageIdentifier() );
$services->newRevisionSaver()->save( $revision );

// Move a page
$services->newPageMover()->move(
	$services->newPageGetter()->getFromTitle( 'FooBar' ),
	new Title( 'FooBar' )
);

// Delete a page
$services->newPageDeleter()->delete(
	$services->newPageGetter()->getFromTitle( 'DeleteMe!' ),
	array( 'reason' => 'Reason for Deletion' )
);

// Create a new page
$newContent = new \Addwiki\Mediawiki\DataModel\Content( 'Hello World' );
$title = new \Addwiki\Mediawiki\DataModel\Title( 'New Page' );
$identifier = new \Addwiki\Mediawiki\DataModel\PageIdentifier( $title );
$revision = new \Addwiki\Mediawiki\DataModel\Revision( $newContent, $identifier );
$services->newRevisionSaver()->save( $revision );

// List all pages in a category
$pages = $services->newPageListGetter()->getPageListFromCategoryName( 'Category:Cat name' );
```

