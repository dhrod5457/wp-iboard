<?php

/**
 * Created by PhpStorm.
 * User: OKS
 * Date: 2014-12-22
 * Time: 오후 6:51
 */
class IBoardListAuth extends IBoardBaseAuth {
	/* @var IBoardItemList */
	public $boardList;

	public function __construct( $boardList ) {
		parent::__construct();

		$this->boardList    = $boardList;
		$this->boardSetting = $this->boardList->setting;
	}

	public function isReadAble() {
		if ( $this->auth->is_board_admin( $this->boardSetting ) ) {
			return true;
		}

		$boardRoles = $this->boardSetting->getListRoles();

		return $this->checkRole( $boardRoles );
	}

	public function isWriteAble() {
		if ( $this->auth->is_board_admin( $this->boardSetting ) ) {
			return true;
		}

		$boardRoles = $this->boardSetting->getWriteRoles();

		return $this->checkRole( $boardRoles );
	}
}