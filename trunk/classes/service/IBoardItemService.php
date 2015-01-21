<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 22.
 * Time: 오전 12:26
 */
class IBoardItemService extends IBoardBaseService {
	public function getServiceName() {
		return 'iboard_item';
	}

	private static $instance;

	public static function getInstance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new IBoardItemService();
		}

		return self::$instance;
	}

	/**
	 * @param $ID
	 *
	 * @return IBoardItem
	 */
	public function itemFromID( $ID ) {
		$boardItem = $this->fromID( 'IBoardItem', $ID );
		$boardItem = apply_filters( $this->getServiceName() . 'getItem', $boardItem );

		return $boardItem;
	}

	public function getRootItem( $grp ) {
		$query    = "select * from {$this->tableName} where grp={$grp} and depth=0";
		$rootItem = $this->db->wpdb->get_row( $query, ARRAY_A );
		$rootItem = $this->itemFromArray( $rootItem );
		$rootItem = apply_filters( $this->getServiceName() . 'getItem', $rootItem );

		return $rootItem;
	}

	/**
	 * @param array $args
	 *
	 * @return IBoardItem
	 */
	public function itemFromArray( $args ) {
		return $this->fromArray( 'IBoardItem', $args );
	}

	public function replyInsert( IBoardItem $item, $parentID ) {
		$parentItem = $this->itemFromID( $parentID );

		$item->ord   = $parentItem->ord + 1;
		$item->depth = $parentItem->depth + 1;
		$item->grp   = $parentItem->grp;

		$query = "update {$this->tableName} set ord=ord+1 where grp={$parentItem->grp} and ord > {$parentItem->ord}";
		$query = apply_filters( 'iboard_item_reply_insert_pre', $query, $item, $parentItem );

		$this->db->wpdb->query( $query );

		$item->parent = $parentID;
		$item->prepareInsert();

		$result = $this->insert( $item );

		$item->ID = $this->db->wpdb->insert_id;

		return $item->ID;
	}

	public function generateGrp() {
		$query = "SELECT ifnull(MIN(grp),0)-1 FROM {$this->tableName}";

		return $this->db->wpdb->get_var( $query );
	}

	/**
	 * @param array $args
	 *
	 * @return IBoardItem[]
	 */
	public function getList( array $args = array() ) {
		$defaults = array(
			'offset'   => 0,
			'rowCount' => 10,
			'orderBy'  => array( 'is_notice asc', 'grp asc', 'ord asc' )
		);

		$args = wp_parse_args( $args, $defaults );

		$q = $this->generateListQueryItem( "select * from {$this->tableName} where 1=1 ", $args );

		$orderBy = apply_filters( 'iboard_item_list_order_by', $args['orderBy'] );

		$q['query'] .= " ORDER BY " . implode( ",", $orderBy );
		$q['query'] .= "  LIMIT %d,%d  ";

		$q['param'][] = $args['offset'];
		$q['param'][] = $args['rowCount'];

		$q = apply_filters( "iboard_get_list_query_pre", $q );

		$items = $this->db->wpdb->get_results( $this->db->wpdb->prepare( $q['query'], $q['param'] ), ARRAY_A );

		$result = array();

		foreach ( $items as $item ) {
			$boardItem = $this->itemFromArray( $item );
			$boardItem = apply_filters( $this->getServiceName() . 'getItem', $boardItem );
			$result[]  = $boardItem;
		}

		return $result;
	}

	public function getListCount( array $args = array() ) {
		$q = $this->generateListQueryItem( "select count(*) cnt from {$this->tableName} where 1=1 ", $args );
		$q = apply_filters( "iboard_get_list_count_pre", $q );

		return empty( $q['param'] ) ? $this->db->wpdb->get_var( $q['query'] ) :
			$this->db->wpdb->get_var( $this->db->wpdb->prepare( $q['query'], $q['param'] ) );
	}

	public function getCommentCount( $ID ) {
		/* @var $iboard IBoard */
		global $iboard;

		$q = "select count(*) from {$iboard->commentTable} where itemID={$ID}";

		return $this->db->wpdb->get_var( $q );
	}

	public function generateListQueryItem( $query, $args ) {
		$where = array();

		if ( isset( $args['subject'] ) ) {
			$where[] = array( "and subject like %s", '%' . $this->db->wpdb->esc_like( $args['subject'] ) . '%' );
		}

		if ( isset( $args['content'] ) ) {
			$where[] = array( "and content like %s", '%' . $this->db->wpdb->esc_like( $args['content'] ) . '%' );
		}

		if ( isset( $args['user_nm'] ) ) {
			$where[] = array( "and user_nm like %s", '%' . $this->db->wpdb->esc_like( $args['user_nm'] ) . '%' );
		}

		if ( isset( $args['is_secret'] ) ) {
			$where[] = array( "and is_secret=%s", $args['is_secret'] );
		}

		if ( isset( $args['start_date'] ) ) {
			$where[] = array( "and reg_date >= %s", $args['start_date'] );
		}

		if ( isset( $args['end_date'] ) ) {
			$where[] = array( "and reg_date <= %s", $args['end_date'] );
		}

		if ( ! empty( $args['category'] ) ) {
			$where[] = array( "and (category = %s or is_notice=1)", $args['category'] );
		}

		if ( ! empty( $args['BID'] ) || ! is_super_admin() ) {
			$where[] = array( "and BID=%s", $args['BID'] );
		}

		$queryList = array();
		$paramList = array();

		foreach ( $where as $value ) {
			$queryList[] = $value[0];
			$paramList[] = $value[1];
		}

		$query .= " " . implode( " ", $queryList );

		return array(
			'query' => $query,
			'param' => $paramList
		);
	}

	public function updateReadCount( $boardItem, $BID ) {
		$key = 'iboard_read_key_' . $boardItem->BID . $boardItem->ID;

		if ( ! @in_array( $key, $_SESSION['iboard_read_keys'] ) ) {
			@$_SESSION['iboard_read_keys'][] = $key;

			$updateCnt = intval( $boardItem->read_cnt ) + 1;

			$this->update( array(
				'BID'      => $BID,
				'ID'       => $boardItem->ID,
				'read_cnt' => $updateCnt,
				'filter'   => false
			) );
		}
	}

	public function getPrevItem( $args ) {
		$ID       = $args['ID'];
		$BID      = $args['BID'];
		$category = $args['category'];

		$query = "SELECT * FROM {$this->tableName} WHERE
					ID = (
						SELECT
							max(ID)
						FROM
							{$this->tableName}
						WHERE
							ID < {$ID}";

		if ( ! empty( $category ) ) {
			$query .= "  AND category='{$category}'  ";
		}

		$query .= " ) AND BID = '{$BID}'";

		$result = $this->db->wpdb->get_row( $query, ARRAY_A );

		return $this->itemFromArray( $result );
	}

	public function getNextItem( $args ) {
		$ID       = $args['ID'];
		$BID      = $args['BID'];
		$category = $args['category'];

		$query = "SELECT * FROM {$this->tableName} WHERE
					ID = (
						SELECT
							min(ID)
						FROM
							{$this->tableName}
						WHERE
							ID > {$ID}";

		if ( ! empty( $category ) ) {
			$query .= "  AND category='{$category}'  ";
		}

		$query .= " ) AND BID = '{$BID}'";

		$result = $this->db->wpdb->get_row( $query, ARRAY_A );

		return $this->itemFromArray( $result );
	}
}