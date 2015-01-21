<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 15. 1. 4.
 * Time: 오후 4:05
 */
class IBoardCommentPage extends IBoardBasePage {
	/* @var IBoardCommentList */
	public $commentList;

	/* @var IBoardItem */
	public $iboardItem;

	/* @var IBoardCommentAuth */
	public $auth;

	public function init( $args ) {
		$this->iboardItem = $args['iboardItem'];

		if ( ! is_object( $this->iboardItem ) ) {
			return;
		}

		$this->commentList = new IBoardCommentList( array(
			'itemID'  => $this->iboardItem->ID,
			'BID'     => $this->iboardItem->BID,
			'setting' => $this->boardSetting
		) );

		$this->auth = new IBoardCommentAuth( null, $this->boardSetting );
	}

	public function view() {
		$this->getView( 'comment.php' );
	}

	public function commentForm() {
		if ( ! $this->boardSetting->isUseNoticeComment() && $this->iboardItem->isNotice() ) {
			return null;
		}

		if ( ! $this->auth->isCommentWriteAble() ) {
			return null;
		}

		ob_start();
		?>
		<div class="iboard_comment_form">
			<form action="<?php echo iboard_process_comment_url( 'insert' ); ?>"
			      method="post"
			      class="validateForm"
			      id="iboardCommentForm">

				<h3>댓글</h3>

				<?php if ( ! $this->auth->auth->isLogin ) { ?>
					<p class="comment_user_nm">
						<label for="comment_user_nm">작성자</label>
						<input type="text" name="user_nm" id="comment_user_nm" class="iboard_input"
						       required="required" placeholder="작성자"/>
					</p>

					<p class="comment_user_mail">
						<label for="comment_user_mail">이메일</label>
						<input type="text" name="user_mail" id="comment_user_mail" class="iboard_input"
						       required="required" placeholder="이메일"/>
					</p>

					<p class="comment_password">
						<label for="comment_password">비밀번호</label>
						<input type="password" name="password" id="comment_password" class="iboard_input"
						       required="required" placeholder="비밀번호"/>
					</p>
				<?php } ?>

				<p class="comment_content">
					<label for="comment_content">내용</label>
					<textarea name="content" id="comment_content" placeholder="내용"></textarea>
				</p>

				<div class="buttons">
					<div class="buttons_right">
						<input type="submit" value="댓글등록" class="iboard_button iboard_button_default"/>
					</div>
				</div>

				<input type="hidden" name="itemID" value="<?php echo $this->iboardItem->ID; ?>"/>
				<input type="hidden" name="BID" value="<?php echo $this->boardSetting->BID; ?>"/>
			</form>
		</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}
}