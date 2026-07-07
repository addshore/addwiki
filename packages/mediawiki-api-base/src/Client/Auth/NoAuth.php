<?php

declare( strict_types = 1 );

namespace Addwiki\Mediawiki\Api\Client\Auth;

use Addwiki\Mediawiki\Api\Client\Request\Request;
use Addwiki\Mediawiki\Api\Client\Request\Requester;

/**
 * For use with plain MediaWiki and no authentication (anon)
 */
class NoAuth implements AuthMethod {

	private ?string $userAgentIdentifier = null;

	public function preRequestAuth( Request $request, Requester $requester ): Request {
		// Verify that the user is logged in if set to user, not logged in if set to anon, or has the bot user right if bot.
		$request->setParam( 'assert', 'anon' );
		return $request;
	}

	public function identifierForUserAgent(): ?string {
		return $this->userAgentIdentifier;
	}

	public function setIdentifierForUserAgent( string $identifier ): void {
		$this->userAgentIdentifier = $identifier;
	}

}
