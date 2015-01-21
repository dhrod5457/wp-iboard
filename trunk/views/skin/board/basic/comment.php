<?php
/* @var $this IBoardCommentPage */
?>
<ul class="iboard_comment_list">
	<?php foreach ( $this->commentList->comments as $comment ) { ?>
		<li>
			<span class="comment_user_avatar">
				<?php echo get_avatar( $comment->user_mail, 30 ); ?>
			</span>
			<span class="comment_user_nm"><?php echo $comment->user_nm; ?></span>
			<span class="comment_reg_date"><?php echo $comment->reg_date; ?></span>
			<span class="comment_content"><?php echo $comment->content; ?></span>
			<?php if ( $comment->auth->isModifyAble() || $this->boardSetting->isNonMemberCommentAble() ) { ?>
				<a href="<?php echo iboard_process_comment_url( 'delete', array(
					'itemID' => $comment->itemID,
					'ID'     => $comment->ID
				) ); ?>"
				   title="<?php __iboard_e( "삭제" ); ?>"><?php __iboard_e( "삭제" ); ?></a>
			<?php } ?>
		</li>
	<?php } ?>
</ul>