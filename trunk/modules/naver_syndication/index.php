<?php
if ( ! class_exists( 'NaverSyndication' ) ) {
	require_once IBOARD_PLUGIN_DIR . 'library/naver_syndication/NaverSyndication.php';
}

require_once dirname( __FILE__ ) . "/classes/IBoardSyndicationEntry.php";
require_once dirname( __FILE__ ) . "/classes/IBoardNaverSyndicationXml.php";

function iboard_naver_syndication_setting() {
	?>
	<tr>
		<th style="width:130px;"><label for="iboard_syndication">신디케이션설정</label></th>
		<td>
			<p class="alignright"><a href="http://webmastertool.naver.com" target="_blank">신디케이션 키 받으러 가기.</a></p>
			<input type="text"
			       name="<?php __iboard_setting_name( 'syndication' ); ?>"
			       value="<?php echo iboard_get_setting_option( 'syndication' ); ?>"
			       id="iboard_syndication_token" style="width:100%;"/>

			<div class="alignright">
				<strong style="line-height:38px;margin-right:5px;">- 값을 입력하지 않으시면 신디케이션은 자동으로 동작하지 않습니다.</strong>
				<button type="button" class="button action actions" id="iboard_syndication_verify_btn"
				        style="margin-top:5px;">검증
			</div>
			</button>
		</td>
	</tr>
<?php
}

add_action( 'iboard_setting_page', 'iboard_naver_syndication_setting' );

function iboard_syndication_resources() {
	wp_register_script( 'iboard-syndication-setting', IBOARD_PLUGIN_MODULE_URL . '/naver_syndication/script.js', array( 'jquery' ) );
	wp_enqueue_script( 'iboard-syndication-setting' );
}

add_action( 'admin_enqueue_scripts', 'iboard_syndication_resources' );

function iboard_syndication_ajax_url() {
	return admin_url( 'admin-ajax.php' ) . "?action=iboard_naver_syndication";
}

function iboard_get_syndication() {
	$token = iboard_get_setting_option( 'syndication' );

	if ( ! empty( $token ) ) {
		$syndication = new NaverSyndication( $token );
		$syndication->setSyndicationService( new WPNaverSyndicationService() );

		return $syndication;
	}

	return false;
}

function iboard_send_syndication( $item ) {
	/* @var $iboard_authorizer IBoardAuthorizer */
	global $iboard_authorizer;

	if ( $item instanceof IBoardItem ) {
		$item->add_meta( 'permalink', get_permalink() );

		$iboard_authorizer->set_query_var( 'BID', $item->BID );
		$iboard_authorizer->set_query_var( 'ID', $item->ID );
		$iboard_authorizer->var_init();

		$role = $iboard_authorizer->getReadRole();

		if ( $role instanceof IBoardAuthorizerError ) {
			return false;
		}

		$ping_url = iboard_syndication_ajax_url() . "&ID={$item->ID}";

		$syndication = iboard_get_syndication();

		if ( $syndication ) {
			$result = $syndication->send( $ping_url );
			$item->add_meta( 'syndication_result', $result );
		}

		return true;
	}

	return false;
}

add_action( 'iboard_process_insert_after', 'iboard_send_syndication' );

function iboard_delete_syndication( $item ) {
	if ( $item instanceof IBoardItem ) {
		$syndication = iboard_get_syndication();

		if ( $syndication ) {
			$ping_url = iboard_syndication_ajax_url() . "&ID={$item->ID}&mode=delete";
			$syndication->send( $ping_url );

			$item->delete_meta( 'permalink' );
			$item->delete_meta( 'syndication_result' );

			return true;
		}
	}

	return false;
}

add_action( 'iboard_process_delete_after', 'iboard_delete_syndication' );

function iboard_syndication_verify_token_validate( $new_option, $old_option ) {
	$token = iboard_get_array_var( $new_option, 'syndication' );

	if ( empty( $token ) ) {
		return $new_option;
	}

	$syndication   = new NaverSyndication( $token );
	$check         = $syndication->verifyToken();
	$error_code    = iboard_get_array_var( $check, 'error_code', '-1', true );
	$error_message = iboard_get_array_var( $check, 'msg', '토큰값을 검사하세요' );

	if ( $error_code != '122' ) {
		iboard_alert( $error_message );

		return $old_option;
	}

	return $new_option;
}

add_filter( 'iboard_admin_page_validate_option', 'iboard_syndication_verify_token_validate', 1, 2 );

/**
 * register ajax
 */

function iboard_syndication_verify_token() {
	$token       = iboard_request_param( 'token' );
	$syndication = new NaverSyndication( $token );

	header( "Content-type: application/json; charset=utf-8" );
	echo json_encode( $syndication->verifyToken() );
	die;
}

add_action( 'wp_ajax_iboard_syndication_verify_token', 'iboard_syndication_verify_token' );

$iboardNaverSyndicationXml = new IBoardNaverSyndicationXml();

add_action( 'wp_ajax_iboard_naver_syndication', array( $iboardNaverSyndicationXml, 'render' ) );
add_action( 'wp_ajax_nopriv_iboard_naver_syndication', array( $iboardNaverSyndicationXml, 'render' ) );