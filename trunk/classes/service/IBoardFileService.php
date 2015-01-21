<?php

/**
 * Created by PhpStorm.
 * User: OKS
 * Date: 2014-12-22
 * Time: 오후 12:19
 */
class IBoardFileService extends IBoardBaseService {
	public function getServiceName() {
		return 'iboard_file';
	}

	private static $instance;

	public static function getInstance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new IBoardFileService();
		}

		return self::$instance;
	}

	/**
	 * @param $ID
	 *
	 * @return IBoardFile
	 */
	public function fileFromID( $ID ) {
		return $this->fromID( 'IBoardFile', $ID );
	}

	/**
	 * @param array $args
	 *
	 * @return IBoardFile
	 */
	public function fileFromArray( array $args ) {
		return $this->fromArray( 'IBoardFile', $args );
	}

	/**
	 * @param $itemID
	 *
	 * @return IBoardFile[]
	 */
	public function fileListFromItemID( $itemID ) {
		$query   = "SELECT * FROM {$this->tableName} WHERE itemID=%d ";
		$results = array();
		$rows    = $this->db->wpdb->get_results( $this->db->wpdb->prepare( $query, array(
			'itemID' => $itemID
		) ), ARRAY_A );

		foreach ( $rows as $row ) {
			$results[] = $this->fileFromArray( $row );
		}

		return $results;
	}

	/**
	 * @param $boardItem
	 * @param $file
	 *
	 * @return false|int
	 */
	public function insertFile( $boardItem, $file ) {
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		$upload_overrides = array( 'test_form' => false );
		$upload_file      = wp_handle_upload( $file, $upload_overrides );

		$boardFile         = new IBoardFile();
		$boardFile->itemID = is_object( $boardItem ) ? $boardItem->ID : $boardItem['ID'];
		$boardFile->url    = $upload_file['url'];
		$boardFile->file   = $upload_file['file'];
		$boardFile->size   = $file['size'];
		$boardFile->name   = $file['name'];

		return $this->insert( (array) $boardFile );
	}
}