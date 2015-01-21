<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 15. 1. 11.
 * Time: 오후 7:33
 */
class WPNaverSyndicationFeed extends NaverSyndicationFeed {
	public function getId() {
		return urlencode( get_home_url() );
	}

	public function getTitle() {
		return get_bloginfo( 'name' );
	}

	public function getAuthor( $name_or_email ) {
		$admin = get_userdata( 1 );

		if ( $name_or_email == 'email' ) {
			return $admin->get( 'user_email' );
		} else if ( $name_or_email == 'name' ) {
			return $admin->get( 'display_name' );
		}
	}

	public function getLink( $title_or_href ) {
		if ( $title_or_href == 'href' ) {
			return urlencode( get_home_url() );
		} else if ( $title_or_href == 'title' ) {
			return get_bloginfo( 'name' );
		}
	}
}