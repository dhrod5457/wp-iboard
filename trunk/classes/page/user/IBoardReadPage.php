<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 23.
 * Time: 오전 1:13
 */
class IBoardReadPage extends IBoardBasePage {
	/* @var IBoardItem */
	public $boardItem;

	/* @var IBoardItemService */
	public $boardService;

	/* @var IBoardCommentPage */
	public $commentPage;

	public function init( $args ) {
		$this->boardService = IBoardItemService::getInstance();
	}

	public function commentList() {
		return $this->commentPage->render();
	}

	public function updateReadCount() {
		$this->boardService->updateReadCount( $this->boardItem, $this->boardSetting->BID );
	}

	public function view() {
		$result = $this->iboard_authorizer->getReadRole();

		if ( iboard_has_authorize_error( $result ) ) {
			if ( $result instanceof IBoardAuthorizerError ) {
				if ( $result->message == IBoardAuthorizerError::PASSWORD_INCORRECT ) {
					$this->getView( 'password.php' );
				} else {
					$this->error = new IBoardError( __iboard( $result->message ) );
					$this->getView( 'error.php' );
				}
			}
		} else {
			$this->boardItem   = $result->objects;
			$this->commentPage = new IBoardCommentPage( array(
				'iboardItem' => $result->objects,
				'BID'        => $this->boardSetting->BID
			) );

			$this->updateReadCount();
			$this->getView( 'read.php' );
		}
	}

	public function navigation( $args = array() ) {
		$defaults = array(
			'msg_next'       => __iboard( '다음글' ),
			'msg_prev'       => __iboard( '이전글' ),
			'msg_next_empty' => __iboard( '다음글이 없습니다.' ),
			'msg_prev_empty' => __iboard( '이전글이 없습니다.' )
		);

		$args = wp_parse_args( $args, $defaults );

		$args = apply_filters( "iboard_navigation_pre", $args, $this );
		$args = apply_filters( "iboard_navigation_pre_{$this->boardSetting->BID}", $args, $this );

		$nav = $this->boardItem->getNav( array(
			'category' => iboard_request_param( 'category' )
		) );

		ob_start();

		do_action( 'iboard_navigation_pre' );
		?>
		<dl class="iboard_navigation">
			<dt class="next"><?php echo $args['msg_next']; ?></dt>
			<dd class="next">
				<?php if ( is_null( $nav['next'] ) ) { ?><?php echo $args['msg_next_empty']; ?><?php } else { ?>
					<a href="<?php echo iboard_safe_link( 'pageMode=read&ID=' . $nav['next']->ID ); ?>">
						<?php echo $nav['next']->subject; ?>
					</a>
				<?php } ?>
			</dd>
			<dt class="prev"><?php echo $args['msg_prev']; ?></dt>
			<dd class="prev">
				<?php if ( is_null( $nav['prev'] ) ) { ?><?php echo $args['msg_prev_empty']; ?><?php } else { ?>
					<a href="<?php echo iboard_safe_link( 'pageMode=read&ID=' . $nav['prev']->ID ); ?>">
						<?php echo $nav['prev']->subject; ?>
					</a>
				<?php } ?>
			</dd>
		</dl>
		<?php
		do_action( 'iboard_navigation_after' );

		$content = ob_get_contents();
		ob_end_clean();

		$content = apply_filters( 'iboard_navigation_content', $content );

		return $content;
	}

	public function isModifyAble() {
		return $this->iboard_authorizer->getUpdateRole()->result || $this->boardSetting->isNonMemberEditAble();
	}

	public function isDeleteAble() {
		return $this->iboard_authorizer->getDeleteRole()->result || $this->boardSetting->isNonMemberEditAble();
	}

	public function isWriteAble() {
		return $this->iboard_authorizer->getInsertRole() || $this->boardSetting->isNonMemberEditAble();
	}

	public function getWriteLink() {
		return iboard_safe_link( "pageMode=reply&parent={$this->boardItem->ID}&grp={$this->boardItem->grp}" );
	}

	public function getDeleteLink() {
		return iboard_safe_link( array(
			'pageMode' => 'delete',
			'ID'       => $this->boardItem->ID
		) );
	}

	public function getModifyLink() {
		return iboard_safe_link( "pageMode=edit&ID={$this->boardItem->ID}" );
	}

	public function getBackLink() {
		return iboard_safe_link( 'pageMode=list' );
	}
}