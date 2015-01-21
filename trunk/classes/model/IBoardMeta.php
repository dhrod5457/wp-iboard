<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 31.
 * Time: 오전 12:54
 */
class IBoardMeta {
	public $ID;
	public $meta_key;
	public $meta_value;
	public $reg_date;
	public $update_date;

	public function __construct( $key = null, $value = null ) {
		$this->meta_key   = $key;
		$this->meta_value = $value;
	}
}