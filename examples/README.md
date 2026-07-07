# Examples

This directory contains small, self-contained example scripts.

This folder itself is a Composer project.

- `check.php`: Check whether a given homepage URL appears to be a MediaWiki site.
- `mediawiki-last-log.php`: Fetch one recent public log event from a MediaWiki API endpoint.
- `wikidata-search-earth.php`: Search Wikidata entities (default term: `earth`) via `wbsearchentities`.
- `wikidata-query-one.php`: Run one tiny SPARQL query against the Wikidata Query Service.

## Setup

Install dependencies:

```sh
composer install
```

## Run

```sh
php check.php
php mediawiki-first-log.php
php wikidata-search-earth.php
php wikidata-query-one.php
```
