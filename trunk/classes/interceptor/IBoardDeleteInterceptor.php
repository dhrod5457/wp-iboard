<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 24.
 * Time: 오전 2:30
 */
class IBoardDeleteInterceptor {
	function __construct() {
		add_action( 'iboard_pre_get_view_edit', array( $this, 'delete' ) );
	}

	public function delete() {
		if ( iboard_get_query_var( 'pageMode' ) == 'delete' ) {
			$redirect_url = iboard_process_url( 'delete', iboard_get_query_vars() );
			iboard_redirect( $redirect_url );
			die;
		}
	}
}