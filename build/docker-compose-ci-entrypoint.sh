#!/bin/bash

set -x

if [ ! -f entrypoint-done.txt ]; then

    # Wait for the DB to be ready?
    /wait-for-it.sh $MYSQL_SERVER:3306 -t 300
    sleep 1
    /wait-for-it.sh $MYSQL_SERVER:3306 -t 300

    # Install MediaWiki
    php maintenance/install.php --server="http://localhost:8877" --scriptpath= --dbtype mysql --dbuser $MYSQL_USER --dbpass $MYSQL_PASSWORD --dbserver $MYSQL_SERVER --lang en --dbname $MYSQL_DATABASE --pass LongCIPass123 SiteName CIUser

    # Settings for extensions
    echo "\$wgServer = 'http://localhost:8877';" >> LocalSettings.php
    echo "\$wgCanonicalServer = 'http://localhost:8877';" >> LocalSettings.php
    echo "\$wgScriptPath = '';" >> LocalSettings.php
    echo "wfLoadExtension( 'OAuth' );" >> LocalSettings.php
    echo "\$wgGroupPermissions['sysop']['mwoauthproposeconsumer'] = true;" >> LocalSettings.php
    echo "\$wgGroupPermissions['sysop']['mwoauthmanageconsumer'] = true;" >> LocalSettings.php
    echo "\$wgGroupPermissions['sysop']['mwoauthviewprivate'] = true;" >> LocalSettings.php
    echo "\$wgGroupPermissions['sysop']['mwoauthupdateownconsumer'] = true;" >> LocalSettings.php
    echo "require_once \"\$IP/extensions/Wikibase/vendor/autoload.php\";" >> LocalSettings.php
    echo "wfLoadExtension( 'WikibaseRepository', \"\$IP/extensions/Wikibase/extension-repo.json\" );" >> LocalSettings.php
    echo "require_once \"\$IP/extensions/Wikibase/repo/ExampleSettings.php\";" >> LocalSettings.php

    # Settings to make testing easier
    echo "\$wgGroupPermissions['*']['noratelimit'] = true;" >> LocalSettings.php
    echo "\$wgEnableUploads = true;" >> LocalSettings.php

    # Update MediaWiki & Extensions
    php maintenance/update.php --quick

    ## Run some needed scripts
    # Add a site for Wikibase sitelinks
    php maintenance/addSite.php mywiki default --interwiki-id --pagepath http://localhost:8877/index.php?title=\$1 --filepath http://localhost:8877/\$1
    echo "\$wgWBRepoSettings['siteLinkGroups'] = [ 'default' ];" >> LocalSettings.php
    # Add an OAuth Consumer
    php maintenance/resetUserEmail.php --no-reset-password CIUser CIUser@addwiki.github.io
    rm -f createOAuthConsumer.json createOAuthConsumer.stderr.log
    oauth_consumer_created=0
    for i in 1 2 3
    do
        CONSUMER_NAME="CIConsumer-${i}-$(date +%s%N)-$RANDOM"
        if php extensions/OAuth/maintenance/addwikiAddOauth.php --approve --callbackUrl https://CiConsumerUrl \
            --callbackIsPrefix true --user CIUser --name "$CONSUMER_NAME" --description CIConsumer --version 1.1.0 \
            --grants highvolume --jsonOnSuccess > createOAuthConsumer.json 2> createOAuthConsumer.stderr.log && \
            jq -e 'has("consumerKey") or has("key")' createOAuthConsumer.json > /dev/null
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

    cat createOAuthConsumer.json

    # Hide depreaction warnings from MediaWiki output
    echo "error_reporting(0);" >> LocalSettings.php

    # Mark the entrypoint as having run!
    echo "entrypoint done!" > entrypoint-done.txt

fi

# Run apache
apache2-foreground
