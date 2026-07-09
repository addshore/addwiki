<?php

declare( strict_types = 1 );

namespace Addwiki\Mediawiki\Api\Client\Request;

interface HasPath {

	/**
	 * Extra path to be added onto the base. For example "/v0/foo"
	 */
	public function getPath(): string;

	public function setPath( string $path ): self;

}
