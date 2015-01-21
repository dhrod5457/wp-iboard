<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 28.
 * Time: 오후 1:16
 */
class IBoardDownLoadPage extends IBoardBasePage {
	public function init( $args ) {
		/* @var $iboard_uploader IBoardUploader */
		global $iboard_uploader;

		$ID   = iboard_request_param( "ID" );
		$item = IBoardItemService::getInstance()->itemFromID( $ID );

		$file     = iboard_request_param( "file" );
		$fileName = array_pop( explode( "/", $file ) );

		$dir             = wp_upload_dir( $item->update_date );
		$upload_base_dir = $dir['basedir'];

		$check_path = IBoardUploader::CHECK;
		$all_path   = IBoardUploader::ALL;

		$check = is_file( $upload_base_dir . "/{$check_path}/{$dir['subdir']}/{$fileName}" );

		if ( $check ) {
			$auth = new IBoardFileAuth( array(
				'boardItem'    => $item,
				'boardSetting' => IBoardSettingService::getInstance()->settingFromBID( $item->BID )
			) );

			if ( ! $auth->isDownAble() ) {
				iboard_alert( "권한이 부족합니다." );
				die;
			}

			$file = $upload_base_dir . "/{$check_path}/{$dir['subdir']}/{$fileName}";
		} else {
			$file = $upload_base_dir . "/{$all_path}/{$dir['subdir']}/{$fileName}";
		}

		if ( ! is_file( $file ) ) {
			wp_die( "파일을 찾을수 없습니다." );
		}

		$file_size = filesize( $file );

		$fileName = $iboard_uploader->get_file_real_name_by_name( $fileName );

		header( "Pragma: public" );
		header( "Expires: 0" );
		header( "Content-Type: application/octet-stream" );
		header( "Content-Disposition: attachment; filename=\"$fileName\"" );
		header( "Content-Transfer-Encoding: binary" );
		header( "Content-Length: $file_size" );

		$fp = fopen( $file, "r" );
		if ( ! fpassthru( $fp ) ) {
			fclose( $fp );
		}
		die;
	}


	public function view() {
		return null;
	}
}