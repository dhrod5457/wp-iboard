<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 15. 1. 7.
 * Time: 오전 12:07
 */
class IBoardAuthorizerResult {
	public $result = true;
	public $objects;

	public function __construct( $objects ) {
		$this->objects = $objects;
	}
}

class IBoardAuthorizerError {
	const ID_REQUIRED        = 'ID_REQUIRED';
	const BID_REQUIRED       = 'BID_REQUIRED';
	const PASSWORD_REQUIRED  = 'PASSWORD_REQUIRED';
	const PASSWORD_INCORRECT = 'PASSWORD_INCORRECT';
	const PERMISSION_ERROR   = 'PERMISSION_ERROR';
	const NOT_EXISTS_ITEM    = 'NOT_EXISTS_ITEM';

	public $result = false;
	public $message;

	public function __construct( $message ) {
		$this->message = $message;
	}
}

class IBoardAuthorizer {
	/* @var IBoardUser */
	public $user;

	/* @var IBoardItemService */
	public $boardService;

	/* @var IBoardSettingService */
	public $settingService;

	public $query_vars = array(
		'ID'       => '',
		'BID'      => '',
		'password' => ''
	);

	/* @var IBoardSetting */
	public $boardSetting;

	public function __construct() {
		$this->query_init();

		$this->var_init();
	}

	public function var_init() {
		$this->settingService = IBoardSettingService::getInstance();
		$this->boardService   = IBoardItemService::getInstance();

		$this->user = new IBoardUser();

		if ( ! $this->is_null_query_var( 'BID' ) ) {
			$this->boardSetting = $this->settingService->settingFromBID( $this->get_query_var( 'BID' ) );
		}
	}

	public function set_query_var( $key, $value ) {
		$this->query_vars[ $key ] = $value;
	}

	public function query_init() {
		foreach ( array_keys( $this->query_vars ) as $key ) {
			$this->query_vars[ $key ] = iboard_request_param( $key, null );
		}
	}

	public function get_query_var( $key ) {
		if ( ! array_key_exists( $key, $this->query_vars ) ) {
			return null;
		}

		return $this->query_vars[ $key ];
	}

	public function is_null_query_var( $var_name ) {
		return is_null( $this->get_query_var( $var_name ) );
	}

	public function getListRoles() {
		if ( $this->is_null_query_var( 'BID' ) ) {
			return new IBoardAuthorizerError( IBoardAuthorizerError::BID_REQUIRED );
		}

		$boardList = new IBoardItemList( array(
			'boardSetting' => $this->boardSetting,
			'BID'          => $this->get_query_var( 'BID' ),
			'pageNo'       => iboard_request_param( 'pageNo', 1 )
		) );

		$auth = new IBoardListAuth( $boardList );

		$objects = array(
			'list'  => $auth->isReadAble(),
			'write' => $auth->isWriteAble()
		);

		if ( $auth->isReadAble() ) {
			$objects['listItem'] = $auth->boardList;
		}

		return new IBoardAuthorizerResult( $objects );
	}

	public function getDeleteRole() {
		if ( $this->is_null_query_var( 'ID' ) ) {
			return new IBoardAuthorizerError( IBoardAuthorizerError::ID_REQUIRED );
		}

		$boardItem = $this->boardService->itemFromID( $this->get_query_var( 'ID' ) );

		$auth = new IBoardItemAuth( array(
			'boardItem'    => $boardItem,
			'boardSetting' => $this->boardSetting
		) );

		if ( $auth->isDeleteAble( array( 'password' => $this->get_query_var( 'password' ) ) ) ) {
			return new IBoardAuthorizerResult( $boardItem );
		} else {
			return new IBoardAuthorizerError( IBoardAuthorizerError::PERMISSION_ERROR );
		}
	}

