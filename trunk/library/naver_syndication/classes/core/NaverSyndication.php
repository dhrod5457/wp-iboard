<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 15. 1. 11.
 * Time: 오후 7:30
 */
class NaverSyndication {
	public $token;

	/* @var NaverSyndicationClientInterface */
	public $client;

	/* @var NaverSyndicationServiceInterface */
	public $syndicationService;

	function __construct( $token, $client = null ) {
		$this->token = $token;

		if ( $client == null ) {
			$this->client = new DefaultNaverSyndicationClient();
		}
	}

	/**
	 * @param NaverSyndicationServiceInterface $syndicationService
	 */
	public function setSyndicationService( $syndicationService ) {
		$this->syndicationService = $syndicationService;
	}

	function setClient( NaverSyndicationClientInterface $client ) {
		$this->client = $client;
	}

	function validateCheck( $ping_url ) {
		if ( is_null( $this->client ) ) {
			throw new Exception( "syndication client is null" );
		}

		$response = $this->client->get( $ping_url );

		$xml = new DOMDocument();
		$xml->loadXML( $response );

		$xsd = NAVER_SYNDICATION_DIR_PATH . "/xsd/syndi.xsd";

		$validate = $xml->schemaValidate( $xsd );

		return $validate;
	}

	//error_code 값이 122 invalid_site 라면 정상 토큰입니다.
	function verifyToken() {
		return $this->send( 'http://webmastertool.naver.com/syndi/naver_syndication_document_sample.xml' );
	}

	function send( $ping_url ) {
		if ( is_null( $this->client ) ) {
			throw new Exception( "syndication client is null" );
		}

		if ( ! $this->validateCheck( $ping_url ) ) {
			return false;
		}

		$url = 'https://apis.naver.com/crawl/nsyndi/v2';

		$response = $this->client->post( $url, array(
			'ping_url' => $ping_url
		), array(
			"Authorization" => "Authorization:Bearer {$this->token}"
		) );

		$xml = simplexml_load_string( $response, null, LIBXML_NOCDATA );

		$result             = NaverSyndicationUtil::xml2array( $xml );
		$result['msg']      = $this->responseCodeToMessage( $result['error_code'] );
		$result['ping_url'] = $ping_url;

		if ( ! is_null( $this->syndicationService ) ) {
			$this->syndicationService->insertSyndication( $result );
		}

		return $result;
	}

	function responseCodeToMessage( $responseCode ) {
		$message_list = array(
			'000' => '인증에 성공하였습니다.',
			'024' => '인증 실패하였습니다.',
			'028' => 'OAuth Header 가 없습니다.',
			'029' => '요청한 Authorization값을 확인할 수 없습니다.',
			'030' => 'https 프로토콜로 요청해주세요.',
			'061' => '잘못된 형식의 호출 URL입니다.',
			'063' => '잘못된 형식의 인코딩 문자입니다.',
			'071' => '지원하지 않는 리턴 포맷입니다.',
			'120' => '전송된 내용이 없습니다. (ping_url 필요)',
			'121' => '유효하지 않은 parameter가 전달되었습니다.',
			'122' => '등록되지 않은 사이트 입니다.',
			'123' => '1일 전송 횟수를 초과하였습니다.',
			'130' => '서버 내부의 오류입니다. 재시도 해주세요.'
		);

		return $message_list[ $responseCode ];
	}
}