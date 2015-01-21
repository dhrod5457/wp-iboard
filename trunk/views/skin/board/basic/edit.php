<?php
/* @var $this IBoardEditPage */
?>
<div class="iboard iboardEdit iboard_<?php echo iboard_get_query_var( 'BID' ); ?>">
	<form action="<?php echo iboard_process_url( $this->mode ); ?>"
	      method="post"
	      class="validateForm"
	      id="iboardEditForm">
		<fieldset>
			<legend>글쓰기 폼</legend>
			<?php do_action( 'iboard_edit_form_start', $this ); ?>

			<table class="iboard_table">
				<caption>글쓰기</caption>
				<tbody>
				<tr class="iboard_subject">
					<th scope="col"><label for="subject"><?php __iboard_e( '제목' ); ?></label></th>
					<td><input type="text" id="subject" name="subject"
					           class="iboard_input"
					           required="required"
					           value="<?php echo $this->boardItem->getSubject(); ?>"/>
					</td>
				</tr>

				<?php if ( $this->boardSetting->hasCategory() ) { ?>
					<tr>
						<th scope="col"><label for="category">카테고리</label></th>
						<td>
							<select name="category" id="category">
								<?php foreach ( $this->boardSetting->getCategories() as $category ) { ?>
									<option value="<?php echo $category ?>"
									        <?php if (iboard_request_param( 'category' ) == $category) { ?>selected="selected" <?php } ?>>
										<?php echo $category ?>
									</option>
								<?php } ?>
							</select>
						</td>
					</tr>
				<?php } ?>

				<?php if ( $this->boardSetting->isUseField( 'email' ) ) { ?>
					<tr class="iboard_mail">
						<th scope="col"><label for="user_email"><?php __iboard_e( '이메일' ); ?></label></th>
						<td><input type="text" id="user_email" name="user_email"
						           class="iboard_input"
						           value="<?php echo $this->boardItem->user_email; ?>"
						           required="required"/>
						</td>
					</tr>
				<?php } ?>

				<?php if ( $this->boardSetting->isUseField( 'phone' ) ) { ?>
					<tr class="iboard_phone">
						<th scope="col"><label for="user_phone"><?php __iboard_e( '휴대폰' ); ?></label></th>
						<td><input type="text" id="user_phone" name="user_phone"
						           class="iboard_input"
						           value="<?php echo $this->boardItem->user_phone; ?>"
						           required="required"/>
						</td>
					</tr>
				<?php } ?>

				<?php if ( $this->boardSetting->isUseField( 'tel' ) ) { ?>
					<tr class="iboard_tel">
						<th scope="col"><label for="user_tel"><?php __iboard_e( '전화번호' ); ?></label></th>
						<td><input type="text" id="user_tel" name="user_tel"
						           class="iboard_input"
						           value="<?php echo $this->boardItem->user_tel; ?>"
						           required="required"/>
						</td>
					</tr>
				<?php } ?>

				<?php if ( $this->isRequiredFields() ) : ?>
					<tr class="iboard_user_nm">
						<th scope="col"><label for="content"><?php __iboard_e( '작성자' ); ?></label></th>
						<td><input type="text" id="user_nm" name="user_nm"
						           class="iboard_input"
						           value="<?php echo $this->boardItem->getUserNm(); ?>"
						           required="required"/>
						</td>
					</tr>

					<?php if ( $this->isInsertMode() ) { ?>
						<tr class="iboard_password">
							<th scope="col"><?php __iboard_e( '비밀번호' ); ?></th>
							<td>
								<input type="password" name="password"
								       class="iboard_input"
								       value="<?php echo $this->boardItem->getPassword(); ?>"
								       required="required"/>
							</td>
						</tr>
					<?php } ?>

					<?php if ( $this->isUpdateMode() ) { ?>
						<input type="hidden"
						       name="password"
						       value="<?php echo $this->boardItem->getPassword(); ?>"/>
					<?php } ?>
				<?php endif; ?>

				<tr class="iboard_etc">
					<th scope="col"><?php __iboard_e( '선택사항' ); ?></th>
					<td>
						<?php if ( ! $this->boardSetting->isOnlySecret() ) : ?>
							<label for="is_secret"><?php __iboard_e( '비밀글' ); ?></label>
							<input type="checkbox"
							       id="is_secret"
							       name="is_secret"
								<?php if ( $this->boardItem->is_secret ) {
									echo 'checked="checked"';
								} ?>/>
						<?php endif; ?>

						<?php if ( $this->isWriteNoticeAble() ) { ?>
							<label for="is_notice"><?php __iboard_e( '공지글여부' ); ?></label>
							<input type="checkbox"
							       id="is_notice"
							       name="is_notice"
								<?php if ( $this->boardItem->isNotice() ) {
									echo 'checked="checked"';
								} ?>/>
						<?php } ?>
					</td>
				</tr>

				<tr class="iboard_content">
					<td colspan="2">
						<label for="iboard_content" class="iboard_hide"><?php __iboard_e( '내용' ); ?></label>
						<?php echo $this->editor(); ?>
					</td>
				</tr>

				<?php if ( $this->boardSetting->isUseCaptcha() ) { ?>
					<tr class="iboard_captcha">
						<th scope="col">
							<img src="<?php echo iboard_get_captcha_img( 40 ) ?>" alt=""/>
						</th>
						<td>
							<input type="text" name="ct_captcha" id="ct_captcha" class="iboard_input"
							       required="required"/>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>

			<div class="buttons">
				<div class="buttons_right">
					<input type="submit" class="iboard_button iboard_button_default"
					       value="<?php __iboard_e( '확인' ); ?>"/>
					<a href="<?php echo $this->getBackLink(); ?>"
					   title="<?php __iboard_e( '취소' ); ?>"
					   class="iboard_button iboard_button_default"><?php __iboard_e( '취소' ); ?></a>
				</div>
			</div>

			<input type="hidden" name="ID" value="<?php echo $this->boardItem->ID ?>"/>
			<input type="hidden" name="BID" value="<?php echo $this->boardSetting->BID ?>"/>
			<input type="hidden" name="grp" value="<?php echo $this->boardItem->grp ?>"/>
			<input type="hidden" name="mode" value="<?php echo $this->mode ?>"/>
			<input type="hidden" name="parent" value="<?php echo iboard_get_query_var( 'parent' ); ?>"/>
			<?php do_action( 'iboard_edit_form_end', $this ); ?>
		</fieldset>
	</form>
</div>