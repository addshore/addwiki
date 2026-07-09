<?php

declare( strict_types = 1 );

namespace Addwiki\Mediawiki\Api\Client;

use Addwiki\Mediawiki\Api\Client\Auth\AuthMethod;

class UserAgentHelper {

	public static function getUserAgent( AuthMethod $auth ): string {
		if ( $auth->userAgentOverride() !== null ) {
			return $auth->userAgentOverride();
		}

		$version = self::getLibraryVersion();
		$ua = "addwiki/addwiki-$version";
		$identifier = $auth->identifierForUserAgent();
		if ( $identifier !== null ) {
			$ua .= " ($identifier)";
		}
		$ua .= " mediawiki-api-base/$version";

		return $ua;
	}

	private static function getLibraryVersion(): string {
		try {
			if ( class_exists( '\Composer\InstalledVersions' ) ) {
				return \Composer\InstalledVersions::getPrettyVersion( 'addwiki/addwiki' ) ?? 'unknown';
			}
		} catch ( \OutOfBoundsException $e ) {
			// Fallback if the package is not installed
		}

		return 'unknown';
	}

}
