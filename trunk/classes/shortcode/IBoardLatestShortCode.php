<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 25.
 * Time: 오후 1:30
 */
class IBoardLatestShortCode extends IBoardBaseShortCode {
	public function shortCodeName() {
		return 'iboard_latest';
	}

	public function getIdentityVar() {
		return array( 'bid', 'url' );
	}

	public function shortCodePre( $atts ) {
		$BID = $atts[1];

		$boardSetting = IBoardSettingService::getInstance()->settingFromBID( $BID );

		if ( is_null( $boardSetting ) ) {
			do_action( 'iboard_not_exists_board', $BID );

			return;
		}
	}

	public function shortCodeAfter( $atts, $content = null ) {
		$param = array( 'BID' => $atts['bid'], 'url' => $atts['url'] );

		$this->page = new IBoardLatestPage( $param );

		if ( array_key_exists( 'row', $atts ) ) {
			$param['rowCount'] = $atts['row'];
		}

		$this->page->list = IBoardItemService::getInstance()->getList( $param );

		return do_shortcode( $content ) . $this->page->render();
	}
}