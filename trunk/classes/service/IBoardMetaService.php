<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 31.
 * Time: 오전 12:54
 */
class IBoardMetaService extends IBoardBaseService {
	public function getServiceName() {
		return 'iboard_meta';
	}

	private static $instance;

	public static function getInstance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new IBoardMetaService();
		}

		return self::$instance;
	}

	public function metaFromKey( $key, $defaultValue = null ) {
		$query = "select * from {$this->tableName} where meta_key = '{$key}'";

		$result = $this->db->wpdb->get_row( $query, ARRAY_A );
		$result = $this->fromArray( 'IBoardMeta', $result );

		if ( is_null( $result ) ) {
			return $defaultValue;
		} else {
			$result->meta_value = maybe_unserialize( $result->meta_value );

			return $result;
		}
	}

	public function metaValueFromKey( $key, $defaultValue = null ) {
		$meta = $this->metaFromKey( $key, $defaultValue );

		if ( $meta instanceof IBoardMeta ) {
			return $meta->meta_value;
		} else {
			return $meta;
		}
	}

	public function update_meta( $key, $value ) {
		$meta  = $this->metaFromKey( $key );
		$model = new IBoardMeta( $key, maybe_serialize( $value ) );

		if ( is_null( $meta ) ) {
			$model->reg_date    = date( 'Y-m-d H:i:s' );
			$model->update_date = $model->reg_date;

			$this->db->wpdb->insert( $this->tableName, (array) $model );
		} else {
			$model->update_date = date( 'Y-m-d H:i:s' );
			$model->ID          = $meta->ID;

			$this->db->wpdb->update( $this->tableName, (array) $model, array( 'ID' => $model->ID ) );
		}

		return true;
	}

	public function delete_meta( $key ) {
		$query = "delete from {$this->tableName} where meta_key='{$key}'";

		return $this->db->wpdb->query( $query );
	}
}