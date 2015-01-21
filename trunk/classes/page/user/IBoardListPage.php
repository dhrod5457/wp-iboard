<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 23.
 * Time: 오전 1:12
 */
class IBoardListPage extends IBoardBasePage {
	/* @var IBoardItemList */
	public $boardList;

	/* @var IBoardAuthorizerResult */
	public $roles;

	public function view() {
		$searchType  = iboard_get_query_var( 'searchType', md5( uniqid() ) );
		$searchValue = iboard_get_query_var( 'searchValue', md5( uniqid() ) );

		$params = array(
			'boardSetting' => $this->boardSetting,
			$searchType    => $searchValue
		);

		$this->boardList = new IBoardItemList( wp_parse_args( $params, $this->args ) );

		$this->roles = $this->iboard_authorizer->getListRoles();

		if ( $this->roles->objects['list'] ) {
			$this->getView( 'list.php' );
		} else {
			$this->error = new IBoardError( "권한이 부족합니다." );
			$this->getView( 'error.php' );
		}
	}

	public function init( $args ) {

	}

	public function pagination() {
		$pagination = $this->boardList->pagination;
		ob_start();
		do_action( 'iboard_list_pagination_pre', $this );
		?>
		<div class="paging">
			<?php if ( $pagination->hasPrevBlock() ) { ?>
				<a class="paging_prev"
				   data-pno="<?php echo $pagination->prev_block; ?>"
				   title="<?php echo $pagination->prev_block ?> page"
				   href="<?php echo iboard_safe_link( "pageNo={$pagination->prev_block}" ) ?>"><?php __iboard_e( '이전' ); ?></a>
			<?php } ?>
			<?php for ( $i = $pagination->start_page; $i <= $pagination->end_page; $i ++ ) { ?>
				<a href="<?php echo iboard_safe_link( "pageNo={$i}" ); ?>" <?php if ( $pagination->pageNo == $i ) {
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
				   href="<?php echo iboard_safe_link( "pageNo={$pagination->next_block}" ) ?>"><?php __iboard_e( '다음' ); ?></a>
			<?php } ?>
		</div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();

		$content = apply_filters( 'iboard_list_pagination', $content );

		return $content;
	}

	public function isWriteAble() {
		return $this->boardSetting->isNonMemberEditAble() || $this->roles->objects['write'];
	}

	public function getWriteLink() {
		return iboard_safe_link( "pageMode=edit" );
	}

}