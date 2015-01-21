<?php
if ( ! class_exists( 'Securimage' ) ) {
	require_once IBOARD_PLUGIN_DIR . 'library/securimage/securimage.php';
}

function iboard_get_captcha_img( $w = 50 ) {
	/* @var $wp_filesystem WP_Filesystem_Direct */
	global $wp_filesystem;

	$dir  = IBOARD_PLUGIN_DIR . 'library/securimage';
	$file = $dir . '/AHGBold.ttf';

	if ( ! is_file( $file ) ) {
		$wp_filesystem->copy( $dir . '/AHGBold', $file );
	} else {
		$size = $wp_filesystem->size( $file );

		if ( $size == 0 ) {
			$wp_filesystem->copy( $dir . '/AHGBold', $file );
		}
	}

	return IBOARD_PLUGIN_URL . "library/securimage/securimage_show.php?w={$w}";
}

function iboard_is_captcha_real() {
	$code            = iboard_request_post_param( 'ct_captcha' );
	$session_captcha = iboard_session_get( 'captcha_code' );

	iboard_session_del( 'captcha_code' );

	if ( ! is_null( $code ) && $session_captcha == $code ) {
		return true;
	} else {
		return false;
	}
}

function iboard_captcha_check_ajax() {
	$image   = new Securimage();
	$captcha = iboard_request_post_param( 'ct_captcha', false );

	if ( $image->check( $captcha ) == true ) {
		iboard_session_set( 'captcha_code', $captcha );
		echo "true";
	} else {
		iboard_session_set( 'captcha_code', false );
		echo "false";
	}

	die;
}

add_action( 'wp_ajax_iboard_captcha_check_ajax', 'iboard_captcha_check_ajax' );
add_action( 'wp_ajax_nopriv_iboard_captcha_check_ajax', 'iboard_captcha_check_ajax' );