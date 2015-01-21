<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 23.
 * Time: 오전 1:13
 */
class IBoardEditPage extends IBoardBasePage {
	/* @var IBoardItem */
	public $boardItem;

	/* @var IBoardItemService */
	public $service;

	public $mode;

	public function init( $args ) {
		$this->service = IBoardItemService::getInstance();

		if ( isset( $args['ID'] ) ) {
			$this->mode      = 'update';
			$this->boardItem = $this->service->itemFromID( $args['ID'] );

		} else {
			$this->mode      = 'insert';
			$this->boardItem = $this->service->itemFromArray( array(
				'BID' => $this->boardSetting->BID,
				'grp' => $args['grp']
			) );
		}
	}

	public function isInsertMode() {
		return $this->mode == 'insert';
	}

	public function isUpdateMode() {
		return $this->mode == 'update';
	}

	public function getBackLink() {
		if ( $this->isUpdateMode() ) {
			return iboard_safe_link( "pageMode=read&ID={$this->boardItem->ID}" );
		}

		if ( $this->isInsertMode() ) {
			return iboard_safe_link( "pageMode=list" );
		}

		return false;
	}

	public function isRequiredFields() {
		return $this->boardSetting->isNonMemberEditAble() && ! $this->iboard_authorizer->user->isLogin;
	}

	public function isWriteNoticeAble() {
		return $this->iboard_authorizer->getNoticeRole()->result;
	}

	public function view() {
		if ( $this->mode == 'insert' ) {
			$result = $this->iboard_authorizer->getInsertRole();
		} else {
			$result = $this->iboard_authorizer->getUpdateRole();
		}

		if ( $result instanceof IBoardAuthorizerError ) {
			if ( $result->message == IBoardAuthorizerError::PERMISSION_ERROR ) {
				$this->error = new IBoardError( '글 등록 권한이 부족합니다.' );
				$this->getView( 'error.php' );
			}

			if ( $result->message == IBoardAuthorizerError::PASSWORD_INCORRECT || $result->message == IBoardAuthorizerError::PASSWORD_REQUIRED ) {
				$this->getView( 'password.php' );
			}
		} else {
			$this->getView( 'edit.php' );
		}
	}

	public function editor() {
		return iboard_get_editor( $this, $this->boardItem->getContent(), 'iboard_content', array( 'type' => $this->boardSetting->editor ) );
	}
}