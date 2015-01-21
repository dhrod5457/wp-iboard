<?php
/**
 * Created by PhpStorm.
 * User: OKS
 * Date: 2014-12-22
 * Time: 오후 5:41
 */

function iboard_is_error( $arg ) {
	return $arg instanceof IBoardError;
}

function iboard_safe_hidden_inputs( $skip_names = array() ) {
	$p = parse_url( iboard_safe_link() );
	parse_str( $p['query'], $params );

	ob_start();

	foreach ( $params as $key => $value ) {
		if ( is_numeric( array_search( $key, $skip_names ) ) ) {
			continue;
		}
		?>
		<input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>"/>
	<?php
	}

	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}

function iboard_safe_link( $query = '', $u = '' ) {
	$iboard = IBoard::getInstance();

	$filter_args = array();

	if ( is_string( $query ) ) {
		$query = iboard_parse_params( $query );
	}

	$defaults = array(
		'pageNo'   => $iboard->get_query_var( 'pageNo' ),
		'pageMode' => $iboard->get_query_var( 'pageMode' ),
		'BID'      => $iboard->get_query_var( 'BID' )
	);

	$iboard_ID = iboard_request_param( 'iboard_ID' );
	if ( ! is_null( $iboard_ID ) ) {
		$defaults['iboard_ID'] = $iboard_ID;
	}

	$category = iboard_request_param( 'category' );
	if ( ! empty( $category ) ) {
		$defaults['category'] = $category;
	}

	$searchType  = iboard_request_param( 'searchType' );
	$searchValue = iboard_request_param( 'searchValue' );

	if ( ! empty( $searchType ) && ! empty( $searchValue ) ) {
		$defaults['searchType']  = $searchType;
		$defaults['searchValue'] = $searchValue;
	}

	$defaults = apply_filters( 'iboard_safe_link_pre', $defaults );

	$query = wp_parse_args( $query, $defaults );

	if ( is_array( $query ) ) {
		$query = http_build_query( $query );
	}

	$permalink = iboard_request_param( 'permalink', get_permalink() );

	$url = '';

	if ( ! empty( $permalink ) ) {
		$url = $permalink;
	}

	if ( ! empty( $u ) ) {
		$url = $u;
	}

	$filter_args['u']           = $url;
	$filter_args['queryString'] = $query;
	$filter_args['queryVars']   = iboard_parse_params( $query );

	$url .= iboard_get_query_prefix( $url ) . $query;

	$filter_args['result'] = $url;

	$url = apply_filters( 'iboard_safe_link', $filter_args );

	return $url['result'];
}

function iboard_get_query_prefix( $url ) {
	if ( ! strpos( $url, '?' ) ) {
		return "?";
	} else {
		return "&";
	}
}

function iboard_get_skin_list( $name ) {
	$result = array_diff( scandir( IBOARD_SKIN_DIR . '/' . $name ), array( '..', '.' ) );
	$result = apply_filters( 'iboard_get_skin_list', $result );

	return $result;
}

function iboard_get_plugin_list() {
	$result = array_diff( scandir( IBOARD_PLUGIN_MODULE_DIR ), array( '..', '.' ) );

	return $result;
}

function iboard_process_url( $mode, $params = array(), $url = null ) {
	$actionName = IBoardProcessInterceptor::ACTION_NAME;

	$defaults = array(
		$actionName => $mode
	);

	$params = wp_parse_args( $params, $defaults );

	return iboard_safe_link( $params, $url );
}

function iboard_process_comment_url( $mode, $params = array(), $url = null ) {
	$actionName = IBoardCommentProcessInterceptor::ACTION_NAME;

	$defaults = array(
		$actionName => $mode
	);

	$params = wp_parse_args( $params, $defaults );

	return iboard_safe_link( $params, $url );
}

function iboard_redirect( $url, $responseCode = 302 ) {
	if ( headers_sent() ) {
		echo '<style>body{display:none;}</style>';
		echo "<script>location.href='{$url}'</script>";
	} else {
		wp_redirect( $url, $responseCode );
	}

	die;
}

