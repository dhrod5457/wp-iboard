<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 23.
 * Time: 오전 12:24
 */
class IBoardSettingInterceptor extends IBoardBaseInterceptor {
	function service() {
		return IBoardSettingService::getInstance();
	}

	function add_filters() {
		$this->add_filter( 'insert', 'insert' );
		$this->add_filter( 'update', 'update' );
		$this->add_filter( 'delete', 'delete' );
	}

	function insert( $param ) {
		if ( empty( $param['BID'] ) ) {
			return new IBoardError( 'ID는 필수입력입니다.' );
		}

		if ( empty( $param['title'] ) ) {
			return new IBoardError( '이름은 필수입력입니다.' );
		}
		if ( $this->service()->isAlready( $param['BID'] ) ) {
			return new IBoardError( '이미 존재하는 게시판입니다.' );
		}

		$param['reg_date'] = date( 'Y-m-d H:i:s' );

		return $param;
	}

	function update( $param ) {
		if ( empty( $param['BID'] ) ) {
			return new IBoardError( 'ID는 필수입력입니다.' );
		}

		if ( empty( $param['title'] ) ) {
			return new IBoardError( '이름은 필수입력입니다.' );
		}

		return $param;
	}

	function delete( $param ) {
		if ( empty( $param['ID'] ) ) {
			return new IBoardError( '게시판 고유 ID(INT)를 넣어주세요' );
		}

		return $param;
	}
}