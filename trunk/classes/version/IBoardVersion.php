<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 25.
 * Time: 오전 12:42
 */
class IBoardVersion {
	public $currentVersion;

	public function __construct() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		$iboard_plugin_data = get_plugin_data( IBOARD_PLUGIN_DIR . 'index.php' );

		$this->currentVersion = $iboard_plugin_data['Version'];
	}

	private static $instance;

	public static function getInstance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new IBoardVersion();
		}

		return self::$instance;
	}

	function getVersion() {
		$result = apply_filters( 'IBoardVersion_getVersion', $this->currentVersion );

		return $result;
	}

	function getOldVersion() {
		$old_version = get_option( 'iboard_old_version', 0 );
		$old_version = apply_filters( 'iboard_old_version', $old_version );

		return intval( $old_version );
	}

	function isUpgrade() {
		return $this->getVersion() > $this->getOldVersion();

	}

	function isDownGrade() {
		return $this->getOldVersion() > $this->getVersion();
	}
} 