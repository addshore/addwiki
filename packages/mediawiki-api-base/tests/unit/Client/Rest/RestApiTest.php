<?php

declare( strict_types = 1 );

namespace Addwiki\Mediawiki\Api\Tests\Unit\Client\Rest;

use Addwiki\Mediawiki\Api\Client\Action\Tokens;
use Addwiki\Mediawiki\Api\Client\Request\StandardRequest;
use Addwiki\Mediawiki\Api\Client\Rest\RestApi;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class RestApiTest extends TestCase {

	/**
	 * @return ClientInterface&MockObject
	 */
	private function getMockClient() {
		return $this->createMock( ClientInterface::class );
	}

	/**
	 * @return MockObject&ResponseInterface
	 */
	private function getMockResponse( $responseValue ) {
		$mock = $this->createMock( ResponseInterface::class );
		$mock
			->method( 'getBody' )
			->willReturn( \GuzzleHttp\Psr7\Utils::streamFor( json_encode( $responseValue ) ) );
		return $mock;
	}

	private function getMockTokens() {
		return $this->createMock( Tokens::class );
	}

	private function getUserAgent(): string {
		$version = \Composer\InstalledVersions::getPrettyVersion( 'addwiki/addwiki' );
		return "addwiki/addwiki-$version mediawiki-api-base/$version";
	}

	public function testGetRequest(): void {
		$client = $this->getMockClient();
		$client->expects( $this->once() )
			->method( 'request' )
			->with( 'GET', 'http://localhost/rest.php/path', [
				'query' => [ 'foo' => 'bar', 'assert' => 'anon' ],
				'headers' => [ 'User-Agent' => $this->getUserAgent() ],
			] )
			->will( $this->returnValue( $this->getMockResponse( [ 'ok' => true ] ) ) );

		$api = new RestApi( 'http://localhost/rest.php', null, $client, $this->getMockTokens() );

		$request = new class extends StandardRequest {
			public function getPath(): string {
				return '/path';
			}

			public function getMethod(): string {
				return 'GET';
			}
		};
		$request->setParam( 'foo', 'bar' );

		$result = $api->request( $request );
		$this->assertEquals( [ 'ok' => true ], $result );
	}

	public function testMultipartPostRequestWithExtraHeaders(): void {
		$client = $this->getMockClient();
		$client->expects( $this->once() )
			->method( 'request' )
			->with( 'POST', 'http://localhost/rest.php/upload', [
				'multipart' => [
					[
						'name' => 'chunk',
						'contents' => 'data',
						'headers' => [ 'Content-Disposition' => 'form-data; name="chunk"; filename="foo.jpg"' ],
					],
					[ 'name' => 'assert', 'contents' => 'anon' ],
				],
				'headers' => [ 'User-Agent' => $this->getUserAgent() ],
			] )
			->will( $this->returnValue( $this->getMockResponse( [ 'ok' => true ] ) ) );

		$api = new RestApi( 'http://localhost/rest.php', null, $client, $this->getMockTokens() );

		$request = new class extends StandardRequest {
			public function getPath(): string {
				return '/upload';
			}

			public function getMethod(): string {
				return 'POST';
			}
		};
		$request->setParam( 'chunk', 'data' );
		$request->setMultipartParams( [
			'chunk' => [ 'headers' => [ 'Content-Disposition' => 'form-data; name="chunk"; filename="foo.jpg"' ] ],
		] );

		$result = $api->request( $request );
		$this->assertEquals( [ 'ok' => true ], $result );
	}

}
