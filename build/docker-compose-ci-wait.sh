#!/bin/bash

set -euo pipefail

repo_path=$(dirname "$(dirname "$(realpath "$0")")")

compose_cmd=(docker compose --project-directory "$repo_path" --file "$repo_path/docker-compose-ci.yml")

container_id=$("${compose_cmd[@]}" ps -q mediawiki)
if [ -z "$container_id" ]; then
    echo "ERROR: Could not find mediawiki container"
    "${compose_cmd[@]}" ps
    exit 1
fi

max_attempts=120
previous_restart_count=-1
stable_running_checks=0

for i in $(seq 1 "$max_attempts")
do
    container_status=$(docker inspect -f '{{.State.Status}}' "$container_id" 2>/dev/null || echo "missing")
    restart_count=$(docker inspect -f '{{.RestartCount}}' "$container_id" 2>/dev/null || echo "-1")

    if [ "$container_status" = "running" ]; then
        if [ "$restart_count" = "$previous_restart_count" ]; then
            stable_running_checks=$((stable_running_checks + 1))
        else
            stable_running_checks=0
            previous_restart_count="$restart_count"
        fi

        if [ "$stable_running_checks" -ge 2 ]; then
            if curl --silent --show-error --fail --max-time 5 "http://127.0.0.1:8877/api.php?action=query&meta=siteinfo&format=json" | grep -q '"query"'; then
                echo "MediaWiki API is ready (attempt ${i}/${max_attempts}, restartCount=${restart_count})"
                exit 0
            fi
        fi
    fi

    sleep 1
done

echo "ERROR: Timed out waiting for MediaWiki API readiness"
"${compose_cmd[@]}" ps
"${compose_cmd[@]}" logs --tail=200 mediawiki || true
exit 1
