<?php

namespace Addframe\Test\Unit;

use FileNotFoundException;
use UnexpectedValueException;

/**
 * Class MediawikiTestCase adding some extra functions
 */
class MediawikiTestCase extends \PHPUnit_Framework_TestCase {

	/**
	 * @param $path string of data to get
	 * @throws UnexpectedValueException
	 * @throws FileNotFoundException
	 * @return string data from path
	 */
	protected function getTestApiData( $path ){
		$path =  __DIR__.'/Api/data/'.$path;

		if( ! ( $data = file_get_contents( $path ) ) ){
			throw new FileNotFoundException( "No data file (you should define it) from {$path}" );
		}

		//If there is no data throw an exception, we should define data!
		if( is_null( $data ) || $data === false || !is_string( $data ) || empty( $data ) ){
			throw new UnexpectedValueException( "Bad test data in file {$path}, '{$data}'" );
		}

		return $data;
	}

}