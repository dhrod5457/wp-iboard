<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 15. 1. 5.
 * Time: 오전 12:45
 */
class IBoardCommentInterceptor extends IBoardBaseInterceptor {
	function service() {
		return IBoardCommentService::getInstance();
	}

	function add_filters() {
		$this->add_filter( 'insert', 'insert' );
		$this->add_filter( 'update', 'update' );
		$this->add_filter( 'delete', 'delete' );
	}

	function insert( $param ) {
		$param = $this->validate( $param );

		if ( iboard_is_error( $param ) ) {
			return $param;
		}

		$param['reg_date'] = iboard_now();

		return $param;
	}

	function update( $param ) {
		if ( iboard_is_error( $param ) ) {
			return $param;
		}

		$param = $this->validate( $param );

		$param['update_date'] = iboard_now();
		unset( $param['reg_date'] );

		return $param;
	}

	function delete( $param ) {
		if ( iboard_is_error( $param ) ) {
			return $param;
		}
		
		$comment = $this->service()->commentFromID( $param['ID'] );
		$setting = IBoardSettingService::getInstance()->settingFromBID( iboard_request_param( 'BID' ) );
		$auth    = new IBoardCommentAuth( $comment, $setting );

		if ( ! $auth->isModifyAble( iboard_request_param( 'password' ) ) ) {
			return new IBoardError( "권한이 부족합니다." );
		}

		return $param;
	}

	function validate( $param ) {
		$setting = IBoardSettingService::getInstance()->settingFromBID( iboard_request_param( 'BID' ) );

		$auth = new IBoardCommentAuth( null, $setting );

		if ( ! $auth->isCommentWriteAble() ) {
			return new IBoardError( "권한이 없습니다." );
		}

		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();

			$param['user_id']   = $user->ID;
			$param['user_nm']   = $user->get( 'display_name' );
			$param['user_mail'] = $user->get( 'user_email' );
		} else {
			$param['password'] = strip_tags( @$param['user_nm'] );
			if ( empty( $param['password'] ) ) {
				return new IBoardError( "비밀번호를 입력하세요." );
			}
		}

		$param['user_nm'] = strip_tags( @$param['user_nm'] );
		$param['content'] = strip_tags( nl2br( @$param['content'] ), '<br>' );

		if ( empty( $param['user_nm'] ) ) {
			return new IBoardError( "작성자를 입력하세요." );
		}

		if ( empty( $param['content'] ) ) {
			return new IBoardError( "내용을 입력하세요." );
		}

		if ( empty( $param['user_mail'] ) ) {
			return new IBoardError( "이메일 을 입력하세요." );
		}

		return $param;
	}
}