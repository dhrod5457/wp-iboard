<?php
/* @var $iboardItem IBoardItem */
?>
<div style="padding:10px 0;">
	<h5 style="font-size:20px;margin-bottom: 15px;">게시판에 새 글이 등록되었습니다.</h5>

	<p style="margin-bottom:10px;">글쓴이 : <?php echo $iboardItem->user_nm; ?></p>

	<p style="margin-bottom:10px;">글쓴이 이메일 : <?php echo $iboardItem->user_email; ?></p>

	<p style="margin-bottom:10px;">제목 : <?php echo $iboardItem->subject; ?></p>

	<p><a href="<?php echo iboard_safe_link( "pageMode=read&ID={$iboardItem->ID}" ); ?>">확인하러가기</a></p>
</div>