function iboard_alert( $message ) {
	if ( ! headers_sent() ) {
		header( "Content-Type: text/html; charset=UTF-8" );
	}

	echo "<script>alert('{$message}');history.back();</script>";
}

function iboard_parse_params( $input ) {
	if ( ! isset ( $input ) || ! $input ) {
		return array();
	}

	$pairs = explode( '&', $input );

	$parsed_parameters = array();
	foreach ( $pairs as $pair ) {
		$split     = explode( '=', $pair, 2 );
		$parameter = urldecode( $split [0] );
		$value     = isset ( $split [1] ) ? urldecode( $split [1] ) : '';

		if ( isset ( $parsed_parameters [ $parameter ] ) ) {
			if ( is_scalar( $parsed_parameters [ $parameter ] ) ) {
				$parsed_parameters [ $parameter ] = array(
					$parsed_parameters [ $parameter ]
				);
			}

			$parsed_parameters [ $parameter ] [] = $value;
		} else {
			$parsed_parameters [ $parameter ] = $value;
		}
	}

	return $parsed_parameters;
}

function iboard_get_editor_list() {
	$args = array(
		'wp_editor' => '워드프레스 에디터',
		'se'        => '네이버 에디터',
		'textarea'  => '기본 텍스트필드'
	);

	return apply_filters( 'iboard_get_editor_list', $args );
}

function iboard_get_editor( $page, $content, $name, $args = array() ) {
	$defaults = array(
		'type'        => 'wp_editor',
		'width'       => '100%',
		'height'      => '400px',
		'editor_name' => 'smart',
		'target_form' => 'iboardEditForm',
		'callback'    => ''
	);

	$args = wp_parse_args( $args, $defaults );
	$type = $args['type'];

	$width       = $args['width'];
	$height      = $args['height'];
	$editor_name = $args['editor_name'];
	$target_form = $args['target_form'];
	$callback    = $args['callback'];

	ob_start();

	if ( $page instanceof IBoardEditPage ) {
		do_action( 'iboard_editor_pre', $page, $content, $name );
	}

	if ( $type == 'wp_editor' ) {
		wp_editor( $content, $name, array() );
	} else if ( $type == 'se' ) {
		echo do_shortcode( "[naverse editor_name={$editor_name} width={$width} height={$height} name={$name} target_form={$target_form} callback={$callback}]" );
	} else if ( $type == 'textarea' ) {
		echo "<textarea id='{$name}' class='{$name}' name='{$name}' style='width:{$width};height:{$height};'>{$content}</textarea>";
	}

	if ( $page instanceof IBoardEditPage ) {
		do_action( 'iboard_editor_after', $page, $content, $name );
	}

	$result = ob_get_contents();
	ob_end_clean();

	return $result;
}

function iboard_request_user_var_trim( $value, $defaultValue ) {
	if ( is_array( $value ) ) {
		if ( count( $value ) == 0 ) {
			return null;
		}

		foreach ( $value as &$v ) {
			if ( strlen( trim( $v ) ) == 0 ) {
				$v = $defaultValue;
			}
		}

		return $value;
	} else {
		if ( strlen( trim( $value ) ) == 0 ) {
			return $defaultValue;
		} else {
			return $value;
		}
	}
}

function iboard_get_array_var( $array, $key, $defaultValue = null, $trim = false ) {
	if ( $trim ) {
		return array_key_exists( $key, $array ) ? iboard_request_user_var_trim( $array [ $key ], $defaultValue ) :
			$defaultValue;
	} else {
		return array_key_exists( $key, $array ) ? $array [ $key ] : $defaultValue;
	}
}

function iboard_request_post_param( $key, $defaultValue = null, $trim = false ) {
	return iboard_get_array_var( $_POST, $key, $defaultValue, $trim );
}

function iboard_request_get_param( $key, $defaultValue = null, $trim = false ) {
	return iboard_get_array_var( $_GET, $key, $defaultValue, $trim );
}

function iboard_request_param( $key, $defaultValue = null, $trim = false ) {
	return iboard_get_array_var( $_REQUEST, $key, $defaultValue, $trim );
}

