<?php

declare( strict_types = 1 );

namespace Addwiki\Mediawiki\Api\Client\Action\Exception;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class UnexpectedResponseException extends RuntimeException {

	private ResponseInterface $response;

	public function __construct( ResponseInterface $response, $message = 'Unexpected response from MediaWiki API' ) {
		$this->response = $response;
		parent::__construct( $message );
	}

	public function getResponse(): ResponseInterface {
		return $this->response;
	}

}
