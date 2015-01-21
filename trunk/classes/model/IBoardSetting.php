<?php

/**
 * Created by PhpStorm.
 * User: OKS
 * Date: 2014-12-22
 * Time: 오후 4:13
 */
class IBoardSetting {
	const ROLE_ALL = 'all';
	const ROLE_IS_LOGIN = 'isLogin';

	public $ID;
	public $BID;
	public $title;

	public $write_role;
	public $read_role;
	public $down_role;
	public $list_role;
	public $notice_role;

	public $comment_write_role;

	public $reg_date;
	public $skin;
	public $skin_latest;
	public $editor;
	public $base_content;
	public $banned_words;

	public $use_field_names;

	public $list_cnt = 10;
	public $file_cnt = 2;

	public $use_notification;
	public $use_captcha;

	public $admin_users;

	public $categories;

	public $use_category_appear;

	public $is_only_secret;

	public $is_use_skin_css;

	public $is_use_notice_comment;

	public function getCategories() {
		return $this->commaToArray( $this->categories );
	}

	public function getAdminUsers() {
		return $this->commaToArray( $this->admin_users );
	}

	public function getReadRoles() {
		return $this->commaToArray( $this->read_role );
	}

	public function getWriteRoles() {
		return $this->commaToArray( $this->write_role );
	}

	public function getDownRoles() {
		return $this->commaToArray( $this->down_role );
	}

	public function getListRoles() {
		return $this->commaToArray( $this->list_role );
	}

	public function getNoticeWriteRoles() {
		return $this->commaToArray( $this->notice_role );
	}

	public function getCommentWriteRoles() {
		return $this->commaToArray( $this->comment_write_role );
	}

	public function getUseFieldNames() {
		return $this->commaToArray( $this->use_field_names );
	}

	public function hasCategory() {
		return count( $this->getCategories() ) > 0;
	}

	public function isUseField( $fieldName ) {
		return in_array( $fieldName, $this->getUseFieldNames() );
	}

	public function commaToArray( $str ) {
		if ( empty( $str ) ) {
			return array();
		}

		return explode( ',', $str );
	}

	public function isUseSkinCss() {
		return $this->is_use_skin_css == 'Y' || is_null( $this->is_use_skin_css );
	}

	public function isOnlySecret() {
		return $this->is_only_secret == 'Y';
	}

	public function isUseCaptcha() {
		return $this->use_captcha == 'Y' && ! is_super_admin();
	}

	public function getBannedWords() {
		if ( empty( $this->banned_words ) ) {
			return iboard_get_banned_words();
		}

		return $this->banned_words;
	}

	public function isNonMemberReadAble() {
		return $this->hasRole( self::ROLE_ALL, $this->getReadRoles() );
	}

	public function isNonMemberEditAble() {
		return $this->hasRole( self::ROLE_ALL, $this->getWriteRoles() );
	}

	public function isNonMemberCommentAble() {
		return $this->hasRole( self::ROLE_ALL, $this->getCommentWriteRoles() );
	}

	public function isUseNoticeComment() {
		return $this->is_use_notice_comment == 'Y';
	}

	public function hasRole( $role, $boardRoles ) {
		return ! is_bool( array_search( $role, $boardRoles ) );
	}

	public function add_meta( $key, $value ) {
		if ( is_null( $this->get_meta( $key ) ) ) {
			iboard_update_meta( "iboard_setting_meta_{$this->ID}_{$key}", $value );
		}
	}

	public function get_meta( $key, $defaultValue = null ) {
		$option = iboard_get_meta( "iboard_setting_meta_{$this->ID}_{$key}", $defaultValue );

		return $option;
	}

	public function update_meta( $key, $value ) {
		if ( is_null( $this->get_meta( $key ) ) ) {
			$this->add_meta( $key, $value );
		} else {
			iboard_update_meta( "iboard_setting_meta_{$this->ID}_{$key}", $value );
		}
	}

	public function delete_meta( $key ) {
		iboard_delete_meta( "iboard_setting_meta_{$this->ID}_{$key}" );
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
}