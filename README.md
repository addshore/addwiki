# Addwiki

[![Packagist](https://img.shields.io/packagist/v/addwiki/addwiki)](https://packagist.org/packages/addwiki/addwiki)
[![Tests](https://github.com/addshore/addwiki/actions/workflows/composer_test.yaml/badge.svg)](https://github.com/addshore/addwiki/actions/workflows/composer_test.yaml)

Addwiki is a unified PHP toolkit for interacting with MediaWiki, Wikibase, Wikimedia and related APIs.

To get started quickly, examine and run the scripts in `examples/`.

The different parts of Addwiki used to be separate packages,
but `addwiki/addwiki` is now the canonical source and distribution package
and should be used in preference to the older ones.

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
