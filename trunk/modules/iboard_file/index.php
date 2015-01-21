<?php

function iboard_file_init() {
	$pageMode = iboard_get_query_var( 'pageMode' );

	if ( $pageMode == 'edit' || $pageMode == 'reply' ) {
		add_action( 'wp_enqueue_scripts', 'iboard_file_view_css' );
		add_action( 'wp_enqueue_scripts', 'iboard_file_register_resources' );
		add_action( 'iboard_editor_after', 'iboard_file_button' );
	}

	if ( $pageMode == 'read' ) {
		add_action( 'wp_enqueue_scripts', 'iboard_file_view_css' );
	}
}

add_action( 'iboard_page_init', 'iboard_file_init' );

function iboard_file_button( IBoardEditPage $page ) {
	$upload_file_cnt = $page->boardSetting->file_cnt;

	if ( $upload_file_cnt <= 0 ) {
		return;
	}

	iboard_upload_button( array(
		'title'        => __iboard( '파일첨부' ),
		'js_callback'  => 'iboard_file_callback',
		'pre_function' => 'iboard_file_pre',
		'id'           => 'iboard_file_btn',
		'p_check'      => true
	) );

	$file_message = sprintf( __iboard( "파일은 %s 개 까지 업로드 할수 있습니다." ), "<strong>{$page->boardSetting->file_cnt}</strong>" );

	echo "<p class='iboard_file_help'>{$file_message}</p>";
	echo "<ul class='iboard_upload_file_list'></ul>";
}

function iboard_file_view_css() {
	wp_register_style( 'iboard-file', IBOARD_PLUGIN_MODULE_URL . '/iboard_file/iboard.file.css' );
	wp_enqueue_style( 'iboard-file' );
}

function iboard_file_register_resources() {
	wp_register_script( 'iboard-file', IBOARD_PLUGIN_MODULE_URL . '/iboard_file/iboard.file.js', array( 'iboard-base' ) );
	wp_enqueue_script( 'iboard-file' );
}


function iboard_file_after( $name, $param ) {
	if ( is_admin() ) {
		return;
	}

	$ID = $param['ID'];

	if ( is_null( $ID ) ) {
		return;
	}

	$request_files = iboard_request_param( 'iboard_file' );
	$model         = IBoardItemService::getInstance()->itemFromID( $ID );

	if ( ! is_array( $request_files ) && is_object( $model ) ) {
		$model_files = $model->get_meta( 'files' );

		if ( ! empty( $model_files ) ) {
			$model->delete_meta( 'files' );
		}

		return;
	}

	$file_names = iboard_request_param( 'iboard_file_name' );

	if ( is_array( $file_names ) && is_object( $model ) ) {
		$result = array();

		$i = 0;

		foreach ( $file_names as $name ) {
			$result[] = array(
				"name"     => $name,
				"url"      => $request_files[ $i ],
				'realName' => array_pop( explode( '/', $request_files[ $i ] ) )
			);

			$i ++;
		}


		$model->update_meta( 'files', $result );
	}
}

add_action( 'iboard_insert_after', 'iboard_file_after', 1, 2 );
add_action( 'iboard_update_after', 'iboard_file_after', 1, 2 );

function iboard_file_view( IBoardReadPage $page ) {
	$files = $page->boardItem->getFileList();

	if ( is_null( $files ) ) {
		return;
	}
	?>
	<div class="iboard_file_list">
		<dl>
			<dt><?php __iboard_e( '첨부파일' ); ?></dt>
			<dd>
				<ul>
					<?php foreach ( $files as $file ) {
						?>
						<li>
							<a href="<?php echo iboard_safe_link( $file['down'], get_permalink() ); ?>"
							   title="<?php echo $file['name'] . " " . __iboard( "다운로드" ); ?> ">
								<?php echo $file['name'] ?>
							</a>
						</li>
					<?php } ?>
				</ul>
			</dd>
		</dl>
	</div>
<?php
}

add_action( 'iboard_read_content_pre', 'iboard_file_view' );

function iboard_file_item_pre( $boardItem ) {
	if ( ! $boardItem instanceof IBoardItem ) {
		return;
	}

	$fileList = $boardItem->get_meta( 'files' );

	if ( is_array( $fileList ) ) {
		foreach ( $fileList as &$file ) {
			$realName = @$file['realName'];

			$file['down'] = "pageMode=down&file={$realName}&ID={$boardItem->ID}";
		}
	}

	$boardItem->setFileList( $fileList );

	return $boardItem;
}

add_filter( IBoardItemService::getInstance()->getServiceName() . 'getItem', 'iboard_file_item_pre' );

function iboard_file_ajax_list() {
	header( "Content-type: application/json; charset=utf-8" );
	$ID    = iboard_request_param( 'ID' );
	$model = IBoardItemService::getInstance()->itemFromArray( array( 'ID' => $ID ) );

	echo json_encode( $model->get_meta( 'files' ) );

	die;
}

add_action( 'wp_ajax_iboard_file_ajax_list', 'iboard_file_ajax_list' );
add_action( 'wp_ajax_nopriv_iboard_file_ajax_list', 'iboard_file_ajax_list' );