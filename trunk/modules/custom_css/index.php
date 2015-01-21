<?php

function iboard_custom_css_resources() {
	/* @var $iboard_admin_page IBoardAdminPage */
	global $iboard_admin_page;

	if ( 'iboard_bbs_setting' == $iboard_admin_page->currentPage() ) {
		wp_register_script( 'code-mirror', IBOARD_PLUGIN_MODULE_URL . '/custom_css/codemirror/codemirror.js' );
		wp_register_style( 'code-mirror', IBOARD_PLUGIN_MODULE_URL . '/custom_css/codemirror/codemirror.css' );
		wp_register_script( 'iboard-custom', IBOARD_PLUGIN_MODULE_URL . '/custom_css/custom-css.js', array(
			'jquery',
			'code-mirror'
		) );

		wp_enqueue_style( 'code-mirror' );
		wp_enqueue_script( 'code-mirror' );
		wp_enqueue_script( 'iboard-custom' );
	}
}

add_action( 'admin_enqueue_scripts', 'iboard_custom_css_resources' );

function iboard_custom_css_setting() { ?>
	<tr>
		<th><label for="">CUSTOM CSS</label></th>
		<td>
			<textarea name="<?php echo __iboard_setting_name( 'iboard_custom_css' ); ?>"
			          id="iboard_custom_css"
			          style="min-height:150px;width:100%;box-sizing: border-box;"><?php echo iboard_get_setting_option( 'iboard_custom_css' ) ?></textarea>
		</td>
	</tr>
<?php
}

add_action( 'iboard_setting_page', 'iboard_custom_css_setting', 55 );

function iboard_custom_css_create() {
	/* @var $wp_filesystem WP_Filesystem_Direct */
	global $wp_filesystem;

	if ( is_null( $wp_filesystem ) ) {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}

	WP_Filesystem();

	$css = iboard_get_setting_option( 'iboard_custom_css', '/* add your css */' );

	$wp_filesystem->put_contents( IBOARD_PLUGIN_DIR . 'resources/css/custom.css', $css, 0777 );
}

add_action( 'iboard_page_init', 'iboard_custom_css_create' );

function iboard_register_custom_css() {
	$css = iboard_get_setting_option( 'iboard_custom_css' );

	if ( ! empty( $css ) ) {
		wp_enqueue_style( 'iboard-custom', IBOARD_PLUGIN_URL . 'resources/css/custom.css' );
	}
}

add_action( 'iboard_enqueue_resources', 'iboard_register_custom_css' );