<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 23.
 * Time: 오전 12:25
 */
abstract class IBoardBaseInterceptor {
	abstract function service();

	abstract function add_filters();

	function __construct() {
		$this->filterPrefix = $this->generatePrefix();
		$this->add_filters();
	}

	function add_filter( $name, $funcName ) {
		add_filter( $this->filterPrefix . $name, array( $this, $funcName ) );
	}

	function generatePrefix() {
		if ( is_object( $this->service() ) && $this->service() instanceof IBoardBaseService ) {
			return $this->service()->getServiceName();
		} else if ( is_string( $this->service() ) ) {
			return $this->service();
		}

		return false;
	}
} 