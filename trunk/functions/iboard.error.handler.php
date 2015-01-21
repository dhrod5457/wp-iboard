<?php
function iboard_register_error_handler() {
	set_error_handler( 'iboard_error_handler' );
}

function iboard_un_register_error_handler() {
	restore_error_handler();
}

function iboard_error_handler_mail_content_type() {
	return 'text/html';
}

function iboard_error_handler( $errno, $errmsg, $filename, $linenum, $vars ) {
	/* @var $iboard IBoard */
	global $iboard;

	if ( ! function_exists( 'wp_mail' ) ) {
		require_once( ABSPATH . 'wp-includes/pluggable.php' );
	}

	add_filter( 'wp_mail_content_type', 'iboard_error_handler_mail_content_type' );

	$iboard_error_handle_send_email = iboard_get_setting_option( 'iboard_error_handle_send_email', 'Y' );

	if ( $iboard_error_handle_send_email == 'Y' ) {
		if ( $errno >= E_PARSE ) {
			if ( $errno < E_STRICT ) {
				$message = $errmsg . " file : " . $filename . " line : " . $linenum;
				echo $message;

				wp_mail( IBOARD_AUTHOR_EMAIL, "IBOARD NO SHUTDOWN ERROR", $message );
			}

			return false;
		}
	}

	$site_url = get_home_url();

	$error_message = '<div style="word-break: break-all;font-size:12px;">';
	$error_message .= '<p>IBoard 에서 치명적 오류가 발생하였습니다..</p>';
	$error_message .= "{$errmsg} <br/><br/>";
	$error_message .= "name : {$filename} , line : {$linenum}<br/><br/>";
	$error_message .= "site : {$site_url}<br/><br/>";
	$error_message .= "plugin_version : {$iboard->version->currentVersion}<br/><br/>";
	$error_message .= "</div>";

	if ( $iboard_error_handle_send_email == 'Y' ) {
		wp_mail( IBOARD_AUTHOR_EMAIL, sprintf( "IBOARD ERROR REPORT : %s", $errmsg ), $error_message );
	}

	remove_filter( 'wp_mail_content_type', 'iboard_error_handler_mail_content_type' );

	$iboard_error_handle = iboard_get_setting_option( 'iboard_error_handle', 'Y' );

	if ( $iboard_error_handle == 'Y' ) {
		if ( ! function_exists( 'deactivate_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		deactivate_plugins( IBOARD_PLUGIN_BASE_NAME );

		if ( ! function_exists( 'wp_die' ) ) {
			require_once( ABSPATH . 'wp-includes/functions.php' );
		}

		wp_die( $error_message );
	}
}

function iboard_error_handle_setting() { ?>
	<tr>
		<th rowspan="2"><label for="iboard_error_handle">에러설정</label></th>
		<td>
			<select name="<?php echo __iboard_setting_name( 'iboard_error_handle' ); ?>" id="iboard_error_handle">
				<option value="Y">IBoard 를 비활성화함</option>
				<option value="N">플러그인은 그냥 유지함</option>
			</select>

			<p>에러 발생시 플러그인의 활성화/비활성화 여부</p>
		</td>
	</tr>
	<tr>
		<td>
			<select name="<?php echo __iboard_setting_name( 'iboard_error_handle_send_email' ); ?>"
			        id="iboard_error_handle_send_email">
				<option value="Y">에러를 IBoard 개발자에게 전송함</option>
				<option value="N">에러를 IBoard 개발자에게 전송안함</option>
			</select>

			<p>에러 발생시에 에러 전송 여부</p>
		</td>
	</tr>
<?php
}

//add_action( 'iboard_setting_page', 'iboard_error_handle_setting', 60 );