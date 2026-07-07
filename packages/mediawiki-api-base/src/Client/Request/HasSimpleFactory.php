<?php

declare( strict_types = 1 );

namespace Addwiki\Mediawiki\Api\Client\Request;

interface HasSimpleFactory {

	public static function factory(): self;

	public static function f(): self;

}
