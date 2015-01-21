<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 15. 1. 3.
 * Time: 오후 11:41
 */
class IBoardIFrameShortCode extends IBoardBaseShortCode {
	public function shortCodeName() {
		return 'iboard_frame';
	}

	public function getIdentityVar() {
		return 'bid';
	}

	public function shortCodePre( $atts ) {
	}

	public function shortCodeAfter( $atts, $content = null ) {
		ob_start();
		$prefix = iboard_get_query_prefix( get_permalink() );
		?>
		<iframe src="<?php echo get_permalink() . $prefix . "iboard_ID={$atts['bid']}"; ?>"
		        frameborder="0"
		        scrolling="no"
		        style="width:100%;"
		        id="iboard_frame_<?php echo $atts['bid']; ?>"></iframe>
		<?php
		$result = ob_get_contents();
		ob_end_clean();

		return $content . $result;
	}
}