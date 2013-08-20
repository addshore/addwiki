<?php
/**
 * Created by JetBrains PhpStorm.
 * User: km
 * Date: 8/20/13
 * Time: 2:20 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Addframe\Mediawiki;

/**
 * Generator for pages that have a specific template
 */
class TransclusionGenerator extends Generator {
	/**
	 * @param Site $site
	 * @param Page $page
	 */
	function __construct( $site, $page ) {
		$params = array(
			'list' => 'embeddedin',
			'eititle' => $page->getTitle(),
			'eilimit' => 'max'
		);
		parent::__construct( $site, $params );
	}

	/**
	 * @return Page
	 */
	function current() {
		$data = parent::current();
		return new Page( $this->site, $data['title'] );
	}
}