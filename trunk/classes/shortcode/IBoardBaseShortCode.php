<?php

/**
 * Created by PhpStorm.
 * User: oks
 * Date: 14. 12. 25.
 * Time: 오전 1:53
 */
abstract class IBoardBaseShortCode {
	public $permalink;

	/* @var IBoardBasePage */
	public $page;

	public abstract function shortCodeName();

	public abstract function getIdentityVar();

	public abstract function shortCodePre( $atts );

	public abstract function shortCodeAfter( $atts, $content = null );

	public function __construct() {
		add_filter( 'posts_results', array( $this, 'check' ) );
	}

	function check( $args ) {
		foreach ( $args as $post ) {
			$this->shortCodePrepare( $post );
		}

		return $args;
	}

	public function shortCodePrepare( $post ) {
		if ( $post instanceof WP_Post ) {
			if ( is_array( $this->getIdentityVar() ) ) {
				$atts   = '';
				$i      = 0;
				$length = count( $this->getIdentityVar() );
				foreach ( $this->getIdentityVar() as $id ) {
					$atts .= "{$id}=\"(.*)\"";

					if ( $i < $length - 1 ) {
						$atts .= ' ';
					}

					$i ++;
				}
			} else if ( is_string( $this->getIdentityVar() ) ) {
				$atts = "{$this->getIdentityVar()}=\"(.*)\"";
			}

			$pattern = "/\[{$this->shortCodeName()} {$atts}/";

			preg_match( $pattern, $post->post_content, $matches );

			if ( count( $matches ) > 1 ) {
				$this->permalink = get_permalink( $post->ID );
				$this->shortCodePre( $matches );

				do_action( 'iboard_shortcode_register_pre', $this );
				do_action( 'iboard_action_shortcode_register', $this->shortCodeName(), $this );
				do_action( 'iboard_action_shortcode_register_' . $this->shortCodeName(), $this );
			}

			if ( ! shortcode_exists( $this->shortCodeName() ) ) {
				add_shortcode( $this->shortCodeName(), array( $this, 'shortCodeAfter' ) );
			}
		}
	}
}