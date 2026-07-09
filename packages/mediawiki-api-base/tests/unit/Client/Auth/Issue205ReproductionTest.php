<?php

declare( strict_types = 1 );

namespace Addwiki\Mediawiki\Api\Tests\Unit\Client\Auth;

use Addwiki\Mediawiki\Api\Client\Action\ActionApi;
use Addwiki\Mediawiki\Api\Client\Action\Request\ActionRequest;
use Addwiki\Mediawiki\Api\Client\Auth\UserAndPassword;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class Issue205ReproductionTest extends TestCase {

	public function testLoginWithNonJsonResponse(): void {
		$client = $this->createMock( ClientInterface::class );
		$mockResponse = $this->createMock( ResponseInterface::class );
		$mockResponse
			->method( 'getBody' )
			->willReturn( \GuzzleHttp\Psr7\Utils::streamFor( '<html>Not JSON</html>' ) );

		$client->method( 'request' )
			->willReturn( $mockResponse );

		$auth = new UserAndPassword( 'U1', 'P1' );
		$api = new ActionApi( 'http://example.com/api.php', $auth, $client );

		$this->expectException( \Addwiki\Mediawiki\Api\Client\Action\Exception\UnexpectedResponseException::class );
		$auth->preRequestAuth( ActionRequest::simpleGet( 'dummy' ), $api );
	}
}
