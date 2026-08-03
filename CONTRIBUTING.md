# Contributing

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

Integration tests are facilitated by the `docker-compose-ci.yml` file.
Run it before running integration tests.

```sh
docker compose -f docker-compose-ci.yml up -d --build
```

Wait for the wiki to be accessible, then run the tests:

```sh
composer phpunit-integration
```

## Releases

To publish a new release:

1. Determine the new [semantic version](https://semver.org) number.
2. Update `RELEASENOTES.md` with details of the release.
3. Create a tag (either locally or on GitHub) with a name matching `v*.*.*`.
4. Push the tag to GitHub, where the [release.yaml](./.github/workflows/release.yml) workflow will create a new Release.

[Packagist](https://packagist.org/packages/addwiki/addwiki) will be updated automatically.
