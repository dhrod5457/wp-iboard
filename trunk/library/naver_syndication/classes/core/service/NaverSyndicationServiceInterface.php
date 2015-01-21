<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 15. 1. 11.
 * Time: 오후 7:36
 */
interface NaverSyndicationServiceInterface {
	public function insertSyndication( $result );

	public function deleteSyndication( $seq );

	public function getSyndicationResultList();
}