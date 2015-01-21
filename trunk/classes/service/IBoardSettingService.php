<?php

/**
 * Created by PhpStorm.
 * User: OKS
 * Date: 2014-12-22
 * Time: 오후 4:15
 */
class IBoardSettingService extends IBoardBaseService {
	public function getServiceName() {
		return 'iboard_setting';
	}

	private static $instance;

	public static function getInstance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new IBoardSettingService();
		}

		return self::$instance;
	}

	public function isAlready( $BID ) {
		return ! is_null( $this->settingFromBID( $BID ) );
	}

	/**
	 * @param $ID
	 *
	 * @return IBoardSetting
	 */
	public function settingFromID( $ID ) {
		return $this->fromID( 'IBoardSetting', $ID );
	}

	/**
	 * @param $BID
	 *
	 * @return IBoardSetting
	 */
	public function settingFromBID( $BID ) {
		$result = $this->db->wpdb->get_row( "select * from {$this->tableName} where bid='{$BID}'", ARRAY_A );

		return $this->fromArray( 'IBoardSetting', $result );
	}

	/**
	 * @return IBoardSetting[]
	 */
	public function getList() {
		$list    = array();
		$results = $this->db->wpdb->get_results( "select * from {$this->tableName} order by ID desc", ARRAY_A );

		foreach ( $results as $result ) {
			$list[] = $this->fromArray( 'IBoardSetting', $result );
		}

		return $list;
	}
}