<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 15. 1. 11.
 * Time: 오후 7:33
 */
abstract class NaverSyndicationEntry implements NaverSyndicationEntryInterface {
	/**
	 * @var bool
	 */
	public $publish = true;

	/**
	 * @var date
	 */
	public $published;

	/**
	 * @var date
	 */
	public $updated;

	public function getUpdated() {
		return NaverSyndicationUtil::generateDate( $this->updated );
	}

	public function getPublished() {
		return NaverSyndicationUtil::generateDate( $this->published );
	}

	public function getSummery() {
		return strip_tags( $this->getContent() );
	}

	public function isPublish() {
		return $this->publish;
	}

	public function asXml() {
		ob_start();
		require NAVER_SYNDICATION_DIR_PATH . "/template/entry.php";
		$result = ob_get_contents();
		ob_get_clean();

		return trim( $result );
	}
}