<?php

declare( strict_types = 1 );

namespace Addwiki\Mediawiki\Api\Client\Auth;

trait UserAgentSettings {

	private ?string $userAgentIdentifier = null;

	private ?string $userAgentOverride = null;

	public function setIdentifierForUserAgent( string $identifier ): void {
		$this->userAgentIdentifier = $identifier;
	}

	public function setUserAgentOverride( string $userAgent ): void {
		$this->userAgentOverride = $userAgent;
	}

	public function userAgentOverride(): ?string {
		return $this->userAgentOverride;
	}

	protected function getCustomUserAgentIdentifier(): ?string {
		return $this->userAgentIdentifier;
	}

}
