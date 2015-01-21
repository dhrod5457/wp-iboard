<?php
/* @var $this IBoardAdminPage */
$pagination = new IBoardPagination( array(
	'pageNo' => iboard_request_param( 'pageNo', 1 )
) );

$param = array(
	'offset'   => $pagination->start_record,
	'rowCount' => $pagination->page_per_record
);

$boardList = IBoardItemService::getInstance()->getList( $param );
$pagination->setTotalRecord( IBoardItemService::getInstance()->getListCount( $param ) );
?>

<h3>글관리</h3>

<form id="iboard_edit_list_form" method="get">
	<table class="widefat fixed comments">
		<thead>
		<tr>
			<th scope="col" id="cb" class="manage-column column-cb check-column" style="">
				<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
				<input id="cb-select-all-1" type="checkbox">
			</th>
			<th scope="col" class="manage-column column-author sortable desc">
				<a href="#">
					<span>작성자</span><span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col" style="width:100px;">게시판</th>
			<th scope="col" class="manage-column column-comment" style="">제목</th>
			<th scope="col" style="text-align: center;width:120px;">관리</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ( $boardList as $boardItem ) : ?>
			<tr>
				<th scope="row" class="check-column"><label class="screen-reader-text" for="cb-select-1">Select
						comment</label>
					<input id="cb-select-1" type="checkbox" name="ID[]" value="<?php echo $boardItem->ID; ?>">
				</th>
				<td class="author column-author">
					<strong>
						<?php echo $boardItem->getAvatar( 40 ); ?> <?php echo $boardItem->getUserNm(); ?>
					</strong>
				</td>
				<td>
					<?php echo $boardItem->BID; ?>
				</td>
				<td class="comment column-comment">
					<a href="<?php echo get_home_url(); ?>?iboard_ID=<?php echo $boardItem->BID; ?>&pageMode=read&ID=<?php echo $boardItem->ID; ?>"
					   target="_blank"><?php echo $boardItem->getSubject(); ?></a>
				</td>
				<td style="text-align: center;">
					<a href="<?php echo get_home_url(); ?>?iboard_ID=<?php echo $boardItem->BID; ?>&pageMode=edit&ID=<?php echo $boardItem->ID; ?>"
					   target="_blank">수정</a>
					<a href="<?php echo get_home_url(); ?>?iboard_ID=<?php echo $boardItem->BID; ?>&pageMode=delete&ID=<?php echo $boardItem->ID; ?>"
					   class="delete-board-item"
					   target="hiddenFrame">삭제</a>
					<a href="<?php echo get_home_url(); ?>?iboard_ID=<?php echo $boardItem->BID; ?>&pageMode=reply&parent=<?php echo $boardItem->ID; ?>&grp=<?php echo $boardItem->grp; ?>"
					   target="_blank">답글</a>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<div class="paging" style="text-align: center;margin:10px 0;">
		<?php if ( $pagination->hasPrevBlock() ) { ?>
			<a class="paging_prev" data-pno="<?php echo $pagination->prev_block; ?>"
			   title="<?php echo $pagination->prev_block ?> page"
			   href="<?php echo $this->getCurrentUrl() . "&pageNo={$pagination->prev_block}" ?>">
				<?php __iboard_e( '이전' ); ?>
			</a>
		<?php } ?>
		<?php for ( $i = $pagination->start_page; $i <= $pagination->end_page; $i ++ ) { ?>
			<a href="<?php echo $this->getCurrentUrl() . "&pageNo={$i}" ?>" <?php if ( $pagination->pageNo == $i ) {
				echo 'class="on"';
			} ?>
			   data-pno="<?php echo $i; ?>"
			   title="<?php echo $i ?> page"
				><?php echo $i; ?></a>
		<?php } ?>
		<?php if ( $pagination->hasNextBlock() ) { ?>
			<a class="paging_next"
			   data-pno="<?php echo $pagination->next_block; ?>"
			   title="<?php echo $pagination->next_block ?> page"
			   href="<?php echo $this->getCurrentUrl() . "&pageNo={$pagination->next_block}" ?>"><?php __iboard_e( '다음' ); ?></a>
		<?php } ?>
	</div>
</form>

<iframe frameborder="0" id="hiddenFrame" name="hiddenFrame" style="display: none;width:0;height:0;"></iframe>

<script>
	(function ($) {
		$(document).ready(function () {
			$('#hiddenFrame').load(function () {
				parent.location.reload();
			});

			$('#iboard_edit_list_form .delete-board-item').bind('click', function (e) {
				var url = $(this).attr('href');

				if (!confirm('정말로 삭제하시겠습니까?')) {
					e.preventDefault();
				}
			});
		});
	})(jQuery);
</script>