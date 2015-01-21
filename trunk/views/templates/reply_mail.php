<?php
/* @var $replyItem IBoardItem */
/* @var $parentItem IBoardItem */
?>
<div style="padding:10px 0;">
	<h5 style="font-size:20px;margin-bottom: 15px;">게시글에 답글이 등록되었습니다.</h5>

	<p style="margin-bottom:10px;">답변글 글쓴이 : <?php echo $replyItem->user_nm; ?></p>

	<p style="margin-bottom:10px;">답변글 글쓴이 이메일 : <?php echo $replyItem->user_email; ?></p>

	<p style="margin-bottom:10px;">답변글 제목 : <?php echo $replyItem->subject; ?></p>

	<p><a href="<?php echo iboard_safe_link( "pageMode=read&ID={$replyItem->ID}" ); ?>">확인하러가기</a></p>
</div>