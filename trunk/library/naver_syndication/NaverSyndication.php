<?php
/**
 *
 */


define( 'NAVER_SYNDICATION_DIR_PATH', dirname( __FILE__ ) );

$class_path = NAVER_SYNDICATION_DIR_PATH . "/classes";

require_once $class_path . "/core/client/NaverSyndicationClientInterface.php";
require_once $class_path . "/core/client/DefaultNaverSyndicationClient.php";

require_once $class_path . "/core/model/NaverSyndicationModelInterface.php";
require_once $class_path . "/core/model/NaverSyndicationEntry.php";
require_once $class_path . "/core/model/NaverSyndicationFeed.php";

require_once $class_path . "/core/service/NaverSyndicationServiceInterface.php";

require_once $class_path . "/core/util/NaverSyndicationUtil.php";

require_once $class_path . "/core/NaverSyndication.php";

require_once $class_path . "/wp/WPNaverSyndicationFeed.php";
require_once $class_path . "/wp/WPNaverSyndicationService.php";