<?php
/**
 * Created by PhpStorm.
 * User: oks
 * Date: 15. 1. 11.
 * Time: 오후 7:32
 */

class DefaultNaverSyndicationClient implements NaverSyndicationClientInterface {
	function get( $url, $params = array(), $header = array() ) {
		if ( ! empty( $params ) ) {
			if ( strpos( $url, '?' ) === false ) {
				$url .= '?';
			}
			$url .= http_build_query( $params );
		}
		$curl = $this->getDefaultCurl( $header );
		curl_setopt( $curl, CURLOPT_URL, $url );
		$result = curl_exec( $curl );
		curl_close( $curl );

		return $result;
	}

	function post( $url, $params = array(), $header = array() ) {
		$curl = $this->getDefaultCurl( $header );

		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query( $params, '', '&' ) );
		$result = curl_exec( $curl );
		curl_close( $curl );

		return $result;
	}

	function getDefaultCurl( $header = array() ) {
		$curl = curl_init();

		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curl, CURLOPT_HEADER, false );
		curl_setopt( $curl, CURLINFO_HEADER_OUT, false );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLINFO_HEADER_OUT, true );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );

		if ( ! is_null( $header ) ) {
			curl_setopt( $curl, CURLOPT_HTTPHEADER, $header );
		}

		return $curl;
	}
}