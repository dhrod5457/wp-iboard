<?php
// default redirection
if ( ! headers_sent() ) {
	@session_start();
}
$abs_path = $_SESSION['ABSPATH'];

require_once $abs_path . 'wp-load.php';

$dir = wp_upload_dir();

$sFileInfo = '';
$headers   = array();

foreach ( $_SERVER as $k => $v ) {
	if ( substr( $k, 0, 9 ) == "HTTP_FILE" ) {
		$k             = substr( strtolower( $k ), 5 );
		$headers[ $k ] = $v;
	}
}

$file          = new stdClass;
$file->name    = str_replace( "\0", "", rawurldecode( $headers['file_name'] ) );
$file->size    = $headers['file_size'];
$file->content = file_get_contents( "php://input" );

$filename_ext = strtolower( array_pop( explode( '.', $file->name ) ) );
$allow_file   = array( "jpg", "png", "bmp", "gif" );

if ( ! in_array( $filename_ext, $allow_file ) ) {
	echo "NOTALLOW_" . $file->name;
} else {
	$uploadDir = $dir['path'] . '/naverse/';

	if ( ! is_dir( $uploadDir ) ) {
		mkdir( $uploadDir, 0777, true );
	}

	$newPath = $uploadDir . $file->name;//iconv( "utf-8", "cp949", $file->name );

	$url = $_SERVER['PHP_SELF'];

	$base = dirname( dirname( dirname( $_SERVER['PHP_SELF'] ) ) );

	if ( file_put_contents( $newPath, $file->content ) ) {
		$sFileInfo .= "&bNewLine=true";
		$sFileInfo .= "&sFileName=" . $file->name;
		$sFileInfo .= "&sFileURL=" . $dir['url'] . "/naverse/" . $file->name;
	}

	echo $sFileInfo;
}