<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 22.
 * Time: 오전 12:26
 */
class IBoardItem {
	public $ID;
	public $BID;
	public $subject;
	public $content;

	public $is_secret;

	/**
	 * @column = varchar (30) default 9999
	 */
	public $is_notice;

	public $file_cnt;
	public $user_id;
	public $user_nm;

	public $user_email;
	public $user_phone;
	public $user_tel;

	public $reg_date;
	public $update_date;
	public $reg_ip;
	public $read_cnt;
	public $password;
	public $grp;
	public $ord;
	public $depth;
	public $parent;

	public $etc0;
	public $etc1;
	public $etc2;
	public $etc3;
	public $etc4;
	public $etc5;
	public $etc6;
	public $etc7;
	public $etc8;
	public $etc9;

	public $category;

	/* @var IBoardFile[] */
	private $fileList;

	private $commentCnt = 0;

	public function isNotice() {
		return $this->is_notice == '1';
	}

	public function prepareInsert() {
		unset( $this->fileList );
		unset( $this->commentCnt );
	}

	/**
	 * @return IBoardFile[]
	 */
	public function getFileList() {
		return $this->fileList;
	}

	public function getFileCnt() {
		return count( $this->getFileList() );
	}

	/**
	 * @param IBoardFile[] $fileList
	 */
	public function setFileList( $fileList ) {
		$this->fileList = $fileList;
	}

	public function hasFile() {
		return $this->getFileCnt() > 0;
	}

	/**
	 * @return mixed
	 */
	public function getCommentCnt() {
		return $this->commentCnt;
	}

	public function isReply() {
		return $this->depth > 0;
	}

	public function hasComment() {
		return $this->getCommentCnt() > 0;
	}

	public function getAvatar( $size = 50 ) {
		return get_avatar( $this->getUserEmail(), $size );
	}

	/**
	 * @param mixed $commentCnt
	 */
	public function setCommentCnt( $commentCnt ) {
		$this->commentCnt = $commentCnt;
	}

	public function getRegDate() {
		$result = date_format( date_create( $this->reg_date ), 'Y-m-d' );
		$result = apply_filters( 'IBoardItem_getRegDate', $result, $this );

		return $result;
	}

	public function getUserNm() {
		$result = apply_filters( 'IBoardItem_getUserNm', $this->user_nm, $this );

		return $result;
	}

	public function getReadCnt() {
		$result = apply_filters( 'IBoardItem_getReadCnt', $this->read_cnt, $this );

		return $result;
	}

	public function getContent() {
		$result = apply_filters( 'the_content', $this->content, $this );
		$result = apply_filters( 'IboardItem_getContent', $result, $this );

		return $result;
	}

	public function getPassword() {
		$result = apply_filters( 'IboardItem_getPassword', $this->password, $this );

		return $result;
	}

	public function getSubject() {
		$result = apply_filters( 'IboardItem_getSubject', $this->subject, $this );

		return $result;
	}

	public function getUserEmail() {
		$result = apply_filters( 'IboardItem_getUserEmail', $this->user_email, $this );

		return $result;
	}

	public function hasEmail() {
		$email = $this->getUserEmail();

		return ! empty( $email );
	}

	public function get_meta_prefix() {
		return 'iboard_item_' . $this->ID . '_';
	}

	public function add_meta( $key, $value = null ) {
		$meta = $this->get_meta( $key, null );

		if ( is_null( $meta ) ) {
			iboard_update_meta( $this->get_meta_prefix() . $key, $value );
		}
	}

	public function get_meta( $key, $defaultValue = null ) {
		return iboard_get_meta( $this->get_meta_prefix() . $key, $defaultValue );
	}

	public function update_meta( $key, $value ) {
		$meta = $this->get_meta( $key, null );

		if ( is_null( $meta ) ) {
			$this->add_meta( $key, $value );
		} else {
			iboard_update_meta( $this->get_meta_prefix() . $key, $value );
		}
	}

	public function delete_meta( $key ) {
		iboard_delete_meta( $this->get_meta_prefix() . $key );
	}

	/**
	 * @return IBoardItem[]
	 */
	public function getNav( $args ) {
		$defaults = array(
			'ID'  => $this->ID,
			'BID' => $this->BID
		);

		$args = wp_parse_args( $args, $defaults );

		return array(
			'prev' => $this->get_prev_item( $args ),
			'next' => $this->get_next_item( $args )
		);
	}

	public function get_prev_item( $args ) {

		return IBoardItemService::getInstance()->getPrevItem( $args );
	}

	public function get_next_item( $args ) {
		return IBoardItemService::getInstance()->getNextItem( $args );
	}

	public function getReadLink( $url = '' ) {
		return iboard_safe_link( "pageMode=read&ID={$this->ID}", $url );
	}

	public function add_meta_field( $key, $value = '' ) {
		$fields = $this->get_meta_fields();

		if ( ! array_key_exists( $key, $fields ) ) {
			$fields[ $key ] = $value;
			$this->update_meta( 'meta_fields', $fields );
		}
	}

	public function update_meta_field( $key, $value ) {
		$fields = $this->get_meta_fields();

		if ( array_key_exists( $key, $fields ) ) {
			$fields[ $key ] = $value;
			$this->update_meta( 'meta_fields', $fields );
		} else {
			$this->add_meta_field( $key, $value );
		}
	}

	public function get_meta_field( $key ) {
		$fields = $this->get_meta_fields();

		if ( array_key_exists( $key, $fields ) ) {
			return $fields[ $key ];
		}
	}

	public function get_meta_fields() {
		return $this->get_meta( 'meta_fields', array() );
	}

	public function update_meta_fields( $args = array() ) {
		foreach ( $this->get_meta_fields() as $key => $value ) {
			$v = iboard_get_array_var( $args, $key );

			$this->update_meta_field( $key, $v );
		}
	}

	public function getParentItem() {
		if ( $this->parent != 0 ) {
			return IBoardItemService::getInstance()->itemFromID( $this->parent );
		}

		return false;
	}

	public function getRootItem() {
		if ( $this->parent == 0 ) {
			return $this;
		}

		return IBoardItemService::getInstance()->getRootItem( $this->grp );
	}

	public function unset_password() {
		unset( $this->password );
	}
}