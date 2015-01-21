<?php
// default redirection
if ( ! headers_sent() ) {
	@session_start();
}
$abs_path = $_SESSION['ABSPATH'];

require_once $abs_path . 'wp-load.php';

$dir = wp_upload_dir();

$url            = $_REQUEST["callback"] . '?callback_func=' . $_REQUEST["callback_func"];
$bSuccessUpload = is_uploaded_file( $_FILES['Filedata']['tmp_name'] );

if ( bSuccessUpload ) {
	$tmp_name = $_FILES['Filedata']['tmp_name'];
	$name     = $_FILES['Filedata']['name'];

	$filename_ext = strtolower( array_pop( explode( '.', $name ) ) );
	$allow_file   = array( "jpg", "png", "bmp", "gif" );

	if ( ! in_array( $filename_ext, $allow_file ) ) {
		$url .= '&errstr=' . $name;
	} else {
		$uploadDir = $dir['path'] . '/naverse/';

		if ( ! is_dir( $uploadDir ) ) {
			mkdir( $uploadDir, 0777, true );
		}

		$newPath = $uploadDir . urlencode( $_FILES['Filedata']['name'] );

		@move_uploaded_file( $tmp_name, $newPath );

		$base = dirname( dirname( dirname( $_SERVER['PHP_SELF'] ) ) );

		$url .= "&bNewLine=true";
		$url .= "&sFileName=" . urlencode( urlencode( $name ) );
		$url .= "&sFileURL=" . $dir['url'] . "/naverse/" . urlencode( urlencode( $name ) );
	}
} // FAILED
else {
	$url .= '&errstr=error';
}

header( 'Location: ' . $url );