<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 23.
 * Time: 오전 1:24
 */
class IBoardPagination {
	var $pageNo;
	var $page_per_record;
	var $block_per_page;
	var $now_block;
	var $prev_block;
	var $next_block;
	var $start_page;
	var $start_record;
	var $total_record;
	var $total_page;
	var $total_block;
	var $end_page;
	var $start_no;

	public function __construct( $args = array() ) {
		$default = array( 'page_per_record' => 10, 'block_per_page' => 5 );

		$args = wp_parse_args( $args, $default );

		$pageNo = $args['pageNo'];

		if ( ! $pageNo || $pageNo < 0 ) {
			$pageNo = 1;
		}

		$this->pageNo          = $pageNo;
		$this->page_per_record = $args['page_per_record']; //노출되는 줄의 수
		$this->block_per_page  = $args['block_per_page'];  //한 블럭당 표시될수 있는 페이징 번호의수

		$this->now_block    = ceil( $pageNo / $this->block_per_page );
		$this->prev_block   = ceil( ( ( $this->now_block - 1 ) * $this->block_per_page ) - ( $this->block_per_page - 1 ) );
		$this->next_block   = ceil( ( ( $this->now_block + 1 ) * $this->block_per_page ) - ( $this->block_per_page - 1 ) );
		$this->start_page   = ( ( $this->now_block - 1 ) * $this->block_per_page ) + 1;
		$this->start_record = ( ( $this->pageNo - 1 ) * $this->page_per_record );
	}

	public function setTotalRecord( $total_record ) {
		$this->total_record = $total_record;
		$this->total_page   = ceil( $this->total_record / $this->page_per_record );
		$this->total_block  = ceil( $this->total_page / $this->block_per_page );
		$this->end_page     = ( ( $this->start_page + $this->block_per_page ) <= $this->total_page ) ?
			( $this->start_page + $this->block_per_page ) : $this->total_page;
		$this->start_no     = $this->getListStartNo();
	}

	public function hasNextBlock() {
		return $this->now_block < $this->total_block;
	}

	public function hasPrevBlock() {
		return $this->now_block > 1;
	}

	public function getListStartNo() {
		return $this->total_record - ( ( $this->pageNo - 1 ) * $this->page_per_record );
	}
}