function iboard_session_started() {
	$sid = session_id();

	return ! empty( $sid );
}

function iboard_session_set( $key, $value ) {
	if ( iboard_session_started() ) {
		$_SESSION[ $key ] = $value;
	} else {
		return false;
	}
}

function iboard_session_get( $key ) {
	if ( iboard_session_started() ) {
		if ( isset( $_SESSION[ $key ] ) ) {
			return $_SESSION[ $key ];
		}
	}
}

function iboard_session_del( $key ) {
	if ( iboard_session_started() ) {
		if ( isset( $_SESSION[ $key ] ) ) {
			unset( $_SESSION[ $key ] );
		}
	}
}

function iboard_get_query_var( $key, $defaultValue = '' ) {
	/* @var $iboard IBoard */
	global $iboard;

	return $iboard->get_query_var( $key, $defaultValue );
}

function iboard_is_empty( $value ) {
	return is_null( $value ) || trim( $value ) == '';
}

function iboard_get_query_vars() {
	global $iboard;

	return $iboard->query_vars;
}

function iboard_request_get_files( $name ) {
	$keys   = array( 'name', 'type', 'tmp_name', 'error', 'size' );
	$result = array();

	$count = count( @$_FILES[ $name ]['name'] );

	for ( $i = 0; $i < $count; $i ++ ) {
		$v = array();

		foreach ( $keys as $key ) {
			$v[ $key ] = $_FILES[ $name ][ $key ][ $i ];
		}

		$result[] = $v;
	}

	return $result;
}

function iboard_get_referer() {
	if ( is_null( $_SERVER['HTTP_REFERER'] ) ) {
		return get_home_url();
	} else {
		return $_SERVER['HTTP_REFERER'];
	}
}

function iboard_get_roles() {
	global $wp_roles;

	$roles['all']     = '전체공개';
	$roles['isLogin'] = '로그인 사용자';
	$roles            = array_merge( $roles, $wp_roles->get_names() );

	return $roles;
}

function iboard_chosen_select_box( $options, $values ) {
	foreach ( $options as $key => $value ) {
		?>
		<option value="<?php echo $key; ?>"
			<?php
			foreach ( $values as $v ) {
				if ( $v == $key ) {
					echo 'selected="selected"';
				}
			}
			?>>
			<?php echo $value; ?>
		</option> <?php
	}
}

function iboard_role_chosen_select_box( $roles ) {
	$role_all = iboard_get_roles();

	foreach ( $role_all as $key => $value ) {
		?>
		<option value="<?php echo $key; ?>"
			<?php
			foreach ( $roles as $role ) {
				if ( $role == $key ) {
					echo 'selected="selected"';
				}
			}
			?>>
			<?php echo $value; ?>
		</option> <?php
	}
}

function iboard_update_meta( $key, $value ) {
	return IBoardMetaService::getInstance()->update_meta( $key, $value );
}

function iboard_delete_meta( $key ) {
	return IBoardMetaService::getInstance()->delete_meta( $key );
}

function iboard_get_meta( $key, $defaultValue = null ) {
	return IBoardMetaService::getInstance()->metaValueFromKey( $key, $defaultValue );
}

function iboard_anti_xss( $str, $filterMethod, $filterPattern = null, $noHTMLTag = true ) {
	if ( ! class_exists( 'AntiXSS' ) ) {
		require_once IBOARD_PLUGIN_DIR . 'library/AntiXSS.php';
	}

	return AntiXSS::setFilter( $str, $filterMethod, $filterPattern, $noHTMLTag );
}

/**
 *
 * xss 필터와 아이프레임 걸르는 소스는 kboard_xssfilter 소스를 참고하였습니다.
 *
 */

