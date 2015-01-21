<?php

class IBoardApi {
	/* @var IBoardAuthorizer */
	public $authorizer;

	public function __construct() {
		$this->authorizer = new IBoardAuthorizer();
	}

	function iboard_api_item_filter() {
		add_filter( IBoardItemService::getInstance()->getServiceName() . 'getItem', array(
			$this,
			'iboard_api_item_pre'
		) );
	}

	function iboard_api_item_pre( $boardItem ) {
		if ( ! $boardItem instanceof IBoardItem ) {
			return;
		}

		unset( $boardItem->password );

		return $boardItem;
	}

	function remove_iboard_api_item_filter() {
		remove_filter( IBoardItemService::getInstance()->getServiceName() . 'getItem', array(
			$this,
			'unset_password'
		) );
	}

	public function getProcessHandler() {
		iboard_register_interceptors();
		$handler = new IBoardProcessHandler();

		return $handler;
	}

	public function insert() {
		$handler = $this->getProcessHandler();
		$insert  = $handler->insert( $_POST );
		$this->jsonView( $insert );
	}

	public function update() {
		$handler = $this->getProcessHandler();
		$result  = $this->authorizer->getUpdateRole();

		if ( $result->result ) {
			$update = $handler->update( $_POST );

			if ( $update instanceof IBoardProcessError ) {
				$this->jsonView( $update );
			}
		}

		$this->jsonView( $update );
	}

	public function delete() {
		$handler = $this->getProcessHandler();
		$result  = $this->authorizer->getDeleteRole();

		if ( $result->result ) {
			$handler->delete( $_POST );
		}

		$this->jsonView( $result );
	}


	public function getList() {
		iboard_register_interceptors();

		$result = $this->authorizer->getListRoles();

		if ( isset( $result->objects['listItem'] ) ) {
			$listItem = $result->objects['listItem'];

			if ( $listItem instanceof IBoardItemList ) {
				$listItem->unset_items_password();
			}
		}

		$this->jsonView( $result );
	}

	public function getDetail() {
		iboard_register_interceptors();

		$result = $this->authorizer->getReadRole();
		$this->jsonView( $result );
	}

	public function error_required( $field ) {
		$this->error( array(
			'message' => "required {$field}"
		) );
	}

	public function error( $args = array() ) {
		$args = wp_parse_args( $args, array( 'result' => false, 'message' => 'error' ) );
		$this->jsonView( $args );
	}

	public function jsonView( $var = array() ) {
		header( "Content-type: application/json; charset=utf-8" );
		$var = wp_parse_args( $var, array( 'result' => true, 'message' => 'success' ) );
		echo json_encode( $var );

		die;
	}

	public function getPage() {
		iboard_register_interceptors();

		/* @var $iboard IBoard */
		global $iboard;

		$iboard->set_query_var( 'BID', iboard_request_param( 'BID' ) );
		$iboard->request_init();

		$page = iboard_request_param( 'page' );

		switch ( $page ) {
			case 'list' :
				$result = new IBoardListPage( $iboard->query_vars );
				break;
			case 'read':
				$result = new IBoardReadPage( $iboard->query_vars );
				break;
		}

		header( "Content-type: text/html; charset=utf-8" );
		echo $result->render();
		die;
	}
}

$api = new IBoardApi();

add_action( 'wp_ajax_iboard_api_list', array( $api, 'getList' ) );
add_action( 'wp_ajax_nopriv_iboard_api_list', array( $api, 'getList' ) );

add_action( 'wp_ajax_iboard_api_detail', array( $api, 'getDetail' ) );
add_action( 'wp_ajax_nopriv_iboard_api_detail', array( $api, 'getDetail' ) );

add_action( 'wp_ajax_iboard_api_insert', array( $api, 'insert' ) );
add_action( 'wp_ajax_nopriv_iboard_api_insert', array( $api, 'insert' ) );

add_action( 'wp_ajax_iboard_api_update', array( $api, 'update' ) );
add_action( 'wp_ajax_nopriv_iboard_api_update', array( $api, 'update' ) );

add_action( 'wp_ajax_iboard_api_delete', array( $api, 'delete' ) );
add_action( 'wp_ajax_nopriv_iboard_api_delete', array( $api, 'delete' ) );

add_action( 'wp_ajax_iboard_api_page', array( $api, 'getPage' ) );
add_action( 'wp_ajax_nopriv_iboard_api_page', array( $api, 'getPage' ) );