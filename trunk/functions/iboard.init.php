<?php

function __iboard_e( $text ) {
	_e( $text, IBOARD_PLUGIN_NAME );
}

function __iboard( $text ) {
	return __( $text, IBOARD_PLUGIN_NAME );
}

function iboard_register_languages() {
	load_plugin_textdomain( IBOARD_PLUGIN_NAME, false, dirname( plugin_basename( __FILE__ ) ) . "/../languages/" );
}

add_action( 'plugins_loaded', 'iboard_register_languages' );

function iboard_register_resources() {
	wp_register_script( 'iboard-admin', IBOARD_PLUGIN_URL . 'resources/js/iboard.admin.js', array(
		'jquery',
		'jquery-form'
	) );

	wp_register_script( 'iboard-base', IBOARD_PLUGIN_URL . 'resources/js/iboard.js', array(
		'jquery',
		'jquery-form',
		'json2'
	) );

	wp_register_script( 'iboard-ajax', IBOARD_PLUGIN_URL . 'resources/js/iboard.ajax.js', array(
		'jquery',
		'json2',
		'iboard-base'
	) );

	wp_register_style( 'iboard-base', IBOARD_PLUGIN_URL . 'resources/css/base.css' );

	wp_register_style( 'jquery-chosen', IBOARD_PLUGIN_URL . 'resources/js/jquery.chosen/chosen.min.css' );
	wp_register_script( 'jquery-chosen', IBOARD_PLUGIN_URL . 'resources/js/jquery.chosen/chosen.jquery.min.js', array( 'jquery' ) );

	wp_register_script( 'jquery-validation', IBOARD_PLUGIN_URL . 'resources/js/jquery-validation/jquery.validate.min.js', array( 'jquery' ) );
	wp_register_script( 'jquery-validation-message-ko', IBOARD_PLUGIN_URL . "resources/js/jquery-validation/localization/messages_ko.min.js", array( 'jquery-validation' ) );
}

add_action( 'iboard_init', 'iboard_register_resources' );

/*
 * validate.js는 글쓰기 화면과 패스워드 입력 화면에만 등록된다.
 */
function iboard_register_validate_script() {
	wp_enqueue_script( 'jquery-validation' );

	if ( get_locale() == 'ko_KR' ) {
		wp_enqueue_script( 'jquery-validation-message-ko' );
	}
}

add_action( 'iboard_pre_get_view_read', 'iboard_register_validate_script' );
add_action( 'iboard_pre_get_view_edit', 'iboard_register_validate_script' );
add_action( 'iboard_pre_get_view_password', 'iboard_register_validate_script' );

class IBoardNaverseFilter {
	/* @var IBoardEditPage */
	static $page;

	function __construct() {
		add_action( 'iboard_pre_get_view_edit', array( $this, 'iboard_naverse_editor_content' ) );
	}

	function iboard_naverse_editor_content( IBoardEditPage $page ) {
		self::$page = $page;
		add_filter( 'naverse_editor_content', array( $this, 'iboard_naverse_editor_content_filter' ) );
	}

	function iboard_naverse_editor_content_filter( $content ) {
		return self::$page->boardItem->getContent();
	}
}

new IBoardNaverseFilter();

function iboard_localize_script() {
	wp_localize_script( 'iboard-base', 'IBoardHelper', array(
		'permalink' => get_permalink()
	) );
}

add_action( 'wp', 'iboard_localize_script' );

function iboard_page_init( $page ) {
	$param = array(
		'ajax_url'                => admin_url( 'admin-ajax.php' ),
		'is_admin'                => is_super_admin(),
		'is_login'                => is_user_logged_in(),
		'query_vars'              => json_encode( iboard_get_query_vars() ),
		'message_delete'          => __iboard( "삭제" ),
		'iboard_plugin_url'       => IBOARD_PLUGIN_URL,
		'iboard_captcha_ajax_url' => admin_url( 'admin-ajax.php' ) . "?action=iboard_captcha_check_ajax"
	);

	if ( $page instanceof IBoardBasePage ) {
		$param['iboard_ID']             = $page->boardSetting->BID;
		$param['iboard_skin']           = $page->getSkin();
		$param['iboard_file_cnt']       = $page->boardSetting->file_cnt;
		$param['message_over_file_cnt'] = sprintf( __iboard( "파일은 %s 개 까지 업로드 할수 있습니다." ), $page->boardSetting->file_cnt );
	}

	wp_localize_script( 'iboard-base', 'IBoard', $param );
	add_action( 'wp_enqueue_scripts', 'iboard_enqueue_resources', 1 );
}

function iboard_enqueue_resources() {
	wp_enqueue_style( 'iboard-base' );
	wp_enqueue_script( 'iboard-base' );
	wp_enqueue_script( 'iboard-ajax' );

	do_action( 'iboard_enqueue_resources' );
}

add_action( 'iboard_page_init', 'iboard_page_init' );

function iboard_pre_get_view_edit_action( IBoardEditPage $page ) {
	if ( $page->mode == 'insert' ) {
		$page->boardItem->content = $page->boardSetting->base_content;
	}
}

add_action( 'iboard_pre_get_view_edit', 'iboard_pre_get_view_edit_action' );

function iboard_pre_get_view_read_action( IBoardReadPage $page ) {
	$page->boardItem->content = iboard_remove_banned_words( $page->boardItem->content, $page->boardSetting->getBannedWords() );
}

add_action( 'iboard_pre_get_view_read', 'iboard_pre_get_view_read_action' );

/**
 * iframe 호출을 위함
 */
function iboard_single_view() {
	$iboard_ID = iboard_request_param( 'iboard_ID' );

	if ( ! is_null( $iboard_ID ) ) {
		add_filter( 'show_admin_bar', '__return_false' );

		$iboardPageShortcode = new IBoardPageShortCode();
		$iboardPageShortcode->shortCodePre( array(
			'0' => $iboard_ID,
			'1' => $iboard_ID
		) );

		add_shortcode( $iboardPageShortcode->shortCodeName(), array( $iboardPageShortcode, 'shortCodeAfter' ) );
		require_once $iboardPageShortcode->page->getSkinFolder() . "/single.php";

		die;
	}
}

add_action( 'template_redirect', 'iboard_single_view' );

function iboard_upgrade_action() {
	iboard_upgrade();
}

add_action( 'iboard_upgrade', 'iboard_upgrade_action' );

function iboard_item_subject_filter( $subject, IBoardItem $item ) {
	if ( ! empty( $item->category ) ) {
		$subject = "<span class=\"iboard_subject_category\">[{$item->category}]</span> $subject";
	}

	if ( $item->hasFile() ) {
		$subject = "$subject <span class=\"iboard_subject_has_file\" data-file_cnt=\"{$item->getFileCnt()}\">[파일있음]</span>";
	}

	if ( $item->is_secret ) {
		$subject = $subject . '<span class="is_secret">[' . __iboard( "비밀글" ) . ']</span>';
	}

	return $subject;
}

function iboard_pre_get_view_list_subject_filter() {
	add_filter( 'IboardItem_getSubject', 'iboard_item_subject_filter', 1, 2 );
}

add_action( 'iboard_pre_get_view_list', 'iboard_pre_get_view_list_subject_filter' );
