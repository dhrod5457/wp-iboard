<?php

/**
 * Created by PhpStorm.
 * User: OKS
 * Date: 2014-12-22
 * Time: 오후 6:52
 */
class IBoardItemAuth extends IBoardBaseAuth {
	/* @var IBoardItem */
	public $boardItem;

	public function __construct( $args ) {
		parent::__construct();

		if ( is_null( $args ) ) {
			return;
		}

		$defaults = array(
			'boardItem'    => null,
			'boardSetting' => null
		);

		$args = wp_parse_args( $args, $defaults );

		$this->boardItem    = $args['boardItem'];
		$this->boardSetting = $args['boardSetting'];
	}

	public function authorCheck( $boardItem ) {
		if ( $this->auth->isLogin ) {
			if ( $this->auth->userId == $boardItem->user_id ) {
				return true;
			}
		}

		return false;
	}

	public function isAuthor() {
		return $this->authorCheck( $this->boardItem );
	}

	public function passwordCheck( $item, $args ) {
		if ( $this->boardSetting->isNonMemberEditAble() ) {
			if ( ! isset( $args['password'] ) ) {
				return false;
			}

			if ( $args['password'] == $item->password ) {
				return true;
			}
		}

		return false;
	}

	public function isCheckedSecret( $args ) {
		if ( $this->boardItem->is_secret || $this->boardSetting->isNonMemberEditAble() ) {
			if ( ! isset( $args['password'] ) ) {
				return false;
			}

			if ( $args['password'] == $this->boardItem->password ) {
				return true;
			} else {
				do_action( 'iboard_password_fail', $this );
			}
		}

		return false;
	}

	public function isReadAbleRole() {
		if ( $this->auth->is_board_admin( $this->boardSetting ) ) {
			return true;
		}

		return $this->checkRole( $this->boardSetting->getReadRoles() );
	}

	public function isReadAble( $args = array() ) {
		if ( $this->auth->is_board_admin( $this->boardSetting ) ) {
			return true;
		}

		$check = $this->isReadAbleRole();

		if ( $check ) {
			if ( $this->isAuthor() ) {
				return true;
			}

			if ( $this->boardItem->is_secret && ! $this->isCheckedSecret( $args ) ) {
				$parent = $this->boardItem->getRootItem();

				if ( $this->authorCheck( $parent ) ) {
					return true;
				}

				if ( ! empty( $args['password'] ) ) {
					if ( $args['password'] == $parent->password ) {

						do_action( 'iboard_password_fail', $this );

						return true;
					}
				}

				return false;
			}

			return true;
		}

		return false;
	}

	public function isModifyAble( $args = array() ) {
		if ( $this->auth->is_board_admin( $this->boardSetting ) ) {
			return true;
		}

		$check = $this->isWriteAble( $args );

		if ( $check ) {
			if ( $this->isAuthor() ) {
				return true;
			}

			if ( $this->isCheckedSecret( $args ) ) {
				return true;
			}
		}

		return false;
	}

	public function isWriteAble() {
		if ( $this->auth->is_board_admin( $this->boardSetting ) ) {
			return true;
		}

		if ( is_null( $this->boardSetting ) ) {
			return false;
		}

		$check = $this->checkRole( $this->boardSetting->getWriteRoles() );

		return $check;
	}

	public function isWriteNoticeRole() {
		if ( $this->auth->is_board_admin( $this->boardSetting ) ) {
			return true;
		}

		$check = $this->checkRole( $this->boardSetting->getNoticeWriteRoles() );

		return $check;
	}

	public function nonMemberInsertCheck( $param ) {
		$password = @$param['password'];

		if ( $this->boardSetting->isNonMemberEditAble() ) {
			if ( empty( $password ) ) {
				return false;
			} else {
				if ( $password != $this->boardItem->password ) {
					return false;
				} else {
					return true;
				}
			}
		}

		return false;
	}

	public function isDeleteAble( $args = array() ) {
		if ( $this->auth->is_board_admin( $this->boardSetting ) ) {
			return true;
		}

		return $this->isModifyAble( $args );
	}
}