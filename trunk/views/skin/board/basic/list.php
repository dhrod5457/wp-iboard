<?php
/* @var $this IBoardListPage */
?>
<div class="iboardList iboard iboard_<?php echo iboard_get_query_var( 'BID' ); ?>">
	<?php if ( $this->boardSetting->hasCategory() ) { ?>
		<div class="iboard_category_nav">
			<ul>
				<li class="<?php if ( iboard_is_empty( iboard_request_param( 'category' ) ) ) { ?>on<?php } ?>">
					<a href="<?php echo iboard_safe_link( "category=" ); ?>" title="전체">전체</a>
				</li>
				<?php foreach ( $this->boardSetting->getCategories() as $category ) { ?>
					<li class="<?php if ( iboard_request_param( 'category' ) == $category ) { ?>on<?php } ?>">
						<a href="<?php echo iboard_safe_link( "pageNo=1&category=" . $category ); ?>"
						   title="<?php echo $category; ?>"><?php echo $category; ?></a>
					</li>
				<?php } ?>
			</ul>
		</div>
	<?php } ?>

	<?php echo iboard_get_search_form(); ?>

	<table class="iboard_table">
		<caption class="iboard_caption"><?php echo $this->boardSetting->title ?></caption>
		<thead>
		<tr>
			<th class="iboard_no" scope="col">NO</th>
			<th class="iboard_title" scope="col"><?php __iboard_e( '제목' ); ?></th>
			<th class="iboard_user_nm" scope="col"><?php __iboard_e( '작성자' ); ?></th>
			<th class="iboard_date" scope="col"><?php __iboard_e( '작성일' ); ?></th>
			<th class="iboard_read" scope="col"><?php __iboard_e( '조회' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ( $this->boardList->items as $board ) { ?>
			<tr class="<?php if ( $board->isNotice() ) : echo 'isNotice'; endif; ?>">
				<td class="iboard_no"><?php echo $this->boardList->pagination->start_no --; ?></td>
				<td class="iboard_title iboard_text_over">
					<a href="<?php echo $board->getReadLink(); ?>"
					   title="<?php __iboard_e( sprintf( '%s 읽으러가기', $board->subject ) ); ?>">
						<?php if ($board->isReply()) { ?>
						<span class="iboard_reply">
						<?php for ( $i = 0; $i < $board->depth; $i ++ ) {
							echo '<i class="iboard_re">[re]</i>';
						} ?>
						<?php } ?>
						</span>
						<?php if ( $board->isNotice() ) { ?>
							<span class="iboard_notice">[공지]</span>
						<?php } ?>
						<span class="iboard_subject"><?php echo $board->getSubject(); ?></span>
						<?php if ( $board->hasComment() ) { ?>
							<span class="iboard_comment_cnt">(<?php echo $board->getCommentCnt(); ?>)</span>
						<?php } ?>
					</a>
				</td>
				<td class="iboard_user_nm iboard_text_over">
					<?php echo $board->getUserNm(); ?>
				</td>
				<td class="iboard_date">
					<?php echo $board->getRegDate(); ?>
				</td>
				<td class="iboard_read">
					<?php echo $board->getReadCnt(); ?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

	<?php _e( $this->pagination() ); ?>

	<div class="buttons">
		<div class="buttons_right">
			<?php if ( $this->isWriteAble() ) { ?>
				<a href="<?php echo $this->getWriteLink(); ?>"
				   title="<?php __iboard_e( '글쓰기' ); ?>"
				   class="iboard_button iboard_button_default"><?php __iboard_e( '글쓰기' ); ?></a>
			<?php } ?>
		</div>
	</div>
</div>