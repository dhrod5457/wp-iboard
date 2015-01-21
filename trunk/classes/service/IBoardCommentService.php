<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 15. 1. 4.
 * Time: 오후 3:58
 */
class IBoardCommentService extends IBoardBaseService {
	private static $instance;

	public static function getInstance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new IBoardCommentService();
		}

		return self::$instance;
	}

	public function getServiceName() {
		return 'iboard_comment';
	}

	/**
	 * @return IBoardComment[]
	 */
	public function getCommentList( $args ) {
		$result = array();

		$query = "select * from {$this->tableName} where itemID='{$args['itemID']}' order by ID DESC ";

		$rs = $this->db->wpdb->get_results( $query, ARRAY_A );

		foreach ( $rs as $comment ) {
			$result[] = $this->commentFromArray( $comment );
		}

		return $result;
	}

	/**
	 * @param $ID
	 *
	 * @return IBoardComment
	 */
	public function commentFromID( $ID ) {
		return $this->fromID( 'IBoardComment', $ID );
	}

	/**
	 * @param array $args
	 *
	 * @return IBoardComment
	 */
	public function commentFromArray( $args ) {
		return $this->fromArray( 'IBoardComment', $args );
	}
}