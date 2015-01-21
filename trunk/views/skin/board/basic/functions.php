<?php
function iboard_basic_skin_init( $page ) {
	if ( $page instanceof IBoardBasePage ) {

	}
}

add_action( 'iboard_page_init', 'iboard_basic_skin_init' );