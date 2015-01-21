<?php
$setting = IBoardSettingService::getInstance()->settingFromBID( iboard_request_param( 'BID' ) );
$mode    = 'update';

if ( is_null( $setting ) ) {
	$setting = new IBoardSetting();
	$mode    = 'insert';
}
?>
<style>
	#iboardSettingForm .widefat td {
		overflow: visible;
	}

	#iboardSettingForm .chosen-select {
		width: 80%;
	}
</style>
<h3>게시판 생성</h3>

<form action="<?php echo admin_url( 'admin-ajax.php' ); ?>?action=iboard_setting_ajax&m=<?php echo $mode; ?>"
      id="iboardSettingForm">
	<table class="wp-list-table widefat ">
		<tr>
			<th><label for="BID">ID</label></th>
			<td>
				<input type="text" name="BID" id="BID"
				       value="<?php echo $setting->BID ?>" <?php if ( $mode == 'update' ) {
					echo 'readonly';
				} ?> />

				<p>영어로 입력해 주세요.</p>
			</td>
		</tr>
		<tr>
			<th><label for="title">게시판명</label></th>
			<td><input type="text" name="title" id="title" value="<?php echo $setting->title ?>"/></td>
		</tr>
		<tr>
			<th><label for="categories">카테고리</label></th>
			<td>
				<input style="width:80%;" type="text" name="categories" id="categories"
				       value="<?php echo $setting->categories ?>"/>

				<p>카테고리는 ,로 구분하여 입력해주세요.</p>
			</td>
		</tr>
		<tr>
			<th><label for="editor">에디터</label></th>
			<td>
				<select name="editor" id="editor">
					<?php $editors = iboard_get_editor_list(); ?>
					<?php foreach ( $editors as $key => $value ) { ?>
						<option value="<?php echo $key ?>"
							<?php if ( $key == $setting->editor ) {
								echo 'selected="selected"';
							} ?>
							><?php echo $value ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="admin_users">담당관리자 추가</label></th>
			<td>
				<input type="text" id="admin_users" name="admin_users" value="<?php echo $setting->admin_users; ?>"
				       class="iboard_input" style="width:80%;"/>

				<p>관리자 아이디를 추가해주세요. 구분은 , 로 하시면 됩니다. 예)test1,test2</p>
			</td>
		</tr>
		<tr>
			<th><label for="use_field_names_chosen">추가필드표시</label></th>
			<td>
				<select id="use_field_names_chosen" data-target="use_field_names" class="chosen-select"
				        multiple="multiple"
					>
					<?php iboard_chosen_select_box( iboard_get_use_field_names(), $setting->getUseFieldNames() ); ?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="use_notification">새글등록알림</label></th>
			<td>
				<select name="use_notification" id="use_notification">
					<option value="Y" <?php if ( 'Y' == $setting->use_notification ) {
						echo 'selected="selected"';
					} ?> >사용함
					</option>
					<option value="N" <?php if ( 'N' == $setting->use_notification ) {
						echo 'selected="selected"';
					} ?>>사용안함
					</option>
				</select>

				<p>새글이 등록되었을때 이메일로 전송됩니다. 답글은 원글의 작성자에게 전달되고 관리자에게 전달됩니다. <br/>자체 smtp 서버를 이용하지 않을경우 속도가 상당히 저하될수 있습니다.</p>
			</td>
		</tr>
		<tr>
			<th><label for="is_use_notice_comment">공지글에 댓글 사용여부</label></th>
			<td>
				<select name="is_use_notice_comment" id="is_use_notice_comment">
					<option value="Y" <?php if ( 'Y' == $setting->use_notification ) {
						echo 'selected="selected"';
					} ?> >사용함
					</option>
					<option value="N" <?php if ( 'N' == $setting->use_notification ) {
						echo 'selected="selected"';
					} ?>>사용안함
					</option>
				</select>

				<p>공지글로 설정한곳에 댓글을 사용할것인지 여부를 설정합니다. 관리자에게는 적용되지 않습니다.</p>
			</td>
		</tr>
		<tr>
			<th><label for="use_captcha">자동입력방지</label></th>
			<td>
				<?php if ( extension_loaded( 'gd' ) && function_exists( 'gd_info' ) ): ?>
					<select name="use_captcha" id="use_captcha">
						<option value="Y" <?php if ( 'Y' == $setting->use_captcha ) {
							echo 'selected="selected"';
						} ?> >사용함
						</option>
						<option value="N" <?php if ( 'N' == $setting->use_captcha ) {
							echo 'selected="selected"';
						} ?>>사용안함
						</option>
					</select>
				<?php else: ?>
					GD 라이브러리가 설치되어 있지 않아 활성화 할수 없습니다.
					<input type="hidden" name="use_captcha" value="N"/>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><label for="use_category_appear">제목 앞 카테고리 표시 여부</label></th>
			<td>
				<select name="use_category_appear" id="use_category_appear">
					<option value="Y" <?php if ( 'Y' == $setting->use_category_appear ) {
						echo 'selected="selected"';
					} ?> >사용함
					</option>
					<option value="N" <?php if ( 'N' == $setting->use_category_appear ) {
						echo 'selected="selected"';
					} ?>>사용안함
					</option>
				</select>

				<p>타이틀 앞에 카테고리를 표시 하도록 합니다.</p>
			</td>
		</tr>
		<tr>
			<th><label for="is_only_secret">비밀글 게시판 여부</label></th>
			<td>
				<select name="is_only_secret" id="is_only_secret">
					<option value="N" <?php if ( 'N' == $setting->is_only_secret ) {
						echo 'selected="selected"';
					} ?>>사용안함
					</option>
					<option value="Y" <?php if ( 'Y' == $setting->is_only_secret ) {
						echo 'selected="selected"';
					} ?> >사용함
					</option>
				</select>

				<p>사용할경우 모든글은 비밀글로만 작성됩니다.</p>
			</td>
		</tr>
		<tr>
			<th><label for="is_use_skin_css">스킨의 css 사용여부</label></th>
			<td>
				<select name="is_use_skin_css" id="is_use_skin_css">
					<option value="Y" <?php if ( 'Y' == $setting->is_use_skin_css ) {
						echo 'selected="selected"';
					} ?> >사용함
					</option>
					<option value="N" <?php if ( 'N' == $setting->is_use_skin_css ) {
						echo 'selected="selected"';
					} ?>>사용안함
					</option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="skin">게시판스킨</label></th>
			<td>
				<select name="skin" id="skin">
					<?php foreach ( iboard_get_skin_list( 'board' ) as $name ) { ?>
						<option value="<?php echo $name; ?>"
							<?php if ( $name == $setting->skin ) {
								echo 'selected="selected"';
							} ?>><?php echo $name; ?></option>
					<?php } ?>
				</select>
		</tr>
		<tr>
			<th><label for="skin_latest">최신글스킨</label></th>
			<td>
				<select name="skin_latest" id="skin_latest">
					<?php foreach ( iboard_get_skin_list( 'latest' ) as $name ) { ?>
						<option value="<?php echo $name; ?>"
							<?php if ( $name == $setting->skin_latest ) {
								echo 'selected="selected"';
							} ?>>
							<?php echo $name; ?>
						</option>
					<?php } ?>
				</select>
		</tr>
		<tr>
			<th><label for="list_chosen">목록권한</label></th>
			<td>
				<select id="list_chosen" data-target="list_role" class="chosen-select" multiple="multiple"
					>
					<?php iboard_role_chosen_select_box( $setting->getListRoles() ); ?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="read_chosen">읽기권한</label></th>
			<td>
				<select id="read_chosen" data-target="read_role" class="chosen-select" multiple="multiple"
					>
					<?php iboard_role_chosen_select_box( $setting->getReadRoles() ); ?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="write_chosen">쓰기권한</label></th>
			<td>
				<select id="write_chosen" data-target="write_role" class="chosen-select" multiple="multiple"
					>
					<?php iboard_role_chosen_select_box( $setting->getWriteRoles() ); ?>
				</select>
		</tr>
		<tr>
			<th><label for="write_notice_chosen">공지글 쓰기권한</label></th>
			<td>
				<select id="write_notice_chosen" data-target="notice_role" class="chosen-select" multiple="multiple"
					>
					<?php iboard_role_chosen_select_box( $setting->getNoticeWriteRoles() ); ?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="comment_write_chosen">댓글쓰기권한</label></th>
			<td>
				<select id="comment_write_chosen" data-target="comment_write_role" class="chosen-select"
				        multiple="multiple"
					>
					<?php iboard_role_chosen_select_box( $setting->getCommentWriteRoles() ); ?>
				</select>
		</tr>
		<tr>
			<th><label for="down_chosen">파일다운권한</label></th>
			<td>
				<select id="down_chosen" data-target="down_role" class="chosen-select" multiple="multiple"
					>
					<?php iboard_role_chosen_select_box( $setting->getDownRoles() ); ?>
				</select>
		</tr>
		<tr>
			<th><label for="file_cnt">파일업로드수</label></th>
			<td>
				<input type="number" name="file_cnt" value="<?php echo $setting->file_cnt; ?>" id="file_cnt"/>
			</td>
		</tr>
		<tr>
			<th><label for="list_cnt">페이지당 목록수</label></th>
			<td>
				<input type="number" name="list_cnt" value="<?php echo $setting->list_cnt; ?>" id="list_cnt"/>
			</td>
		</tr>
		<tr>
			<th style="vertical-align: top"><label for="base_content">기본 글 설정</label></th>
			<td>
				<?php echo iboard_get_editor( $this, $setting->base_content, 'base_content' ); ?>
			</td>
		</tr>
		<tr class="banned_words">
			<th style="vertical-align: top"><label for="banned_words">금칙어설정</label></th>
			<td>
			<textarea name="banned_words" id="banned_words" rows="5"
			          style="width:100%;"><?php echo $setting->getBannedWords() ?></textarea>
			</td>
		</tr>
	</table>

	<p style="text-align: right;">
		<input type="submit" value="확인" class="button"/>
	</p>


	<input type="hidden" name="notice_role" id="notice_role"/>
	<input type="hidden" name="comment_write_role" id="comment_write_role"/>
	<input type="hidden" name="use_field_names" id="use_field_names"/>
	<input type="hidden" name="read_role" id="read_role"/>
	<input type="hidden" name="write_role" id="write_role"/>
	<input type="hidden" name="down_role" id="down_role"/>
	<input type="hidden" name="list_role" id="list_role"/>
	<input type="hidden" name="ID" value="<?php echo $setting->ID ?>"/>
</form>