<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 22.
 * Time: 오전 12:11
 */
class IBoard {
	/* @var IBoardWpdb */
	private $db;

	/* @var IBoardVersion */
	public $version;

	/* @var array */
	public $query_vars = array();

	public $itemTable;

	public $settingTable;

	public $metaTable;

	public $commentTable;

	private static $instance;

	public static function getInstance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new IBoard();
		}

		return self::$instance;
	}

	public function __construct() {
		$this->db      = IBoardWpdb::getInstance();
		$this->version = IBoardVersion::getInstance();

		$this->initTableName();

		$this->createBoardTable();

		$this->request_init();

		$this->activate_iboard_modules();

		add_action( 'wp', array( $this, 'version_check' ) );
	}

	function version_check() {
		$this->downGradeCheck();
		$this->upgradeCheck();
	}


	function activate_iboard_modules() {
		$list = iboard_get_plugin_list();

//		iboard_register_error_handler();

		foreach ( $list as $value ) {
			$index = IBOARD_PLUGIN_MODULE_DIR . "/{$value}/index.php";

			if ( is_file( $index ) ) {
				require_once $index;

				do_action( "iboard_plugin_module_{$value}_init" );
			}
		}

		do_action( "iboard_plugin_module_init" );

//		iboard_un_register_error_handler();
	}

	public function upgradeCheck() {
		if ( $this->version->isUpgrade() ) {
			$newVersion = $this->version->currentVersion;
			update_option( 'iboard_old_version', $newVersion );

			do_action( 'iboard_upgrade', $this->version );
		}
	}

	public function downGradeCheck() {
		if ( $this->version->isDownGrade() ) {
			update_option( 'iboard_old_version', 0 );
			do_action( 'iboard_downgrade', $this->version );
		}
	}

	public function request_init() {
		$ID       = iboard_request_param( 'ID' );
		$pageMode = iboard_request_param( 'pageMode', 'list' );
		$password = iboard_request_param( 'password', null );
		$pageNo   = iboard_request_param( 'pageNo', 1 );
		$grp      = iboard_request_param( 'grp', null );
		$parent   = iboard_request_param( 'parent', null );
		$category = iboard_request_param( 'category', '' );

		$searchType  = iboard_request_param( 'searchType', null );
		$searchValue = iboard_request_param( 'searchValue', null );

		$this->set_query_var( 'ID', $ID );
		$this->set_query_var( 'pageMode', $pageMode );
		$this->set_query_var( 'password', $password );
		$this->set_query_var( 'pageNo', $pageNo );
		$this->set_query_var( 'grp', $grp );
		$this->set_query_var( 'parent', $parent );
		$this->set_query_var( 'category', $category );
		$this->set_query_var( 'searchType', $searchType );
		$this->set_query_var( 'searchValue', $searchValue );
	}

	public function set_query_var( $key, $value ) {
		$this->query_vars[ $key ] = $value;
	}

	public function get_query_var( $key, $defaultValue = null ) {
		if ( isset( $this->query_vars[ $key ] ) ) {
			return $this->query_vars[ $key ];
		}

		return $defaultValue;
	}

	public function oldTableName( $tableName ) {
		$pieces = explode( '_', $tableName );
		array_pop( $pieces );

		return implode( '_', $pieces ) . '_' . iboard_old_version();
	}

	public function initTableName() {
		$this->itemTable    = IBoardItemService::getInstance()->tableName;
		$this->metaTable    = IBoardMetaService::getInstance()->tableName;
		$this->settingTable = IBoardSettingService::getInstance()->tableName;
		$this->commentTable = IBoardCommentService::getInstance()->tableName;
	}

	public function createBoardTable() {
		$this->db->queryFromFile( IBOARD_PLUGIN_DIR . '/classes/setup/schema.sql', array(
			'itemTable'    => $this->itemTable,
			'settingTable' => $this->settingTable,
			'metaTable'    => $this->metaTable,
			'commentTable' => $this->commentTable
		) );
	}
} 