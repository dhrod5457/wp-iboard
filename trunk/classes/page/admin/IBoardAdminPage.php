<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 22.
 * Time: 오후 10:15
 */
class IBoardAdminPage {
	function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	function validate_option( $options ) {
		$old_option = iboard_get_setting_option();
		$new_option = wp_parse_args( $options, $old_option );

		//검증후에 통과하지 못할경우에는 $old_option 을 리턴 하도록 해주면 된다.
		$result = apply_filters( 'iboard_admin_page_validate_option', $new_option, $old_option );

		return $result;
	}

	function get_submenu_list() {
		$args = array(
			array(
				'title'     => '게시판생성',
				'menu_slug' => 'iboard_bbs_create',
				'function'  => array( $this, 'admin_view2' )
			),
			array(
				'title'     => '글관리',
				'menu_slug' => 'iboard_bbs_edit_list',
				'function'  => array( $this, 'admin_view5' )
			),
			array(
				'title'     => '환경설정',
				'menu_slug' => 'iboard_bbs_setting',
				'function'  => array( $this, 'admin_view4' )
			),
			array(
				'title'     => '업그레이드',
				'menu_slug' => 'iboard_bbs_upgrade',
				'function'  => array( $this, 'admin_view3' )
			)
		);

		$args = apply_filters( 'iboard_admin_get_submenu_list', $args, $this );

		return $args;
	}

	function admin_init() {
		wp_enqueue_style( 'jquery-chosen' );
		wp_enqueue_script( 'jquery-chosen' );
		wp_enqueue_script( 'iboard-admin' );

		register_setting( 'iboard_setting', 'iboard_setting', array( $this, 'validate_option' ) );
	}

	function admin_menu() {
		global $submenu;

		add_menu_page( 'IBoard', 'IBoard', 'manage_options', 'iboard_bbs_admin', array( $this, 'admin_view1' ) );

		$sub_menu_list = $this->get_submenu_list();

		foreach ( $sub_menu_list as $sub_menu ) {
			add_submenu_page( 'iboard_bbs_admin', $sub_menu['title'], $sub_menu['title'], 'manage_options', $sub_menu['menu_slug'], $sub_menu['function'] );
		}

		if ( is_array( $submenu ) && isset( $submenu['iboard_bbs_admin'] ) ) {
			$submenu['iboard_bbs_admin'][0][0] = '게시판관리';
		}

		if ( $this->currentPage() == 'iboard_bbs_edit_list' ) {
			iboard_register_resources();
			iboard_page_init( null );
			iboard_enqueue_resources();
		}
	}

	function admin_view1() {
		$this->header();
		require_once IBOARD_PLUGIN_DIR . 'views/admin/view1.php';
		$this->footer();
	}

	function admin_view2() {
		$this->header();
		require_once IBOARD_PLUGIN_DIR . 'views/admin/view2.php';
		$this->footer();
	}

	function admin_view3() {
		$this->header();
		require_once IBOARD_PLUGIN_DIR . 'views/admin/upgrade.php';
		$this->footer();
	}

	function admin_view4() {
		$this->header();
		require_once IBOARD_PLUGIN_DIR . 'views/admin/setting.php';
		$this->footer();
	}

	function admin_view5() {
		$this->header();
		require_once IBOARD_PLUGIN_DIR . 'views/admin/edit-list.php';
		$this->footer();
	}

	function getCurrentUrl() {
		return get_admin_url( get_current_blog_id(), 'admin.php' ) . "?page=" . $this->currentPage();
	}

	function currentPage() {
		return iboard_request_param( 'page' );
	}

	function header() {
		$page = $this->currentPage();

		do_action( 'iboard_admin_header' );
		?>
		<div class="wrap">
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( $page == 'iboard_bbs_admin' ) : echo 'nav-tab-active'; endif; ?>"
			   href="admin.php?page=iboard_bbs_admin">게시판관리</a>

			<?php foreach ( $this->get_submenu_list() as $submenu ) : ?>
				<a class="nav-tab <?php if ( $page == $submenu['menu_slug'] ) : echo 'nav-tab-active'; endif; ?>"
				   href="admin.php?page=<?php echo $submenu['menu_slug'] ?>"><?php echo $submenu['title']; ?></a>
			<?php endforeach; ?>
		</h2>
		<?php
		do_action( 'iboard_admin_header_end' );
	}

	function footer() {
		do_action( 'iboard_admin_footer' );
		?>
		</div>
		<?php
		do_action( 'iboard_admin_footer_end' );
	}
}