<?php

/**
 * Created by PhpStorm.
 * User: OKS
 * Date: 2014-12-22
 * Time: 오전 10:02
 */
class IBoardItemList {
	/* @var IBoardItem[] */
	public $items;

	public $itemCount;

	/* @var IBoardSetting */
	public $setting;

	/* @var IBoardPagination */
	public $pagination;

	public function __construct( $args = null ) {
		if ( ! is_null( $args ) ) {
			$this->setting = $args['boardSetting'];

			$pageNo = isset( $args['pageNo'] ) ? $args['pageNo'] : 1;

			$this->pagination = new IBoardPagination( array(
				'pageNo'          => $pageNo,
				'page_per_record' => $this->setting->list_cnt
			) );

			$args = wp_parse_args( $args, array(
				'offset'   => $this->pagination->start_record,
				'rowCount' => $this->pagination->page_per_record
			) );

			$service         = IBoardItemService::getInstance();
			$this->itemCount = $service->getListCount( $args );
			$this->items     = $service->getList( $args );

			$this->pagination->setTotalRecord( $this->itemCount );
		}
	}

	public function unset_items_password() {
		foreach ( $this->items as &$item ) {
			$item->unset_password();
		}
	}
}