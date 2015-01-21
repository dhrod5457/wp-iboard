<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 15. 1. 5.
 * Time: 오전 1:38
 */
class IBoardCommentProcessInterceptor {
	const ACTION_NAME = "iboard_comment_process";

	public $action;

	public function __construct() {
		$this->action = @$_REQUEST[ self::ACTION_NAME ];

		if ( ! empty( $this->action ) ) {
			$this->doProcess();
		}
	}

	public function doProcess() {
		$commentService = IBoardCommentService::getInstance();

		switch ( $this->action ):
			case 'insert' :
				$comment = $commentService->commentFromArray( $_POST );
				$commentService->insert( $comment );
				wp_redirect( iboard_safe_link( "pageMode=read&ID={$comment->itemID}" ) );

				break;
			case 'delete':
				$ID = iboard_request_param( 'itemID' );
				$commentService->delete( array( 'ID' => iboard_request_param( 'ID' ) ) );
				wp_redirect( iboard_safe_link( "pageMode=read&ID={$ID}" ) );
				break;
		endswitch;


		die;
	}
} 