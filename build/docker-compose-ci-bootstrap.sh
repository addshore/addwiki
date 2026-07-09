#!/usr/bin/env bash

set -euo pipefail

repo_path=$(dirname "$(dirname "$(realpath "$0")")")
compose_cmd=(docker compose --project-directory "$repo_path" --file "$repo_path/docker-compose-ci.yml")

container_id=$("${compose_cmd[@]}" ps -q mediawiki)
if [ -z "$container_id" ]; then
	echo "ERROR: Could not find mediawiki container"
	"${compose_cmd[@]}" ps
	exit 1
fi

"${compose_cmd[@]}" exec -T mediawiki bash /dc-scripts/mediawiki-ci-bootstrap-inside.sh

echo "MediaWiki bootstrap completed"
