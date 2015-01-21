<h3>환경설정</h3>

<form action="options.php" method="post">
	<?php settings_fields( 'iboard_setting' ); ?>

	<table class="wp-list-table widefat fixed pages">
		<?php do_action( 'iboard_setting_page', $this ); ?>
	</table>

	<div class="tablenav">
		<div class="alignright" >
			<input type="submit" id="doaction" class="button button-big action" value="확인" style="margin-right: 0;">
		</div>
	</div>
</form>


