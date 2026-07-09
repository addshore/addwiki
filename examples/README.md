# Examples

This directory contains small, self-contained example scripts.

This folder itself is a Composer project.

- `check.php`: Check whether a homepage URL appears to be a MediaWiki site.
- `mediawiki-siteinfo.php`: Minimal `mediawiki-api-base` siteinfo request.
- `mediawiki-last-log.php`: Fetch one recent public log event from a MediaWiki API endpoint.
- `mediawiki-namespaces.php`: List namespaces and IDs.
- `mediawiki-category-members.php`: Fetch first batch of members from a category (single request).
- `mediawiki-random-pages.php`: Fetch random pages using `PageListGetter`.
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
php mediawiki-siteinfo.php
php mediawiki-last-log.php
php mediawiki-namespaces.php
php mediawiki-category-members.php
php mediawiki-random-pages.php
php wikidata-search-earth.php
php wikidata-query-one.php
```

## Notes

- The examples are intentionally no-auth and public-read focused.
- Category traversal and full continuation-heavy list walking can trigger API throttling on public wikis; these examples prefer single-request patterns for reliable local runs.
- Upload/edit workflows (including multipart file uploads) require authentication and are therefore not included in this examples-only set.
