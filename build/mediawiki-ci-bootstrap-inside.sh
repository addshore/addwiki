#!/usr/bin/env bash

set -euo pipefail

cd /var/www/html

append_if_missing() {
	local line=$1
	local file=$2
	grep -Fqx "$line" "$file" || echo "$line" >> "$file"
}

/wait-for-it.sh "$MYSQL_SERVER:3306" -t 300
sleep 1
/wait-for-it.sh "$MYSQL_SERVER:3306" -t 300

if [ ! -f LocalSettings.php ]; then
	php maintenance/install.php --server="http://localhost:8877" --scriptpath= --dbtype mysql --dbuser "$MYSQL_USER" --dbpass "$MYSQL_PASSWORD" --dbserver "$MYSQL_SERVER" --lang en --dbname "$MYSQL_DATABASE" --pass LongCIPass123 SiteName CIUser
fi

append_if_missing "\$wgServer = 'http://localhost:8877';" LocalSettings.php
append_if_missing "\$wgCanonicalServer = 'http://localhost:8877';" LocalSettings.php
append_if_missing "\$wgScriptPath = '';" LocalSettings.php
append_if_missing "wfLoadExtension( 'OAuth' );" LocalSettings.php
append_if_missing "\$wgGroupPermissions['sysop']['mwoauthproposeconsumer'] = true;" LocalSettings.php
append_if_missing "\$wgGroupPermissions['sysop']['mwoauthmanageconsumer'] = true;" LocalSettings.php
append_if_missing "\$wgGroupPermissions['sysop']['mwoauthviewprivate'] = true;" LocalSettings.php
append_if_missing "\$wgGroupPermissions['sysop']['mwoauthupdateownconsumer'] = true;" LocalSettings.php
append_if_missing "require_once \"\$IP/extensions/Wikibase/vendor/autoload.php\";" LocalSettings.php
append_if_missing "wfLoadExtension( 'WikibaseRepository', \"\$IP/extensions/Wikibase/extension-repo.json\" );" LocalSettings.php
append_if_missing "require_once \"\$IP/extensions/Wikibase/repo/ExampleSettings.php\";" LocalSettings.php
append_if_missing "\$wgGroupPermissions['*']['noratelimit'] = true;" LocalSettings.php
append_if_missing "\$wgEnableUploads = true;" LocalSettings.php
append_if_missing "\$wgWBRepoSettings['siteLinkGroups'] = [ 'default' ];" LocalSettings.php
append_if_missing "error_reporting(0);" LocalSettings.php

php maintenance/update.php --quick
php maintenance/addSite.php mywiki default --interwiki-id --pagepath http://localhost:8877/index.php?title=\$1 --filepath http://localhost:8877/\$1 || true
php maintenance/resetUserEmail.php --no-reset-password CIUser CIUser@addwiki.github.io

rm -f createOAuthConsumer.json createOAuthConsumer.stderr.log
oauth_consumer_created=0

for i in 1 2 3
do
	CONSUMER_NAME="CIConsumer-${i}-$(date +%s%N)-$RANDOM"
	if php extensions/OAuth/maintenance/addwikiAddOauth.php --approve --callbackUrl https://CiConsumerUrl \
		--callbackIsPrefix true --user CIUser --name "$CONSUMER_NAME" --description CIConsumer --version 1.1.0 \
		--grants highvolume --jsonOnSuccess > createOAuthConsumer.json 2> createOAuthConsumer.stderr.log && \
		jq -e 'has("key") and has("secret") and has("accessToken") and has("accessSecret")' createOAuthConsumer.json > /dev/null
	then
		oauth_consumer_created=1
		break
	fi
	echo "OAuth consumer creation attempt ${i} failed, retrying..."
	sleep 1
done

if [ "$oauth_consumer_created" -ne 1 ]; then
	echo "ERROR: Failed to create OAuth consumer JSON after 3 attempts" >&2
	if [ -f createOAuthConsumer.json ]; then
		echo "createOAuthConsumer.json contents:" >&2
		cat createOAuthConsumer.json >&2
	else
		echo "createOAuthConsumer.json was not created" >&2
	fi
	if [ -f createOAuthConsumer.stderr.log ]; then
		echo "OAuth consumer creation stderr:" >&2
		cat createOAuthConsumer.stderr.log >&2
	fi
	exit 1
fi

curl --silent --show-error --fail --max-time 5 "http://127.0.0.1/api.php?action=query&meta=siteinfo&format=json" | jq -e '.query' > /dev/null
cat createOAuthConsumer.json
