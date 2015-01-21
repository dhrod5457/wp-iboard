<?php

/**
 * Created by PhpStorm.
 * User: OKS
 * Date: 2014-12-22
 * Time: 오전 11:38
 */
class IBoardWpdb {
	private static $instance;

	public static function getInstance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new IBoardWpdb();
		}

		return self::$instance;
	}

	/* @var wpdb */
	public $wpdb;

	public function __construct() {
		global $wpdb;

		$this->wpdb = $wpdb;
	}

	public function getField( $table, $columnName ) {
		$query = "SHOW columns from {$table} where field='{$columnName}'";

		return $this->wpdb->get_row( $query );
	}

	public function isExistsColumn( $table, $columnName ) {
		return is_object( $this->getField( $table, $columnName ) );
	}

	public function addColumn( $table, $columnName, $type, $after = '' ) {
		if ( $this->isExistsColumn( $table, $columnName ) ) {
			return false;
		}

		$query = "alter table {$table} add column {$columnName} {$type} $after";

		return $this->wpdb->query( $query );
	}

	public function dropColumn( $table, $columnName ) {
		if ( $this->isExistsColumn( $table, $columnName ) ) {
			return false;
		}

		$query = "alter table {$table} drop column {$columnName}";

		return $this->wpdb->query( $query );
	}

	public function dropTable( $table ) {
		$query = "drop table {$table}";

		return $this->wpdb->query( $query );
	}

	public function copyTable( $oldTable, $newTable ) {
		$query = "INSERT INTO {$newTable} SELECT * FROM {$oldTable}";

		$this->wpdb->query( $query );

		$this->dropTable( $oldTable );
	}

	public function queryFromFile( $file, $param = array() ) {
		ob_start();
		require_once $file;
		$query = ob_get_contents();
		ob_end_clean();

		foreach ( $param as $key => $value ) {
			$query = str_replace( '#{' . $key . '}', $value, $query );
		}

		$queryList = explode( ";", $query );

		foreach ( $queryList as $sql ) {
			if ( trim( $sql ) == "" ) {
				continue;
			}

			$this->wpdb->query( $sql );
		}
	}
}