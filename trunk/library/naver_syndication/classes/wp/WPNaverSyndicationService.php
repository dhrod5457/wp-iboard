<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 15. 1. 11.
 * Time: 오후 7:42
 */
class WPNaverSyndicationService implements NaverSyndicationServiceInterface {
	const OPTION_NAME = 'syndication_results';

	const OPTION_SEQ = "syndication_results_seq";

	private $results;

	public function __construct() {
		$this->results = get_option( self::OPTION_NAME, array() );
	}

	public function insertSyndication( $result ) {
		if ( $result['error_code'] == '000' ) {
			$this->results['success']["{$this->generateSequence()}"] = $result;
		} else {
			$this->results["error"]["{$this->generateSequence()}"] = $result;
		}

		$this->commit();
	}

	public function getSyndicationResultList() {
		return $this->results;
	}

	public function deleteSyndication( $seq ) {
		unset( $this->results['success'][ $seq ] );
		unset( $this->results['error'][ $seq ] );

		$this->commit();
	}

	public function commit() {
		update_option( self::OPTION_NAME, $this->results );
	}

	private function generateSequence() {
		$seq = get_option( self::OPTION_SEQ, 0 );
		$seq = $seq + 1;

		update_option( self::OPTION_SEQ, $seq );

		return $seq;
	}
}