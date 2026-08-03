# Release notes

## v4.0.1

Clean up `composer.json` by removing some unused packages and adding a normalization test.

## v4.0.0

The start of the unified package release cycle.

This repository now ships as a single Composer package: `addwiki/addwiki`.

This release is fundamentally the same as the split packages release of v3.1.0, but within a single package.
The split packages will no longer be maintained and will be archived.

Things of interest to note in this release:

- Added examples in an `examples` folder
- More flexible User-Agent configuration
- Fix TypeError on non-JSON API response
- Allow installation with `"symfony/yaml": "~4.0||~5.0||^6.0"`
- Allow guzzlehttp/promises 2.0
- Remove mediawiki-flow-api package

And other less important things:

- Consolidated distribution into one package (`addwiki/addwiki`).
- Removed split-package Composer manifests and split-release workflows.
- Unified test and validation workflows at repository root.
- Consolidated packaging metadata and housekeeping files to the root.
- Refined internal package documentation and updated references to the unified package.
- Increased PHP and MW testing range in CI
- Totally removed the docs site stuff

## Historical notes

Release-note history previously maintained per internal module has been retired in favor of this single repository-level release notes file.

You can still find them in git history of the individual module folders.
