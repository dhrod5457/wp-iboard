<?php

/**
 * Created by PhpStorm.
 * User: OKS
 * Date: 2014-12-29
 * Time: 오전 11:37
 */
class IBoardUploader {
	const ALL   = 'iboard_all';
	const CHECK = 'iboard_check';

	public $copy_dir;
	public $copy_url;

	public $args;

	public $allow_ext;

	/* @var array */
	public $saved_files;

	public function __construct( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'permission_check' => false
		) );

		$this->args = $args;

		if ( ! $this->is_utf_8() ) {
			$this->saved_files = iboard_get_meta( 'iboard_uploaded_saved_files', null );
		}
	}

	public function upload_mimes( $existing_mimes = array() ) {
		$existing_mimes['hwp'] = 'application/hangul';

		return $existing_mimes;
	}

	public function initialize( $permission_check = false ) {
		$perm_path = $permission_check ? "/" . self::CHECK : "/" . self::ALL;
		$wp_dir    = wp_upload_dir();
		$base_path = "{$perm_path}{$wp_dir['subdir']}";

		$this->copy_dir = "{$wp_dir['basedir']}{$base_path}";
		$this->copy_url = "{$wp_dir['baseurl']}{$base_path}";

		if ( ! is_dir( $this->copy_dir ) ) {
			mkdir( $this->copy_dir, 0777, true );

			if ( $permission_check ) {
				if ( ! is_file( $this->copy_dir . '/.htaccess' ) ) {
					$from = IBOARD_PLUGIN_DIR . "classes/.htaccess";
					$to   = $this->copy_dir . '/.htaccess';

					copy( $from, $to );
				}
			}
		}
	}

	private function add_encoding_filter() {
		if ( ! $this->is_utf_8() ) {
			add_filter( 'sanitize_file_name', array( $this, 'sanitize_file_name' ) );
			add_filter( 'wp_handle_upload_prefilter', array( $this, 'sanitize_file_name' ) );
			add_filter( 'sanitize_file_name_chars', array( $this, 'sanitize_file_name_chars' ) );

		}
	}

	private function remove_encoding_filter() {
		if ( ! $this->is_utf_8() ) {
			remove_filter( 'sanitize_file_name', array( $this, 'sanitize_file_name' ) );
			remove_filter( 'wp_handle_upload_prefilter', array( $this, 'sanitize_file_name' ) );
			remove_filter( 'sanitize_file_name_chars', array( $this, 'sanitize_file_name_chars' ) );
		}
	}

	private function is_utf_8() {
		return PHP_OS != 'WINNT';
	}

	private function wp_handle_upload( $file, $overrides = false, $time = null ) {
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		$this->add_encoding_filter();

		$defaults = array(
			'test_form' => false
		);

		$overrides = wp_parse_args( $overrides, $defaults );

		add_filter( 'upload_mimes', array( $this, 'upload_mimes' ) );
		$result = wp_handle_upload( $file, $overrides, $time );
		remove_filter( 'upload_mimes', array( $this, 'upload_mimes' ) );

		$this->remove_encoding_filter();

		return $result;
	}

	private function move( $file, $move_file ) {
		$this->add_encoding_filter();

		$upload_file = $move_file['file'];

		$file_name = wp_unique_filename( $this->copy_dir, $file['name'] );

		$moved_file = "{$this->copy_dir}/{$file_name}";
		$moved_url  = "{$this->copy_url}/{$file_name}";

		rename( $upload_file, $moved_file );

		$this->remove_encoding_filter();

		return array(
			'dir'  => $moved_file,
			'url'  => $moved_url,
			'name' => $file_name
		);
	}

	public function upload( $file, $overrides = null, $time = null ) {
		$this->initialize( iboard_request_param( 'p_check', false ) );

		$move_file = $this->wp_handle_upload( $file, $overrides, $time );

		if ( $move_file ) {
			if ( ! empty( $move_file['error'] ) ) {
				return array(
					'result'  => false,
					'message' => $move_file['error']
				);
			}

			$move = $this->move( $file, $move_file );

			$result = array(
				'url'    => $move['url'],
				'dir'    => $move['dir'],
				'file'   => $file,
				'result' => true
			);

			if ( ! $this->is_utf_8() ) {
				$this->add_file_real_name( $move['name'], $file['name'] );
			}

			return $result;
		} else {
			return array(
				'result'  => false,
				'message' => '업로드에 실패하였습니다.'
			);
		}
	}

	public function get_file_real_name_by_name( $create_name ) {
		if ( ! $this->is_utf_8() ) {
			$info = pathinfo( $create_name );
			$ext  = ! empty( $info['extension'] ) ? '.' . $info['extension'] : '';
			$name = basename( $create_name, $ext );

			$option = iboard_get_meta( 'iboard_uploaded_saved_files', null );

			return $option[ $name ];
		} else {
			return $create_name;
		}
	}

	public function add_file_real_name( $create_name, $real_name ) {
		$info = pathinfo( $create_name );
		$ext  = ! empty( $info['extension'] ) ? '.' . $info['extension'] : '';
		$name = basename( $create_name, $ext );

		$this->saved_files[ $name ] = $real_name;
		iboard_update_meta( 'iboard_uploaded_saved_files', $this->saved_files );
	}

	function sanitize_file_name( $name ) {
		if ( is_array( $name ) ) {
			$file = $name;
			$name = $file['name'];
		}

		if ( ! empty( $name ) && seems_utf8( $name ) ) {
			$parts = explode( '.', $name );
			$ext   = array_pop( $parts );

			if ( empty( $parts ) ) {
				$file_name = $ext;
				$ext       = null;
			} else {
				$file_name = implode( '.', $parts );
			}

			$file_name = sanitize_title_with_dashes( $file_name );
			$file_name = str_replace( '%', '', $file_name );
			$name      = $file_name . ( $ext ? ".{$ext}" : '' );
		}

		if ( isset( $file ) && is_array( $file ) ) {
			$file['name'] = $name;
			$name         = $file;
		}

		return $name;
	}

	function sanitize_file_name_chars( $chars ) {
		if ( ! in_array( "%", $chars ) ) {
			$chars[] = "%";
		}
		if ( ! in_array( "+", $chars ) ) {
			$chars[] = "+";
		}

		return $chars;
	}
}
