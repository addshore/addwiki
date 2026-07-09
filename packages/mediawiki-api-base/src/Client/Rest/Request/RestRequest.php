<?php

declare( strict_types = 1 );

namespace Addwiki\Mediawiki\Api\Client\Rest\Request;

use Addwiki\Mediawiki\Api\Client\Request\StandardRequest;

class RestRequest extends StandardRequest implements HasJsonBody {

	use JsonBodyTrait;

}
