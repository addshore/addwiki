# addwiki

Addwiki is a unified PHP toolkit for interacting with MediaWiki, Wikibase, Wikimedia and related APIs.

To get started quickly, run the local scripts in `examples/`.

This repository is now the canonical source and distribution package.

Install with Composer:

```sh
composer require addwiki/addwiki
```

## Internal structure

Code is kept in internal package-style folders under `/packages` for separation of concerns, but distribution is done as a single Composer package: `addwiki/addwiki`.

**Most used namespaces:**

- `Addwiki\\Mediawiki\\Api`
- `Addwiki\\Wikibase\\Api`
- `Addwiki\\Wikibase\\Query`
- `Addwiki\\Wikimedia`

## Development

Run examples from the `examples/` directory.

### Testing & CI

Run the default local test workflow (lint + phpcs + unit tests):

```sh
composer run test
```

Run lint only:

```sh
composer lint
```

Run static analysis separately:

```sh
composer psalm
```

Run phpunit tests on a single internal package:

```sh
vendor/bin/phpunit packages/mediawiki-api-base/tests/unit
```

Integration tests are facilitated by `docker-compose-ci.yml` files which are currently kept in sync manually.
The setup in the monorepo should work for all packages.
Run it before running integration tests.

```sh
docker compose -f docker-compose-ci.yml up -d --build
```

Wait for the wiki to be accessible, then run the tests:

```sh
composer phpunit-integration
```
