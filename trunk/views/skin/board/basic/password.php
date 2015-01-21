<?php
/* @var $this IBoardBasePage */
?>
<div class="iboard iboardPassword iboard_<?php echo iboard_get_query_var( 'BID' ); ?>">
	<form action="<?php echo iboard_safe_link( "ID=" . iboard_get_query_var( 'ID' ) ); ?>"
	      method="post"
	      class="validateForm">

		<fieldset>
			<legend>비밀번호 입력 폼</legend>

			<p class="requirePassword"><strong><?php __iboard_e( '비밀번호를 입력해주세요.' ); ?></strong></p>

			<?php if ( ! iboard_is_empty( iboard_get_query_var( 'password' ) ) ) { ?>
				<p class="errorMessage"><em><?php __iboard_e( '올바른 비밀번호를 입력해주세요.' ); ?></em></p>
			<?php } ?>

			<input type="password" name="password" placeholder="<?php __iboard_e( '비밀번호' ); ?>" class="iboard_input"
			       required="required"/>

			<div class="buttons">
				<div class="buttons_left">
					<a href="<?php echo iboard_safe_link( 'pageMode=list' ) ?>"
					   title="<?php __iboard_e( '뒤로' ); ?>"
					   class="iboard_button iboard_button_default"><?php __iboard_e( '뒤로' ); ?></a>
				</div>
				<div class="buttons_right">
					<input type="submit" value="확인" class="iboard_button iboard_button_default"/>
				</div>
			</div>
		</fieldset>
	</form>
</div>