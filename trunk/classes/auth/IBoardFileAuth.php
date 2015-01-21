<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 28.
 * Time: 오후 3:37
 */
class IBoardFileAuth extends IBoardBaseAuth {
	public function __construct( $args ) {
		parent::__construct();

		$this->boardItem    = $args['boardItem'];
		$this->boardSetting = $args['boardSetting'];
	}

	public function isDownAble() {
		return $this->checkRole( $this->boardSetting->getDownRoles() );
	}
}