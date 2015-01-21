<?php
/* @var $this IBoardReadPage */
?>
<div class="iboard iboardRead iboard_<?php echo iboard_get_query_var( 'BID' ); ?>">
	<div class="iboard_subject">
		<h4><?php echo $this->boardItem->getSubject(); ?></h4>
	</div>

	<div class="iboard_meta">
		<ul>
			<li class="user_nm">
				<span class="t1">작성자</span>
				<span class="t2"><?php echo $this->boardItem->getUserNm(); ?></span>
			</li>
			<li class="reg_date">
				<span class="t1">작성일</span>
				<span class="t2"><?php echo $this->boardItem->getRegDate(); ?></span>
			</li>
			<li class="read_cnt">
				<span class="t1">조회수</span>
				<span class="t2"><?php echo $this->boardItem->getReadCnt(); ?></span>
			</li>
		</ul>
	</div>

	<?php do_action( 'iboard_read_content_pre', $this ); ?>

	<div class="iboard_content">
		<?php echo $this->boardItem->getContent(); ?>
	</div>

	<?php echo $this->navigation(); ?>

	<?php echo $this->commentList(); ?>
	<?php echo $this->commentPage->commentForm(); ?>

	<div class="buttons">
		<div class="buttons_left">
			<a href="<?php echo $this->getBackLink(); ?>"
			   title="<?php __iboard_e( '뒤로' ); ?>"
			   class="iboard_button iboard_button_default"><?php __iboard_e( '뒤로' ); ?></a>
		</div>

		<div class="buttons_right">
			<?php if ( $this->isWriteAble() ) { ?>
				<a href="<?php echo $this->getWriteLink(); ?>"
				   title="<?php __iboard_e( '답글' ); ?>"
				   class="iboard_button iboard_button_default"><?php __iboard_e( '답글' ); ?></a>
			<?php } ?>

			<?php if ( $this->isModifyAble() ) { ?>
				<a href="<?php echo $this->getModifyLink(); ?>"
				   title="<?php __iboard_e( '수정' ); ?>"
				   class="iboard_button iboard_button_default"><?php __iboard_e( '수정' ); ?></a>
			<?php } ?>

			<?php if ( $this->isDeleteAble() ) { ?>
				<a href="<?php echo $this->getDeleteLink(); ?>"
				   title="<?php __iboard_e( '삭제' ); ?>"
				   class="iboard_button iboard_button_default"><?php __iboard_e( '삭제' ); ?></a>
			<?php } ?>
		</div>
	</div>
</div>