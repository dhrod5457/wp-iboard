<?php
/**
 * Created by PhpStorm.
 * User: oks
 * Date: 15. 1. 11.
 * Time: 오후 7:32
 */

class NaverSyndicationUtil {
	public static function fromArray( $class, $args ) {
		$result = new ReflectionClass( $class );
		$result = $result->newInstance();

		$reflection = new ReflectionClass( $class );

		foreach ( $reflection->getProperties() as $var ) {
			if ( $var->isPublic() ) {
				$key = $var->getName();

				if ( isset( $args[ $key ] ) ) {
					$var->setValue( $result, $args[ $key ] );
				}
			}
		}
	}

	public static function xml2array( $xml, $out = array() ) {
		foreach ( (array) $xml as $index => $node ) {
			$out[ $index ] = ( is_object( $node ) ) ? self::xml2array( $node ) : $node;
		}

		return $out;
	}

	public static function generateDate( $date = null ) {
		if ( is_null( $date ) ) {
			$date = date( 'Y-m-d H:i:s' );
		}

		return mysql2date( 'Y-m-d\TH:i:s\Z', $date, false );
	}
}