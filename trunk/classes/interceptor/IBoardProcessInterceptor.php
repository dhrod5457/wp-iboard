<?php

/**
 * Created by PhpStorm.
 * User: OKS
 * Date: 2014-12-23
 * Time: 오전 9:25
 */
class IBoardProcessInterceptor {
	const ACTION_NAME = "iboard_process";

	public $action;

	public function __construct() {
		$this->action = @$_REQUEST[ self::ACTION_NAME ];

		if ( ! empty( $this->action ) ) {
			$this->doProcess( $this->action );
		}
	}

	public function doProcess( $action ) {
		try {
			$handler = new IBoardProcessHandler();

			switch ( $action ) {
				case 'insert' :
					$result = $handler->insert( $_POST );

					if ( $result instanceof IBoardProcessError ) {
						iboard_alert( $result->message );
						die;
					}

					wp_redirect( iboard_safe_link( "pageMode=list&pageNo=1" ) );

					break;
				case 'update' :
					$result = $handler->update( $_POST );

					if ( $result instanceof IBoardProcessError ) {
						iboard_alert( $result->message );

					} else {
						$ID = iboard_get_query_var( 'ID' );
						wp_redirect( iboard_safe_link( "pageMode=read&ID={$ID}" ) );
					}

					break;
				case 'delete':
					$ID       = iboard_get_query_var( 'ID' );
					$password = iboard_get_query_var( 'password' );

					$handler->delete( array(
						'ID'       => $ID,
						'password' => $password
					) );

					wp_redirect( iboard_safe_link( "pageMode=list" ) );
					break;
			}
		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}

		die;
	}
}