<?php

declare( strict_types = 1 );

namespace Addwiki\Mediawiki\Api\Client\Request;

interface HasMethod {

	/**
	 * A HTTP Method. e.g. 'GET'
	 */
	public function getMethod(): string;

	public function setMethod( string $method ): self;

}
