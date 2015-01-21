<?php

/**
 * Created by PhpStorm.
 * User: OKS
 * Date: 2015-01-07
 * Time: 오후 5:08
 */
class IBoardProcessError {
	const CAPTCHA_ERROR = 'CAPTCHA_ERROR';
	const BASIC_ERROR = 'BASIC_ERROR';

	public $result = false;
	public $message;

	function __construct( $message ) {
		$this->message = $message;
	}
}

class IBoardProcessResult {
	public $result = true;
	public $objects;

	function __construct( $objects ) {
		$this->objects = $objects;
	}
}

class IBoardProcessHandler {
	public function delete( $args ) {
		$service = IBoardItemService::getInstance();

		$ID       = $args['ID'];
		$password = $args['password'];
		$item     = $service->itemFromID( $ID );

		do_action( 'iboard_process_delete_pre', $item );

		$del = $service->delete( array(
			'ID'       => $ID,
			'password' => $password
		) );

		if ( iboard_is_error( $del ) ) {
			return new IBoardProcessError( $del->message );
		}

		do_action( 'iboard_process_delete_after', $item );

		return new IBoardProcessResult( true );
	}

	public function update( $args ) {
		$service = IBoardItemService::getInstance();
		$model   = $service->itemFromArray( $args );
		$model->prepareInsert();

		$setting = IBoardSettingService::getInstance()->settingFromBID( $model->BID );

		$captcha_check = $this->check_captcha( $setting );

		if ( $captcha_check instanceof IBoardProcessError ) {
			return $captcha_check;
		}

		unset( $model->read_cnt );
		unset( $model->grp );
		unset( $model->ord );
		unset( $model->depth );
		unset( $model->parent );

		do_action( 'iboard_process_update_pre', $model );

		$update = $service->update( $model );

		if ( $update instanceof IBoardError ) {
			return new IBoardProcessError( $update->message );
		}

		$model->update_meta_fields( $_POST );

		do_action( 'iboard_process_update_after', $model );

		return new IBoardProcessResult( true );
	}

	public function insert( $args ) {
		$service = IBoardItemService::getInstance();
		$model   = $service->itemFromArray( $args );
		$model->prepareInsert();

		$setting = IBoardSettingService::getInstance()->settingFromBID( $model->BID );

		$captcha_check = $this->check_captcha( $setting );

		if ( $captcha_check instanceof IBoardProcessError ) {
			return $captcha_check;
		}

		if ( is_null( $model->grp ) || $model->grp == 0 ) {
			$model->grp = $service->generateGrp();
			$model->ord = 0;

			do_action( 'iboard_process_insert_pre', $model );

			$result = $service->insert( $model );

			if ( iboard_is_error( $result ) ) {
				return new IBoardProcessError( IBoardProcessError::BASIC_ERROR );
			}

			$item = $service->itemFromID( $result );

			$item->update_meta_fields( $args );

			do_action( 'iboard_process_insert_after', $item );

			if ( $setting->use_notification == 'Y' ) {
				iboard_notification_admin_email( $item );
			}

			return new IBoardProcessResult( $item );
		} else {
			$parent = iboard_get_query_var( 'parent' );

			if ( ! empty( $parent ) && $parent != 0 ) {
				do_action( 'iboard_process_reply_insert_pre', $model, $parent );

				$result = $service->replyInsert( $model, $parent );

				$parentItem = $service->itemFromID( $parent );
				$replyItem  = $service->itemFromID( $result );
				$replyItem->update_meta_fields( $args );

				if ( $setting->use_notification == 'Y' ) {
					iboard_notification_admin_email( $replyItem );
					iboard_reply_mail_send( $replyItem, $parentItem );
				}

				do_action( 'iboard_process_reply_insert_after', $replyItem, $parentItem );

				$parentItem->unset_password();
				$replyItem->unset_password();

				return new IBoardProcessResult( array( 'replyItem' => $replyItem, 'parentItem' => $parent ) );
			}
		}
	}

	public function check_captcha( IBoardSetting $setting ) {
		if ( $setting->isUseCaptcha() ) {
			$image   = new Securimage();
			$captcha = iboard_request_post_param( 'ct_captcha', false );

			if ( ! $image->check( $captcha ) ) {
				$result = new IBoardProcessError( IBoardProcessError::CAPTCHA_ERROR );

				$result->message = "자동입력방지 문구를 올바르게 입력하세요.";

				return $result;
			}
		}

		return true;
	}
} 