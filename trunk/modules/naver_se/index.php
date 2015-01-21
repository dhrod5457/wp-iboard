<?php
define( 'NAVER_SE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'NAVER_SE_PLUGIN_URL', IBOARD_PLUGIN_URL . "modules/naver_se/" );

function naverse_register_resources() {
	wp_register_script( 'naver-se', NAVER_SE_PLUGIN_URL . 'se/js/HuskyEZCreator.js' );
	wp_register_script( 'naver-se-js', NAVER_SE_PLUGIN_URL . 'naver_se.js', array(
		'jquery',
		'naver-se'
	) );
}

add_action( 'wp_enqueue_scripts', 'naverse_register_resources' );

function naverse_shortcode( $atts, $content = null ) {
	global $wp_filesystem;
	$wp_filesystem->chmod( NAVER_SE_PLUGIN_DIR, 0755 );

	$default = array(
		'name'        => '',
		'target_form' => '',
		'editor_name' => 'ir1',
		'width'       => '570px;',
		'height'      => '400px',
		'callback'    => ''
	);

	$atts = wp_parse_args( $atts, $default );

	$name        = $atts['name'];
	$target_form = $atts['target_form'];
	$editor_name = $atts['editor_name'];
	$width       = $atts['width'];
	$height      = $atts['height'];
	$callback    = $atts['callback'];

	$editor_content = '';
	$editor_content = apply_filters( 'naverse_editor_content', $editor_content );

	if ( ! empty( $callback ) ) {
		$editor_content = call_user_func( $callback );
	}

	wp_localize_script( 'naver-se', 'NaverSE', array(
		'sSkinURI'    => NAVER_SE_PLUGIN_URL . 'se/SmartEditor2Skin.html',
		'name'        => $name,
		'target_form' => $target_form,
		'editor_name' => $editor_name
	) );

	wp_enqueue_script( 'naver-se' );
	wp_enqueue_script( 'naver-se-js' );

	ob_start();

	echo "<textarea name='{$editor_name}' id='{$editor_name}' style='width:{$width}; height:{$height}; display:none;'>{$editor_content}</textarea>";
	echo '<input type="hidden" name="' . $name . '" id="' . $name . '"/>';

	$result = ob_get_contents();

	ob_end_clean();

	return do_shortcode( $content ) . $result;
}

add_shortcode( 'naverse', 'naverse_shortcode' );

function naverse_abs_path_save() {
	$_SESSION['ABSPATH'] = ABSPATH;
}

add_action( 'wp', 'naverse_abs_path_save' );