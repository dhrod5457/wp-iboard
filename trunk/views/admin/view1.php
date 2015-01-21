<?php
$boardSettings = IBoardSettingService::getInstance()->getList();
?>
<h3>게시판 목록</h3>

<table class="wp-list-table widefat fixed pages">
	<colgroup>
		<col style="width:120px;"/>
		<col style="width:120px;"/>
		<col/>
		<col style="width:120px;"/>
	</colgroup>
	<thead>
	<tr>
		<th>ID</th>
		<th>게시판명</th>
		<th>숏코드</th>
		<th>관리</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ( $boardSettings as $setting ) { ?>
		<tr>
			<td><?php echo $setting->BID ?></td>
			<td><?php echo $setting->title ?></td>
			<td>
				<ul style="margin-top:0;">
					<li>일반 : [iboard bid="<?php echo $setting->BID ?>"]</li>
					<li>아이프레임 : [iboard_frame bid="<?php echo $setting->BID ?>"]</li>
					<li>최신글 : [iboard_latest bid="<?php echo $setting->BID ?>" url="주소" row="보여줄수"]</li>
				</ul>
			<td>
				<a href="?page=iboard_bbs_create&BID=<?php echo $setting->BID ?>" class="button">수정</a>
				<a href="#" data-id="<?php echo $setting->ID ?>" data-bid="<?php echo $setting->BID ?>" class="button deleteBoardSetting">삭제</a>
			</td>
		</tr>
	<?php } ?>
	</tbody>
</table>