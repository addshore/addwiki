<?php

use Addframe\Http;
use Addframe\TestHttp;

class HttpTest extends PHPUnit_Framework_TestCase {

	function testCanGetDefaultInstance(){
		$http = Http::getDefaultInstance();
		$http2 = Http::getDefaultInstance();

		$this->assertInstanceOf( 'Addframe\Http', $http );
		$this->assertInstanceOf( 'Addframe\Http', $http2 );

		$this->assertEquals( $http->getUid(), $http2->getUid() );
	}

	function testCanConstruct(){
		$http = new Http();
		$http2 = new Http();

		$this->assertInstanceOf( 'Addframe\Http', $http );
		$this->assertInstanceOf( 'Addframe\Http', $http2 );

		$this->assertNotEquals( $http->getUid(), $http2->getUid() );
	}

	/**
	 * @dataProvider provideDataToEncode
	 */
	function testEncodeData( $data, $expected ){
		$http = new TestHttp();
		$encodedData = $http->encodeData( $data );
		$this->assertEquals( $expected, $encodedData );
	}

	function provideDataToEncode(){
		return array(
			//to encode, //encoded
			array( array( 'key' => 'value' ), 'key=value&' ),
			array( array( 'key_12' => 'value spaced' ), 'key_12=value+spaced&' ),
			array( array( 'chars' => '&&$$""' ), 'chars=%26%26%24%24%22%22&' ),
		);
	}

	function testTestHttp(){
		$testData = '{}';
		$testHttp = new TestHttp( $testData );
		$this->assertEquals( $testData, $testHttp->get( 'testurl' ) );
		$this->assertEquals( $testData, $testHttp->post( 'testurl', array() ) );
	}

}