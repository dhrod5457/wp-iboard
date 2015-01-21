<?php
/* @var $this IBoardBasePage */
?>
<div class="iboard iboardPassword iboard_<?php echo iboard_get_query_var( 'BID' ); ?>">
	<p><?php echo iboard_error_message( $this->error ) ?></p>

	<div class="buttons">
		<a href="<?php echo iboard_get_referer() ?>"
		   title="<?php __iboard_e( '뒤로' ); ?>">
			<?php __iboard_e( '뒤로' ); ?>
		</a>
	</div>
</div>