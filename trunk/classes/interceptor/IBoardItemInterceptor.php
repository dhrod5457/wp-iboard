<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 23.
 * Time: 오전 12:43
 */
class IBoardItemInterceptor extends IBoardBaseInterceptor {
	function service() {
		return IBoardItemService::getInstance();
	}

	function add_filters() {
		$this->add_filter( 'insert', 'insertOrUpdate' );
		$this->add_filter( 'update', 'insertOrUpdate' );

		$this->add_filter( 'insert', 'insert' );
		$this->add_filter( 'update', 'update' );
		$this->add_filter( 'getItem', 'getItem' );
	}

	function insertOrUpdate( $param ) {
		$setting = IBoardSettingService::getInstance()->settingFromBID( $param['BID'] );

		if ( $setting->is_only_secret == 'Y' ) {
			$param['is_secret'] = 'on';
		}

		return $param;
	}

	function insert( $param ) {
		$param = $this->validate( $param );

		if ( iboard_is_error( $param ) ) {
			return $param;
		}

		$param['reg_date']    = date( 'Y-m-d H:i:s' );
		$param['update_date'] = date( 'Y-m-d H:i:s' );

		return $param;
	}

	function update( $param ) {
		$param = $this->validate( $param );

		if ( iboard_is_error( $param ) ) {
			return $param;
		}

		$param['update_date'] = date( 'Y-m-d H:i:s' );
		unset( $param['reg_date'] );

		return $param;
	}

	function getItem( $boardItem ) {
		if ( $boardItem instanceof IBoardItem ) {
			$boardItem->setCommentCnt( $this->service()->getCommentCount( $boardItem->ID ) );
		}

		return $boardItem;
	}

	function validate( $param ) {
		if ( iboard_is_error( $param ) ) {
			return $param;
		}

		if ( ! isset( $param['BID'] ) ) {
			return new IBoardError( "BID required" );
		}

		$setting = IBoardSettingService::getInstance()->settingFromBID( $param['BID'] );

		if ( is_null( $setting ) ) {
			return new IBoardError( "게시판이 존재하지 않습니다." );
		}

		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();

			$param['user_id']    = $user->ID;
			$param['user_email'] = $user->get( 'user_email' );
			$param['user_nm']    = $user->get( 'display_name' );
		}

		$iboard_content = iboard_request_post_param( 'iboard_content' );

		if ( ! empty( $iboard_content ) ) {
			$param['content'] = stripslashes_deep( $iboard_content );
		}

		$is_secret = iboard_request_post_param( 'is_secret', @$param['is_secret'] );

		if ( $is_secret == 'on' ) {
			$param['is_secret'] = 1;
		} else {
			$param['is_secret'] = 0;
		}

		$is_notice = iboard_request_post_param( 'is_notice', @$param['is_notice'] );

		if ( $is_notice == 'on' ) {
			$param['is_notice'] = 1;
		} else {
			$param['is_notice'] = 9999;
		}

		$param['subject']    = strip_tags( @$param['subject'] );
		$param['user_nm']    = strip_tags( @$param['user_nm'] );
		$param['user_email'] = strip_tags( @$param['user_email'] );
		$param['user_phone'] = strip_tags( @$param['user_phone'] );
		$param['user_tel']   = strip_tags( @$param['user_tel'] );
		$param['content']    = iboard_xss_filter( @$param['content'] );

		if ( empty( $param['subject'] ) ) {
			return new IBoardError( "제목은 필수입니다." );
		}

		if ( empty( $param['user_nm'] ) ) {
			return new IBoardError( "작성자는 필수입니다." );
		}

		if ( empty( $param['content'] ) ) {
			return new IBoardError( "내용을 입력하세요." );
		}

		$param['reg_ip'] = @$_SERVER['REMOTE_ADDR'];

		return $param;
	}
}