<?php
/**
 * Created by PhpStorm.
 * User: oks
 * Date: 15. 1. 11.
 * Time: 오후 7:33
 */

abstract class NaverSyndicationFeed implements NaverSyndicationFeedInterface {
	/* @var NaverSyndicationEntryInterface[] */
	public $entries = array();

	public function add_entry( NaverSyndicationEntryInterface $entry ) {
		$this->entries[] = $entry;
	}

	public function getUpdated() {
		return NaverSyndicationUtil::generateDate();
	}

	public function asXml() {
		ob_start();
		require NAVER_SYNDICATION_DIR_PATH . "/template/feed.php";
		$result = ob_get_contents();
		ob_get_clean();

		return '<?xml version="1.0" encoding="UTF-8"?>' . trim( $result );
	}
}