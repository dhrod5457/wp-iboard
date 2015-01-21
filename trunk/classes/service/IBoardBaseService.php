<?php

/**
 * Created by PhpStorm.
 * User: OKS
 * Date: 2014-12-22
 * Time: 오후 12:32
 */
abstract class IBoardBaseService {
	public abstract function getServiceName();

	/* @var IBoardWpdb */
	public $db;

	public $tableName;

	public function __construct() {
		$this->db        = IBoardWpdb::getInstance();
		$this->tableName = apply_filters( 'iboard_table_name', $this->db->wpdb->prefix . $this->getServiceName() );
	}

	public function insert( $args ) {
		if ( is_object( $args ) ) {
			$param = (array) $args;
		} else if ( is_array( $args ) ) {
			$param = $args;
		}

		$param = apply_filters( $this->getServiceName() . 'insert', $param );

		if ( iboard_is_error( $param ) ) {
			return $param;
		}

		do_action( 'iboard_insert_pre', $this->getServiceName(), $param );
		do_action( "iboard_insert_pre_{$this->getServiceName()}", $param );

		$this->db->wpdb->insert( $this->tableName, $param );

		$param['ID'] = $this->db->wpdb->insert_id;

		do_action( 'iboard_insert_after', $this->getServiceName(), $param );
		do_action( "iboard_insert_after_{$this->getServiceName()}", $param );

		return $param['ID'];
	}

	public function update( $args ) {
		$defaults = array( 'filter' => true );

		if ( is_object( $args ) ) {
			$args = (array) $args;
		}

		$args   = wp_parse_args( $args, $defaults );
		$filter = $args['filter'];
		unset( $args['filter'] );

		if ( $filter ) {
			$args = apply_filters( $this->getServiceName() . 'update', $args );
		}

		if ( iboard_is_error( $args ) ) {
			return $args;
		}

		if ( $filter ) {
			do_action( 'iboard_update_pre', $this->getServiceName(), $args );
			do_action( "iboard_update_pre_{$this->getServiceName()}", $args );
		}

		$this->db->wpdb->update( $this->tableName, $args, array( 'ID' => $args['ID'] ) );

		if ( $filter ) {
			do_action( 'iboard_update_after', $this->getServiceName(), $args );
			do_action( "iboard_update_after_{$this->getServiceName()}", $args );
		}

		return $args;
	}

	public function delete( $param ) {
		$param = apply_filters( $this->getServiceName() . 'delete', $param );

		if ( iboard_is_error( $param ) ) {
			return $param;
		}

		do_action( 'iboard_delete_pre', $this->getServiceName(), $param );
		do_action( "iboard_delete_pre_{$this->getServiceName()}", $param );

		$this->db->wpdb->delete( $this->tableName, array( 'ID' => $param['ID'] ) );

		do_action( 'iboard_delete_after', $this->getServiceName(), $param );
		do_action( "iboard_delete_after_{$this->getServiceName()}", $param );

		return $param;
	}

	public function fromID( $className, $ID ) {
		$query  = "select * from {$this->tableName} where ID=%d LIMIT 1";
		$result = $this->db->wpdb->get_row( $this->db->wpdb->prepare( $query, $ID ), ARRAY_A );
		$result = apply_filters( $this->getServiceName() . 'fromID', $result );

		if ( is_array( $result ) ) {
			return $this->fromArray( $className, $result );
		}

		return false;
	}

	public function fromArray( $className, $args ) {
		if ( is_null( $args ) ) {
			return null;
		}

		$result = new ReflectionClass( $className );
		$result = $result->newInstance();

		$reflection = new ReflectionClass( $className );

		foreach ( $reflection->getProperties() as $var ) {
			if ( $var->isPublic() ) {
				$key = $var->getName();

				if ( isset( $args[ $key ] ) ) {
					$var->setValue( $result, $args[ $key ] );
				}
			}

			if ( $var->isPrivate() ) {

			}
		}

		$result = apply_filters( $this->getServiceName() . 'fromArray', $result );

		return $result;
	}
} 