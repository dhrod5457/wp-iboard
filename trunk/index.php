<?php
/*
Plugin Name: IBoard
Plugin URI: http://www.saweb.co.kr
Description: IBoard 는 간단하게 워드프레스에 추가할수 있는 한국형 게시판 플러그인 입니다.
Version: 0.8.5.5
Author: IPeople
Author URI: http://www.saweb.co.kr
*/

define( 'IBOARD_PLUGIN_NAME', 'IBoard' );

define( 'IBOARD_PLUGIN_BASE_NAME', plugin_basename( __FILE__ ) );
define( 'IBOARD_PLUGIN_URL', plugin_dir_url( '' ) . 'iboard/' );
define( 'IBOARD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'IBOARD_SKIN_DIR', IBOARD_PLUGIN_DIR . 'views/skin' );
define( 'IBOARD_SKIN_URL', IBOARD_PLUGIN_URL . 'views/skin' );
define( 'IBOARD_PLUGIN_MODULE_DIR', IBOARD_PLUGIN_DIR . 'modules' );
define( 'IBOARD_PLUGIN_MODULE_URL', IBOARD_PLUGIN_URL . 'modules' );
define( 'IBOARD_AUTHOR_EMAIL', 'dhrod0325@naver.com' );

require_once IBOARD_PLUGIN_DIR . 'functions/iboard.error.handler.php';

require_once IBOARD_PLUGIN_DIR . 'classes/db/IBoardWpdb.php';

require_once IBOARD_PLUGIN_DIR . 'classes/setup/IBoard.php';

require_once IBOARD_PLUGIN_DIR . 'classes/auth/IBoardUser.php';
require_once IBOARD_PLUGIN_DIR . 'classes/auth/IBoardBaseAuth.php';
require_once IBOARD_PLUGIN_DIR . 'classes/auth/IBoardItemAuth.php';
require_once IBOARD_PLUGIN_DIR . 'classes/auth/IBoardListAuth.php';
require_once IBOARD_PLUGIN_DIR . 'classes/auth/IBoardFileAuth.php';
require_once IBOARD_PLUGIN_DIR . 'classes/auth/IBoardCommentAuth.php';
require_once IBOARD_PLUGIN_DIR . 'classes/auth/IBoardAuthorizer.php';

require_once IBOARD_PLUGIN_DIR . 'classes/model/IBoardError.php';
require_once IBOARD_PLUGIN_DIR . 'classes/model/IBoardItem.php';
require_once IBOARD_PLUGIN_DIR . 'classes/model/IBoardItemList.php';
require_once IBOARD_PLUGIN_DIR . 'classes/model/IBoardSetting.php';
require_once IBOARD_PLUGIN_DIR . 'classes/model/IBoardMeta.php';
require_once IBOARD_PLUGIN_DIR . 'classes/model/IBoardComment.php';
require_once IBOARD_PLUGIN_DIR . 'classes/model/IBoardCommentList.php';

require_once IBOARD_PLUGIN_DIR . 'classes/pagination/IBoardPagination.php';

require_once IBOARD_PLUGIN_DIR . 'classes/service/IBoardBaseService.php';
require_once IBOARD_PLUGIN_DIR . 'classes/service/IBoardItemService.php';
require_once IBOARD_PLUGIN_DIR . 'classes/service/IBoardSettingService.php';
require_once IBOARD_PLUGIN_DIR . 'classes/service/IBoardMetaService.php';
require_once IBOARD_PLUGIN_DIR . 'classes/service/IBoardCommentService.php';

require_once IBOARD_PLUGIN_DIR . 'classes/handler/IBoardProcessHandler.php';

require_once IBOARD_PLUGIN_DIR . 'classes/interceptor/IBoardBaseInterceptor.php';
require_once IBOARD_PLUGIN_DIR . 'classes/interceptor/IBoardSecurityInterceptor.php';
require_once IBOARD_PLUGIN_DIR . 'classes/interceptor/IBoardSettingInterceptor.php';
require_once IBOARD_PLUGIN_DIR . 'classes/interceptor/IBoardItemInterceptor.php';
require_once IBOARD_PLUGIN_DIR . 'classes/interceptor/IBoardDeleteInterceptor.php';
require_once IBOARD_PLUGIN_DIR . 'classes/interceptor/IBoardProcessInterceptor.php';
require_once IBOARD_PLUGIN_DIR . 'classes/interceptor/IBoardCommentInterceptor.php';
require_once IBOARD_PLUGIN_DIR . 'classes/interceptor/IBoardCommentProcessInterceptor.php';

require_once IBOARD_PLUGIN_DIR . 'classes/shortcode/IBoardBaseShortCode.php';
require_once IBOARD_PLUGIN_DIR . 'classes/shortcode/IBoardPageShortCode.php';
require_once IBOARD_PLUGIN_DIR . 'classes/shortcode/IBoardLatestShortCode.php';
require_once IBOARD_PLUGIN_DIR . 'classes/shortcode/IBoardIFrameShortCode.php';

require_once IBOARD_PLUGIN_DIR . 'classes/page/admin/IBoardAdminPage.php';
require_once IBOARD_PLUGIN_DIR . 'classes/page/admin/IBoardAdminAjax.php';

require_once IBOARD_PLUGIN_DIR . 'classes/page/user/IBoardBasePage.php';
require_once IBOARD_PLUGIN_DIR . 'classes/page/user/IBoardListPage.php';
require_once IBOARD_PLUGIN_DIR . 'classes/page/user/IBoardReadPage.php';
require_once IBOARD_PLUGIN_DIR . 'classes/page/user/IBoardEditPage.php';
require_once IBOARD_PLUGIN_DIR . 'classes/page/user/IBoardLatestPage.php';
require_once IBOARD_PLUGIN_DIR . 'classes/page/user/IBoardDownLoadPage.php';
require_once IBOARD_PLUGIN_DIR . 'classes/page/user/IBoardCommentPage.php';

require_once IBOARD_PLUGIN_DIR . 'classes/version/IBoardVersion.php';
require_once IBOARD_PLUGIN_DIR . 'classes/helper/IBoardUploader.php';

require_once IBOARD_PLUGIN_DIR . 'functions/iboard.functions.php';
require_once IBOARD_PLUGIN_DIR . 'functions/iboard.init.php';
require_once IBOARD_PLUGIN_DIR . 'functions/iboard.upload.php';
require_once IBOARD_PLUGIN_DIR . 'functions/iboard.captcha.php';
require_once IBOARD_PLUGIN_DIR . 'functions/iboard.upgrade.php';

function iboard_init() {
	if ( ! headers_sent() ) {
		@session_start();
	}

	do_action( 'iboard_init' );
}

add_action( 'init', 'iboard_init', 9999 );

function iboard_init_classes() {
	$GLOBALS['iboard']            = IBoard::getInstance();
	$GLOBALS['iboard_uploader']   = new IBoardUploader();
	$GLOBALS['iboard_authorizer'] = new IBoardAuthorizer();
	$GLOBALS['iboard_admin_page'] = new IBoardAdminPage();

	new IBoardAdminAjax();
	new IBoardPageShortCode();
	new IBoardLatestShortCode();
	new IBoardIFrameShortCode();
}

add_action( 'iboard_init', 'iboard_init_classes' );

function iboard_register_interceptors() {
	new IBoardSecurityInterceptor();
	new IBoardSettingInterceptor();
	new IBoardItemInterceptor();
	new IBoardDeleteInterceptor();
	new IBoardCommentInterceptor();

	new IBoardProcessInterceptor();
	new IBoardCommentProcessInterceptor();
}

add_action( 'wp', 'iboard_register_interceptors' );