function iboard_xss_filter( $data, $allow_hosts = array() ) {
	$purifier_path = IBOARD_PLUGIN_DIR . '/library/htmlpurifier';
	$cache_path    = $purifier_path . "/cache";

	if ( ! is_dir( $cache_path ) ) {
		mkdir( $cache_path, 0777, true );
	}

	if ( ! class_exists( 'HTMLPurifier' ) ) {
		include_once $purifier_path . '/HTMLPurifier.standalone.php';
		copy( $purifier_path . '/schema.txt', $purifier_path . '/standalone/HTMLPurifier/ConfigSchema/schema.ser' );
	}

	$config = HTMLPurifier_Config::createDefault();
	$config->set( 'HTML.SafeIframe', true );
	$config->set( 'URI.SafeIframeRegexp', '(.*)' );
	$config->set( 'HTML.TidyLevel', 'light' );
	$config->set( 'HTML.SafeObject', true );
	$config->set( 'HTML.SafeEmbed', true );
	$config->set( 'Attr.AllowedFrameTargets', array( '_blank' ) );
	$config->set( 'Output.FlashCompat', true );
	$config->set( 'Cache.SerializerPath', $cache_path );

	$data = HTMLPurifier::getInstance()->purify( $data, $config );

	return iboard_safe_iframe( $data, $allow_hosts );
}

function iboard_iframe_white_list() {
	$whilelist   = array();
	$whilelist[] = 'youtube.com';
	$whilelist[] = 'www.youtube.com';
	$whilelist[] = 'maps.google.com';
	$whilelist[] = 'maps.google.co.kr';
	$whilelist[] = 'serviceapi.nmv.naver.com';
	$whilelist[] = 'serviceapi.rmcnmv.naver.com';
	$whilelist[] = 'videofarm.daum.net';
	$whilelist[] = 'player.vimeo.com';
	$whilelist[] = 'w.soundcloud.com';
	$whilelist[] = 'slideshare.net';
	$whilelist[] = 'www.slideshare.net';

	return apply_filters( 'iboard_iframe_white_list', $whilelist );
}

function iboard_safe_iframe( $data, $allow_hosts = array() ) {
	/*
	 * 허가된 도메인 호스트 (화이트 리스트)
	 */
	$whilelist = iboard_iframe_white_list();
	$whilelist = array_merge( $whilelist, $allow_hosts );

	preg_match_all( '/<iframe.+?src="(.+?)".+?[^>]*+>/is', $data, $matches );

	$iframe = $matches[0];
	$domain = $matches[1];

	foreach ( $domain AS $key => $value ) {
		$value = 'http://' . preg_replace( '/^(http:\/\/|https:\/\/|\/\/)/i', '', $value );
		$url   = parse_url( $value );
		if ( ! in_array( $url['host'], $whilelist ) ) {
			$data = str_replace( $iframe[ $key ] . '</iframe>', '', $data );
			$data = str_replace( $iframe[ $key ], '', $data );
		}
	}

	return $data;
}

function iboard_get_banned_words() {
	$result = "시발,개객기,좆,젖,썅,호로,씹새끼,씨팔,시벌,씨벌,떠그랄,좆밥,8억,새끼,개새끼,소새끼,병신,지랄,씨팔,십팔,찌랄,지랄,쌍년,쌍놈,빙신,좆까,니기미,좆같은게,잡놈,벼엉신,바보새끼,추천인,추천id,추천아이디,추천id,추천아이디,추/천/인,쉐이,등신,싸가지,미친놈,미친넘,찌랄,죽습니다,님아,님들아,씨밸넘";

	return apply_filters( 'iboard_get_banned_words', $result );
}

function iboard_get_use_field_names() {
	$result = array(
		'email' => '이메일',
		'phone' => '휴대폰번호',
		'tel'   => '전화번호'
	);

	return apply_filters( 'iboard_get_use_field_names', $result );
}

function iboard_remove_banned_words( $string, $banned_words ) {
	$bannedList = explode( ',', $banned_words );

	foreach ( $bannedList as $banned_word ) {
		$string = str_replace( $banned_word, "***", $string );
	}

	return $string;
}

function iboard_admin_mail_template( $iboardItem ) {
	ob_start();
	require IBOARD_PLUGIN_DIR . "views/templates/admin_mail.php";
	$message = ob_get_contents();
	ob_end_clean();

	return $message;
}

