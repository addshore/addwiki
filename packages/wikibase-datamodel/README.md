# wikibase-datamodel

[![GitHub issue custom search in repo](https://img.shields.io/github/issues-search/addwiki/addwiki?label=issues&query=is%3Aissue%20is%3Aopen%20%5Bwikibase-datamodel%5D)](https://github.com/addwiki/addwiki/issues?q=is%3Aissue+is%3Aopen+%5Bwikibase-datamodel%5D+)

Issue tracker: https://github.com/addwiki/addwiki/issues

This module is generally only for use by other addwiki modules.

There are probably not many use cases where you would want to use this module directly.

## Installation

Install the unified toolkit package:

```sh
composer require addwiki/addwiki
```

This module lives in `packages/wikibase-datamodel` and is autoloaded via the root package.

#### Load

```php
require_once( __DIR__ . '/vendor/autoload.php' );
```

## External Libraries

Some code, such as `MediaInfo` related code is pulled in from MediaWiki extensions and can be found in the `/lib` directory.
This is because this code is not available as a library, but there is little point in rewriting it...

This code can be updated using the `sync-copied-files` composer command.

- MediaInfo is pinned at `d86d961a0eb0c28e9b5d8ce600c64a9dae973533` which is just before the 2021 DataModel changes, which this library is not yet adapted for.