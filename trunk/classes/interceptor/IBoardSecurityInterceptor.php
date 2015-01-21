<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 22.
 * Time: 오후 9:52
 */
class IBoardSecurityInterceptor extends IBoardBaseInterceptor {
	function service() {
		return IBoardItemService::getInstance();
	}

	function add_filters() {
		$this->add_filter( 'insert', 'insert' );
		$this->add_filter( 'delete', 'delete' );
		$this->add_filter( 'update', 'update' );
	}

	function insert( $param ) {
		$settingService = IBoardSettingService::getInstance();
		$setting        = $settingService->settingFromBID( $param['BID'] );

		$boardItem     = $this->service()->itemFromArray( $param );
		$boardItemAuth = new IBoardItemAuth( array( 'boardItem' => $boardItem, 'boardSetting' => $setting ) );

		if ( ! $boardItemAuth->isWriteAble() ) {
			return new IBoardError( "권한이 부족합니다." );
		}

		if ( $setting->isNonMemberEditAble() && ! $boardItemAuth->auth->isLogin ) {
			$password = @$param['password'];

			if ( empty( $password ) ) {
				return new IBoardError( "비밀번호가 필요합니다." );
			}
		}

		return $param;
	}

	function update( $param ) {
		$settingService = IBoardSettingService::getInstance();
		$setting        = $settingService->settingFromBID( $param['BID'] );
		$boardItem      = $this->service()->itemFromID( $param['ID'] );

		$boardItemAuth = new IBoardItemAuth( array(
			'boardItem'    => $boardItem,
			'boardSetting' => $setting
		) );

		$password = @$param['password'];

		if ( $setting->isNonMemberEditAble() && ! $boardItemAuth->auth->isLogin ) {
			if ( empty( $password ) ) {
				return new IBoardError( "비밀번호가 필요합니다." );
			} else {
				if ( $password != $boardItem->password ) {
					return new IBoardError( "비밀번호가 다릅니다." );
				}
			}
		} else {
			if ( ! $boardItemAuth->isModifyAble( $param ) ) {
				return new IBoardError( "권한이 부족합니다." );
			}
		}

		return $param;
	}

	function delete( $param ) {
		$boardItem      = $this->service()->itemFromID( $param );
		$settingService = IBoardSettingService::getInstance();
		$setting        = $settingService->settingFromBID( $boardItem->BID );

		$boardItemAuth = new IBoardItemAuth( array(
			'boardItem'    => $boardItem,
			'boardSetting' => $setting
		) );

		if ( ! $boardItemAuth->isDeleteAble( $param ) ) {
			return new IBoardError( "권한이 부족합니다." );
		}

		return $param;
	}
}