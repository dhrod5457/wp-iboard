<?php

/**
 * Created by PhpStorm.
 * User: OKS
 * Date: 2014-12-22
 * Time: 오후 4:09
 */
class IBoardUser {
	public $userId;
	public $userMail;
	public $userName;
	public $userLogin;
	public $isLogin;
	public $roles;

	public function __construct() {
		$user = new WP_User( get_current_user_id() );

		$this->isLogin = $user->exists();

		if ( $this->isLogin ) {
			$this->userId    = $user->ID;
			$this->userMail  = $user->get( "user_email" );
			$this->userName  = $user->get( "display_name" );
			$this->userLogin = $user->get( "user_login" );
			$this->roles     = $user->roles;
		}
	}

	/**
	 * @param $boardSetting IBoardSetting
	 *
	 * @return bool
	 */
	public function is_board_admin( $boardSetting ) {
		if ( ! $boardSetting instanceof IBoardSetting ) {
			return false;
		}

		if ( is_super_admin() ) {
			return true;
		}

		$users = $boardSetting->getAdminUsers();

		return in_array( $this->userLogin, $users );
	}
}