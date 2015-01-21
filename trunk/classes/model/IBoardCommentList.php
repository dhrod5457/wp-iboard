<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 15. 1. 5.
 * Time: 오전 1:15
 */
class IBoardCommentList {
	/* @var IBoardComment[] */
	public $comments;

	public $setting;

	public function __construct( $args = null ) {
		if ( ! is_null( $args ) ) {
			$this->setting = $args['setting'];

			$service = IBoardCommentService::getInstance();

			$this->comments = $service->getCommentList( $args );

			foreach ( $this->comments as $comment ) {
				$comment->auth = new IBoardCommentAuth( $comment, $this->setting );
			}
		}
	}
}