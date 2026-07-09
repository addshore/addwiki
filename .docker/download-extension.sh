#!/usr/bin/env bash

set -euo pipefail

EXTENSION=$1
RELBRANCH=$2

TAR_URL=$(curl -fsSL "https://www.mediawiki.org/w/api.php?action=query&list=extdistbranches&edbexts=$EXTENSION&formatversion=2&format=json" | jq -r ".query.extdistbranches.extensions.$EXTENSION.$RELBRANCH // empty")

try_download() {
	local url=$1
	echo "Downloading $EXTENSION from: $url"
	curl -fsSL --retry 8 --retry-delay 1 --retry-all-errors --connect-timeout 10 --max-time 180 "$url" -o "$EXTENSION.tar.gz"
}

declare -a candidate_urls=()

if [[ -n "$TAR_URL" ]]; then
	candidate_urls+=("$TAR_URL")
fi

candidate_urls+=(
	"https://gerrit.wikimedia.org/r/plugins/gitiles/mediawiki/extensions/$EXTENSION/+archive/refs/heads/$RELBRANCH.tar.gz"
	"https://gerrit.wikimedia.org/r/plugins/gitiles/mediawiki/extensions/$EXTENSION/+archive/refs/tags/$RELBRANCH.tar.gz"
	"https://github.com/wikimedia/mediawiki-extensions-$EXTENSION/archive/refs/heads/$RELBRANCH.tar.gz"
)

downloaded=0
for url in "${candidate_urls[@]}"; do
	if try_download "$url"; then
		downloaded=1
		break
	fi
	echo "Download failed for $url, trying next source..."
done

if [[ "$downloaded" -ne 1 ]]; then
	echo "ERROR: Failed to download extension archive for $EXTENSION ($RELBRANCH) from all known sources" >&2
	exit 1
fi