<?php
function iboard_upload_init() {
	$pageMode = iboard_get_query_var( 'pageMode' );

	if ( $pageMode == 'edit' || $pageMode == 'reply' ) {
		add_action( 'iboard_editor_pre', 'iboard_upload_button' );
	}
}

add_action( 'iboard_page_init', 'iboard_upload_init' );

function iboard_upload_button( $args = array() ) {
	$defaults = array(
		'title'        => __iboard( '미디어 업로드' ),
		'id'           => 'iboard_upload_btn',
		'class'        => 'iboard_upload_btn',
		'js_callback'  => 'iboard_upload_editor_callback',
		'pre_function' => 'iboard_upload_editor_pre',
		'p_check'      => false
	);

	$args = wp_parse_args( $args, $defaults );

	if ( is_super_admin() && $args['js_callback'] == 'iboard_upload_editor_callback' ) {
		return;
	}

	$button = "<input type='button'
				value='{$args['title']}'
				class='iboard_button iboard_button_default " . $args['class'] . "'
				id='{$args['id']}'
				data-callback='{$args['js_callback']}'
			    data-pre_function='{$args['pre_function']}'
			    data-p_check='{$args['p_check']}' />";

	add_action( 'iboard_after_get_view_edit', 'iboard_upload_form' );
	echo $button;
}

function iboard_upload_form() {
	?>
	<form id="iboardUploadForm"
	      action="<?php echo admin_url( 'admin-ajax.php' ); ?>?action=iboard_ajax_upload_file"
	      method="post"
	      enctype="multipart/form-data">
		<label for="iboard_upload_file"><?php __iboard_e( '파일 업로드' ); ?></label>
		<input type="file" name="iboard_upload_file" id="iboard_upload_file"/>
		<input type="hidden" name="p_check" id="p_check"/>
		<input type="hidden" name="BID" value="<?php echo iboard_get_query_var( 'BID' ); ?>"/>
	</form>
<?php
}

function iboard_upload( $file ) {
	/* @var $iboard_uploader IBoardUploader */
	global $iboard_uploader;

	$BID = iboard_request_param( 'BID', null );

	if ( is_null( $BID ) ) {
		return array(
			'result'  => false,
			'message' => __iboard( '잘못된 접근입니다.' )
		);
	}

	$result = $iboard_uploader->upload( $file );

	unset( $result['file']['tmp_name'] );
	unset( $result['dir'] );

	return $result;
}

function iboard_ajax_upload_file() {
	if ( ! function_exists( 'wp_handle_upload' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}

	if ( ! isset( $_FILES['iboard_upload_file'] ) ) {
		return;
	}

	$uploadedFiles = $_FILES['iboard_upload_file'];

	$result = iboard_upload( $uploadedFiles );

	header( "Content-type:application/json; charset=utf-8" );

	echo json_encode( $result );

	die;
}

add_action( 'wp_ajax_iboard_ajax_upload_file', 'iboard_ajax_upload_file' );
add_action( 'wp_ajax_nopriv_iboard_ajax_upload_file', 'iboard_ajax_upload_file' );