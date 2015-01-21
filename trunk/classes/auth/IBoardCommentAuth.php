<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 15. 1. 4.
 * Time: 오후 5:03
 */
class IBoardCommentAuth extends IBoardBaseAuth {
	/* @var IBoardComment */
	public $comment;

	/* @var IBoardSetting */
	public $boardSetting;

	public function __construct( $comment, $boardSetting ) {
		parent::__construct();

		$this->comment      = $comment;
		$this->boardSetting = $boardSetting;
	}

	public function isCheckedSecret( $args ) {
		if ( $this->boardSetting->isNonMemberEditAble() ) {
			if ( ! isset( $args['password'] ) ) {
				return false;
			}

			if ( $args['password'] == $this->comment->password ) {
				return true;
			}
		}

		return false;
	}

	public function isAuthor() {
		if ( $this->auth->isLogin ) {
			if ( $this->auth->userId == $this->comment->user_id ) {
				return true;
			}
		}

		return false;
	}

	public function isCommentWriteAble() {
		if ( $this->auth->is_board_admin( $this->boardSetting ) ) {
			return true;
		}

		if ( $this->checkRole( $this->boardSetting->getCommentWriteRoles() ) ) {
			return true;
		}

		return false;
	}

	public function isModifyAble( $password = null ) {
		if ( $this->auth->is_board_admin( $this->boardSetting ) ) {
			return true;
		}

		if ( ! $this->isCommentWriteAble() ) {
			return false;
		}

		if ( $this->isAuthor() ) {
			return true;
		}

		if ( is_null( $password ) ) {
			return false;
		}

		if ( $this->isCheckedSecret( array( 'password' => $password ) ) ) {
			return true;
		}

		return false;
	}
}