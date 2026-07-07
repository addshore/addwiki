# wikibase-query

[![GitHub issue custom search in repo](https://img.shields.io/github/issues-search/addwiki/addwiki?label=issues&query=is%3Aissue%20is%3Aopen%20%5Bwikibase-query%5D)](https://github.com/addwiki/addwiki/issues?q=is%3Aissue+is%3Aopen+%5Bwikibase-query%5D+)

Issue tracker: https://github.com/addwiki/addwiki/issues

## Installation

Install the unified toolkit package:

    composer require addwiki/addwiki

This module lives in `packages/wikibase-query` and is autoloaded via the root package.

## Examples

Use the `SimpleQueryService` with wikidata.

```php
use Addwiki\Wikibase\Query\WikibaseQueryFactory;
use Addwiki\Wikibase\Query\PrefixSets;

$factory = new WikibaseQueryFactory(
    "https://query.wikidata.org/sparql",
    PrefixSets::WIKIDATA
);

$r = $factory->newSimpleQueryService()->query(["P31:Q1"]);
```