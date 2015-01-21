<?php

/**
 * Created by PhpStorm.
 * User: OKS
 * Date: 2014-12-22
 * Time: 오후 6:51
 */
abstract class IBoardBaseAuth {
	public $auth;

	/* @var $boardSetting IBoardSetting */
	public $boardSetting;

	public function __construct() {
		$this->auth = new IBoardUser();
	}

	public function hasRole( $role, $boardRoles ) {
		return ! is_bool( array_search( $role, $boardRoles ) );
	}

	public function checkRole( $boardRoles ) {
		if ( is_super_admin() ) {
			return true;
		}

		if ( $this->hasRole( 'all', $boardRoles ) ) {
			return true;
		} else if ( $this->hasRole( 'isLogin', $boardRoles ) ) {
			if ( $this->auth->isLogin ) {
				return true;
			}
		} else {
			if ( is_null( $this->auth->roles ) ) {
				return false;
			}

			foreach ( $this->auth->roles as $role ) {
				if ( $this->hasRole( $role, $boardRoles ) ) {
					return true;
				}
			}
		}

		return false;
	}
}
