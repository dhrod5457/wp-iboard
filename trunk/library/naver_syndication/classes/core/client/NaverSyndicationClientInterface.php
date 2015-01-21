<?php
/**
 * Created by PhpStorm.
 * User: oks
 * Date: 15. 1. 11.
 * Time: 오후 7:31
 */

interface NaverSyndicationClientInterface {
	function get( $url, $params = array(), $header = array() );

	function post( $url, $params = array(), $header = array() );
}