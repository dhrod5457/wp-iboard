<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 23.
 * Time: 오전 1:19
 */
class IBoardPageShortCode extends IBoardBaseShortCode {
	/* @var IBoard */
	public $iboard;

	public function shortCodeName() {
		return 'iboard';
	}

	public function getIdentityVar() {
		return 'bid';
	}

	public function shortCodePre( $atts ) {
		$BID = $atts[1];

		$this->prepareIboard( $BID );

		$boardSetting = IBoardSettingService::getInstance()->settingFromBID( $BID );

		$GLOBALS['iboard_setting'] = $boardSetting;

		if ( is_null( $boardSetting ) ) {
			do_action( 'iboard_not_exists_board', $BID );

			return;
		}

		$this->page = $this->createPage();
	}

	public function shortCodeAfter( $atts, $content = null ) {
		if ( is_null( $this->page ) ) {
			return false;
		}

		return do_shortcode( $content ) . $this->page->render();
	}

	public function prepareIboard( $BID ) {
		$this->iboard = IBoard::getInstance();
		$this->iboard->set_query_var( 'BID', $BID );
	}

	public function createPage() {
		switch ( $this->iboard->get_query_var( 'pageMode' ) ) {
			case 'list' :
				$page = new IBoardListPage( $this->iboard->query_vars );
				break;
			case 'read':
				$page = new IBoardReadPage( $this->iboard->query_vars );
				break;
			case 'edit':
				$page = new IBoardEditPage( $this->iboard->query_vars );
				break;
			case 'reply':
				$page = new IBoardEditPage( $this->iboard->query_vars );
				break;
			case 'delete':
				$page = new IBoardEditPage( $this->iboard->query_vars );
				break;
			case 'down':
				$page = new IBoardDownLoadPage( $this->iboard->query_vars );
				break;
		}

		$page = apply_filters( 'iboard_create_page', $page );

		return $page;
	}
}