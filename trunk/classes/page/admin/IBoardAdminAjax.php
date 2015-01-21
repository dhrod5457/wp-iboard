<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 22.
 * Time: 오후 11:03
 */
class IBoardAdminAjax {
	private $service;

	public function __construct() {
		$this->service = IBoardSettingService::getInstance();

		add_action( 'wp_ajax_iboard_setting_ajax', array( $this, 'process' ) );
	}

	public function process() {
		header( "Content-type: application/json; charset=utf-8" );

		$result = array();

		$m   = iboard_request_param( 'm' );
		$BID = iboard_request_param( 'BID' );

		if ( empty( $BID ) ) {
			$result['result']  = 'error';
			$result['message'] = '게시판 ID는 필수입력입니다.';

			echo json_encode( $result );
			die;
		}

		$param = $this->service->fromArray( 'IBoardSetting', $_REQUEST );
		$param = (array) $param;

		switch ( $m ) {
			case 'insert' :
				$param['reg_date'] = date( 'Y-m-d H:i:s' );

				$r = $this->service->insert( $param );

				if ( iboard_is_error( $r ) ) {
					echo json_encode( (array) $r );
					die;
				}

				break;
			case 'delete' :
				$r = $this->service->delete( $param );

				if ( iboard_is_error( $r ) ) {
					echo json_encode( (array) $r );
					die;
				}

				break;
			case 'update':
				$r = $this->service->update( $param );

				if ( iboard_is_error( $r ) ) {
					echo json_encode( (array) $r );
					die;
				}

				break;
		}

		$result['result'] = 'success';

		echo json_encode( $result );

		die;
	}
}