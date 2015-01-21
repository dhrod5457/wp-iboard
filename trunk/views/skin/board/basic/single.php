<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="<?php language_attributes(); ?>" style="margin:0 !important;padding:0 !important;">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>IBoard</title>
	<?php wp_head(); ?>
</head>
<body style="background: #fff;">
<div id="content">
	<?php
	$BID = iboard_request_param( 'iboard_ID' );
	echo do_shortcode( '[iboard bid="' . $BID . '"]' );
	?>
</div>
<?php wp_footer(); ?>
<script>
	(function ($) {
		function resizeIboardFrame() {
			var frame = parent.document.getElementById("iboard_frame_<?php echo $BID?>");
			frame.style.height = 0;
			var h = $(document).outerHeight();
			frame.style.height = h + "px";
		}

		$(window).load(function () {
			resizeIboardFrame();
		});

		$(document).on('iboard_file_callback', function () {
			resizeIboardFrame();
		});
	})(jQuery);
</script>
</body>
</html>