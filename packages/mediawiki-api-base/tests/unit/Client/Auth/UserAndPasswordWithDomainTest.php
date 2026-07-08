<?php

declare( strict_types = 1 );

namespace Addwiki\Mediawiki\Api\Tests\Unit\Client\Auth;

use Addwiki\Mediawiki\Api\Client\Action\ActionApi;
use Addwiki\Mediawiki\Api\Client\Action\Request\ActionRequest;
use Addwiki\Mediawiki\Api\Client\Auth\UserAndPasswordWithDomain;
use GuzzleHttp\ClientInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers Mediawiki\Api\Client\Auth\UserAndPasswordWithDomain
 */
class UserAndPasswordWithDomainTest extends TestCase {

	/**
	 * @dataProvider provideValidConstruction
	 */
	public function testValidConstruction( string $user, string $pass, ?string $domain = null ): void {
		$userAndPasswordWithDomain = new UserAndPasswordWithDomain( $user, $pass, $domain );
		$this->assertSame( $user, $userAndPasswordWithDomain->getUsername() );
		$this->assertSame( $pass, $userAndPasswordWithDomain->getPassword() );
		$this->assertSame( $domain, $userAndPasswordWithDomain->getDomain() );
	}

	public function provideValidConstruction(): array {
		return [
			[ 'user', 'pass' ],
			[ 'user', 'pass', 'domain' ],
		];
	}

	/**
	 * @dataProvider provideInvalidConstruction
	 */
	public function testInvalidConstruction( string $user, string $pass, ?string $domain = null ): void {
		$this->expectException( InvalidArgumentException::class );
		 new UserAndPasswordWithDomain( $user, $pass, $domain );
	}

	public function provideInvalidConstruction(): array {
		return [
			[ 'user', '' ],
			[ '', 'pass' ],
			[ '', '' ],
			[ '', '', '' ],
			[ 'aaa', 'aaa', '' ],
		];
	}

	/**
	 * @dataProvider provideTestEquals
	 */
	public function testEquals( UserAndPasswordWithDomain $user1, UserAndPasswordWithDomain $user2, bool $shouldEqual ): void {
		$this->assertSame( $shouldEqual, $user1->equals( $user2 ) );
		$this->assertSame( $shouldEqual, $user2->equals( $user1 ) );
	}

	public function provideTestEquals(): array {
		return [
			[ new UserAndPasswordWithDomain( 'usera', 'passa' ), new UserAndPasswordWithDomain( 'usera', 'passa' ), true ],
			[ new UserAndPasswordWithDomain( 'usera', 'passa', 'domain' ), new UserAndPasswordWithDomain( 'usera', 'passa', 'domain' ), true ],
			[ new UserAndPasswordWithDomain( 'DIFF', 'passa' ), new UserAndPasswordWithDomain( 'usera', 'passa' ), false ],
			[ new UserAndPasswordWithDomain( 'usera', 'DIFF' ), new UserAndPasswordWithDomain( 'usera', 'passa' ), false ],
			[ new UserAndPasswordWithDomain( 'usera', 'passa' ), new UserAndPasswordWithDomain( 'DIFF', 'passa' ), false ],
			[ new UserAndPasswordWithDomain( 'usera', 'passa' ), new UserAndPasswordWithDomain( 'usera', 'DIFF' ), false ],
		];
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

	public function testDomainIsSent(): void {
		$client = $this->createMock( ClientInterface::class );
		$client->expects( $this->once() )
			->method( 'request' )
			->with(
				'POST',
				$this->anything(),
				$this->callback( static function ( $options ) {
					return isset( $options['form_params']['lgdomain'] ) && $options['form_params']['lgdomain'] === 'mydomain';
				} )
			)
			->willReturn( $this->getMockResponse( [ 'login' => [ 'result' => 'Success' ] ] ) );

		$auth = new UserAndPasswordWithDomain( 'U1', 'P1', 'mydomain' );
		$api = new ActionApi( '', $auth, $client );
		$auth->preRequestAuth( ActionRequest::simpleGet( 'dummy' ), $api );
	}

}
