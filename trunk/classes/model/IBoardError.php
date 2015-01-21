<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 23.
 * Time: 오전 12:21
 */
class IBoardError {
	public $message;
	public $result;

	function __construct( $message ) {
		$this->message = $message;
		$this->result  = 'error';
	}
}