function iboard_reply_mail_template( $replyItem, $parentItem ) {
	ob_start();
	require IBOARD_PLUGIN_DIR . "views/templates/reply_mail.php";
	$message = ob_get_contents();
	ob_end_clean();

	return $message;
}

function iboard_notification_admin_email( $iboardItem ) {
	add_filter( 'wp_mail_content_type', 'iboard_email_content_type_filter' );
	@wp_mail( get_option( 'admin_email' ), __iboard( 'IBoard 게시판에 새글이 등록되었습니다.' ), iboard_admin_mail_template( $iboardItem ) );
	remove_filter( 'wp_mail_content_type', 'iboard_email_content_type_filter' );
}

function iboard_reply_mail_send( $replyItem, $parentItem ) {
	if ( $parentItem instanceof IBoardItem && $replyItem instanceof IBoardItem ) {
		if ( $parentItem->hasEmail() ) {
			add_filter( 'wp_mail_content_type', 'iboard_email_content_type_filter' );
			@wp_mail( $parentItem->getUserEmail(), __iboard( 'IBoard 게시판에 답글이 등록되었습니다.' ), iboard_reply_mail_template( $replyItem, $parentItem ) );
			remove_filter( 'wp_mail_content_type', 'iboard_email_content_type_filter' );
		}
	}
}

function iboard_email_content_type_filter() {
	return 'text/html';
}

function iboard_now() {
	return date( 'Y-m-d H:i:s' );
}

function iboard_get_search_types() {
	$result = array(
		'subject' => '제목',
		'content' => '내용',
		'user_nm' => '작성자'
	);

	return apply_filters( 'iboard_get_search_types', $result );
}

function iboard_get_search_form() {
	$types       = iboard_get_search_types();
	$searchType  = iboard_get_query_var( 'searchType' );
	$searchValue = iboard_get_query_var( 'searchValue' );

	ob_start();
	?>
	<div class="iboard_search_wrap">
		<form name="iboard_search_form" action="<?php echo iboard_safe_link(); ?>" method="get"
		      class="iboard_search_form">
			<fieldset>
				<legend>검색 폼</legend>
				<select name="searchType" class="iboard_search_type">
					<option value="">전체</option>
					<?php foreach ( $types as $value => $title ) { ?>
						<option value="<?php echo $value ?>"
							<?php if ( $searchType == $value ) : echo 'selected="selected"';endif; ?>>
							<?php echo $title; ?>
						</option>
					<?php } ?>
				</select>
				<input type="text" name="searchValue" class="iboard_search_value"
				       value="<?php echo esc_html( $searchValue ); ?>"/>
				<input type="submit" value="검색" class="iboard_button iboard_button_default"/>

				<input type="hidden" name="pageNo" value="1"/>
				<?php echo iboard_safe_hidden_inputs( array( 'searchType', 'searchValue', 'pageNo' ) ); ?>
			</fieldset>
		</form>
	</div>
	<?php
	$result = ob_get_contents();
	ob_end_clean();

	return $result;
}

function iboard_get_setting_option( $key = null, $defaultValue = null ) {
	$option = get_option( 'iboard_setting', array() );

	if ( is_null( $key ) ) {
		return $option;
	}

	return iboard_get_array_var( $option, $key, $defaultValue );
}

function __iboard_setting_name( $name, $echo = true ) {
	$result = "iboard_setting[{$name}]";
	if ( $echo ) {
		echo $result;
	} else {
		return $result;
	}
}

function iboard_skin_dir_by_bid( $BID ) {
	return apply_filters( 'iboard_skinDir_' . $BID, IBOARD_SKIN_DIR );
}

function iboard_skin( $BID, $skin_type, $skin = '' ) {
	return apply_filters( "iboard_skin_{$skin_type}_{$BID}", $skin, $BID, $skin_type );
}

function iboard_skin_url( $BID, $skin_type, $skin = '' ) {
	return apply_filters( "iboard_skin_url_{$BID}", IBOARD_SKIN_URL . "/{$skin_type}/{$skin}" );
}

function iboard_error_message( IBoardError $error ) {
	return apply_filters( 'iboard_error_message', $error->message, $error );
}

