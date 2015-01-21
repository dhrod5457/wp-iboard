<?php

/**
 * Created by PhpStorm.
 * User: OKS
 * Date: 2015-01-09
 * Time: 오후 1:17
 */
class IBoardNaverSyndicationXml {
	public function insert( $ID ) {
		/* @var $iboard_authorizer IBoardAuthorizer */
		global $iboard_authorizer;

		iboard_register_interceptors();
		$item = IBoardItemService::getInstance()->itemFromID( $ID );

		$iboard_authorizer->set_query_var( 'BID', $item->BID );
		$iboard_authorizer->var_init();

		if ( $item->is_secret ) {
			return;
		}

		$result = $iboard_authorizer->getReadRole();

		if ( $result instanceof IBoardAuthorizerResult ) {
			$feed  = new WPNaverSyndicationFeed();
			$entry = new IBoardSyndicationEntry( $item );

			$feed->add_entry( $entry );

			header( 'Content-Type: text/xml; charset=utf-8' );
			echo $feed->asXml();

			die;
		}
	}

	public function delete( $ID ) {
		$item     = new IBoardItem();
		$item->ID = $ID;

		$feed  = new WPNaverSyndicationFeed();
		$entry = new IBoardSyndicationEntry( $item );

		$entry->publish = false;

		$feed->add_entry( $entry );

		header( 'Content-Type: text/xml; charset=utf-8' );
		echo $feed->asXml();

		die;
	}

	public function render() {
		$ID   = iboard_request_param( 'ID' );
		$mode = iboard_request_param( 'mode', 'insert' );

		if ( $mode == 'insert' ) {
			$this->insert( $ID );
		} else if ( $mode == 'delete' ) {
			$this->delete( $ID );
		}
	}
}
