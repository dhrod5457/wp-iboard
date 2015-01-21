<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 25.
 * Time: 오후 1:42
 */
class IBoardLatestPage extends IBoardBasePage {
	/* @var IBoardItem[] */
	public $list;

	public $url;

	public function init( $args ) {
		$this->skinType = 'latest';
		$this->url      = $args['url'];
	}

	public function view() {
		return $this->getView( 'list.php' );
	}
}