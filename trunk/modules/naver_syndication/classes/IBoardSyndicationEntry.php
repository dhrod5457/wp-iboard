<?php

/**
 * Created by PhpStorm.
 * User: OKS
 * Date: 2015-01-09
 * Time: 오후 1:16
 */
class IBoardSyndicationEntry extends NaverSyndicationEntry {
	public $item;

	public function __construct( IBoardItem $item ) {
		$this->item = $item;
	}

	public function getId() {
		return urlencode( $this->item->get_meta( 'permalink' ) . "?pageMode=read&ID={$this->ID}" );
	}

	public function getTitle() {
		return $this->item->subject;
	}

	public function getAuthor( $name_or_email ) {
		if ( $name_or_email == 'name' ) {
			return $this->item->user_nm;
		} else if ( $name_or_email == 'email' ) {
			return $this->item->user_email;
		}
	}

	public function getLink( $title_or_href ) {
		if ( $title_or_href == 'title' ) {
			return $this->item->subject;
		} else if ( $title_or_href == 'href' ) {
			return urlencode( $this->item->get_meta( 'permalink', get_home_url() ) );
		}
	}

	public function getContent() {
		return $this->item->content;
	}
}
