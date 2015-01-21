<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 23.
 * Time: 오전 1:13
 */
abstract class IBoardBasePage {
	/* @var IBoardSettingService */
	public $settingService;

	/* @var IBoardSetting */
	public $boardSetting;

	/* @var IBoardError */
	public $error;

	/* @var IBoard */
	public $iboard;

	public $skin;

	public $skinDir;

	public $skinType;

	public $currentView;

	public $args;

	/* @var IBoardAuthorizer */
	public $iboard_authorizer;

	public function __construct( $args ) {
		global $iboard_authorizer;

		$this->iboard_authorizer = $iboard_authorizer;
		$this->iboard_authorizer->set_query_var( 'BID', $args['BID'] );
		$this->iboard_authorizer->var_init();

		$this->settingService = IBoardSettingService::getInstance();
		$this->iboard         = IBoard::getInstance();
		$this->boardSetting   = $this->iboard_authorizer->boardSetting;

		if ( is_null( $this->skinType ) ) {
			$this->skinType = 'board';
		}

		$this->args = $args;

		$this->init( $args );

		$this->skinDir = $this->getSkinDir();
		$this->skin    = $this->getSkin();

		if ( is_file( $this->getSkinFolder() . "/functions.php" ) ) {
			require_once $this->getSkinFolder() . "/functions.php";
		}

		do_action( 'iboard_page_init', $this, $args );

		add_action( 'wp_enqueue_scripts', array( $this, 'registerResources' ), 1 );
	}

	function registerResources() {
		$this->registerStyleSheet();
		$this->registerJavascript();
	}

	function getSkinFolder() {
		$path = $this->getSkinDir() . DIRECTORY_SEPARATOR . $this->skinType . DIRECTORY_SEPARATOR . "{$this->getSkin()}";
		$path = apply_filters( 'iboard_get_skin_folder', $path, $this );
		$path = apply_filters( "iboard_get_skin_folder_{$this->boardSetting->BID}", $path, $this );

		return $path;
	}

	function getSkinDir() {
		$BID = $this->iboard->get_query_var( 'BID' );
		$t   = iboard_skin_dir_by_bid( $BID );

		return $t;
	}

	function getSkin() {
		$skin = '';
		if ( $this->skinType == 'board' ) {
			$skin = $this->boardSetting->skin;
		} else if ( $this->skinType == 'latest' ) {
			$skin = $this->boardSetting->skin_latest;
		}

		return iboard_skin( $this->iboard->get_query_var( 'BID' ), $this->skinType, $skin );
	}

	function getSkinUrl() {
		return iboard_skin_url( $this->iboard->get_query_var( 'BID' ), $this->skinType, $this->getSkin() );
	}

	public abstract function init( $args );

	public abstract function view();

	public function getView( $name ) {
		$this->currentView = $name;

		$action_name = str_replace( '.php', '', $name );

		do_action( 'iboard_pre_get_view', $action_name, $this );
		do_action( 'iboard_pre_get_view_' . $action_name, $this );

		$skin_file = $this->getSkinFolder() . DIRECTORY_SEPARATOR . "{$name}";

		if ( ! is_file( $skin_file ) ) {
			$this->boardSetting->skin = 'basic';
			IBoardSettingService::getInstance()->update( $this->boardSetting );

			wp_die( "해당 스킨이 없습니다. 기본 스킨으로 변경되었습니다. 새로고침 하세요." );
		}

		require $this->getSkinFolder() . DIRECTORY_SEPARATOR . "{$name}";

		do_action( 'iboard_after_get_view', $action_name, $this );
		do_action( 'iboard_after_get_view_' . $action_name, $this );
	}

	function hasStyleSheet() {
		return is_file( $this->getSkinFolder() . '/style.css' );
	}

	function hasJavaScript() {
		return is_file( $this->getSkinFolder() . '/script.js' );
	}

	function registerStyleSheet() {
		if ( $this->hasStyleSheet() && $this->boardSetting->isUseSkinCss() ) {
			wp_enqueue_style( 'iboard-' . $this->skinType . '-' . $this->iboard->get_query_var( 'BID' ), $this->getSkinUrl() . '/style.css', 55 );
		}
	}

	function registerJavascript() {
		if ( $this->hasJavaScript() ) {
			wp_enqueue_script( 'iboard-' . $this->skinType . '-' . $this->iboard->get_query_var( 'BID' ), $this->getSkinUrl() . '/script.js', 55 );
		}
	}

	public function render() {
		ob_start();

		$this->view();

		$res = ob_get_contents();

		ob_end_clean();

		$res = trim( str_replace( array( "\n", "\t", "\r" ), '', $res ) );

		return $res;
	}
}