	public function getUpdateRole() {
		if ( $this->is_null_query_var( 'ID' ) ) {
			return new IBoardAuthorizerError( IBoardAuthorizerError::ID_REQUIRED );
		}

		$boardItem = $this->boardService->itemFromID( $this->get_query_var( 'ID' ) );

		$auth = new IBoardItemAuth( array(
			'boardItem'    => $boardItem,
			'boardSetting' => $this->boardSetting
		) );

		$param    = array( 'password' => $this->get_query_var( 'password' ) );
		$password = $param['password'];

		if ( ! $auth->isWriteAble() ) {
			return new IBoardAuthorizerError( IBoardAuthorizerError::PERMISSION_ERROR );
		}

		if ( $boardItem->is_secret ) {
			if ( $auth->isModifyAble( $param ) ) {
				return new IBoardAuthorizerResult( $boardItem );
			} else {
				if ( empty( $param['password'] ) ) {
					return new IBoardAuthorizerError( IBoardAuthorizerError::PASSWORD_REQUIRED );
				}

				return new IBoardAuthorizerError( IBoardAuthorizerError::PASSWORD_INCORRECT );
			}
		} else {
			if ( $this->boardSetting->isNonMemberEditAble() && ! $auth->auth->isLogin ) {
				if ( empty( $password ) ) {
					return new IBoardAuthorizerError( IBoardAuthorizerError::PASSWORD_REQUIRED );
				} else {
					if ( $password != $boardItem->password ) {
						return new IBoardAuthorizerError( IBoardAuthorizerError::PASSWORD_INCORRECT );
					} else {
						return new IBoardAuthorizerResult( $boardItem );
					}
				}
			} else {
				if ( $auth->isModifyAble( $param ) ) {
					return new IBoardAuthorizerResult( $boardItem );
				} else {
					return new IBoardAuthorizerError( IBoardAuthorizerError::PASSWORD_INCORRECT );
				}
			}
		}
	}

	public function getReadRole() {
		if ( $this->is_null_query_var( 'ID' ) ) {
			return new IBoardAuthorizerError( IBoardAuthorizerError::ID_REQUIRED );
		}

		$boardItem = $this->boardService->itemFromID( $this->get_query_var( 'ID' ) );

		if ( is_null( $boardItem ) ) {
			return new IBoardAuthorizerError( IBoardAuthorizerError::NOT_EXISTS_ITEM );
		}

		$auth = new IBoardItemAuth( array(
			'boardItem'    => $boardItem,
			'boardSetting' => $this->boardSetting
		) );

		if ( ! $auth->isReadAbleRole() ) {
			return new IBoardAuthorizerError( IBoardAuthorizerError::PERMISSION_ERROR );
		}

		if ( $boardItem->is_secret ) {
			if ( $auth->isReadAble( array( 'password' => $this->get_query_var( 'password' ) ) ) ) {
				return new IBoardAuthorizerResult( $boardItem );
			} else {
				return new IBoardAuthorizerError( IBoardAuthorizerError::PASSWORD_INCORRECT );
			}
		} else {
			unset( $boardItem->password );

			return new IBoardAuthorizerResult( $boardItem );
		}
	}

	public function getInsertRole() {
		if ( $this->is_null_query_var( 'BID' ) ) {
			return new IBoardAuthorizerError( IBoardAuthorizerError::BID_REQUIRED );
		}

		$auth = new IBoardItemAuth( array(
			'boardSetting' => $this->boardSetting
		) );

		if ( ! $auth->isWriteAble() ) {
			return new IBoardAuthorizerError( IBoardAuthorizerError::PERMISSION_ERROR );
		} else {
			return new IBoardAuthorizerResult( true );
		}
	}

	public function getNoticeRole() {
		if ( $this->is_null_query_var( 'BID' ) ) {
			return new IBoardAuthorizerError( IBoardAuthorizerError::BID_REQUIRED );
		}

		$auth = new IBoardItemAuth( array( 'boardSetting' => $this->boardSetting ) );

		if ( $auth->isWriteNoticeRole() ) {
			return new IBoardAuthorizerResult( true );
		}

		return new IBoardAuthorizerError( IBoardAuthorizerError::PERMISSION_ERROR );
	}
}

function iboard_has_authorize_error( $object ) {
	$result = $object instanceof IBoardAuthorizerError;

	if ( $result ) {
		return $object;
	} else {
		return false;
	}
}