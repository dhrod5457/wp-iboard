<?php
function iboard_upgrade() {
	/* @var $iboard IBoard */
	global $iboard;

	iboard_model_to_db( 'IBoardItem', $iboard->itemTable );
	iboard_model_to_db( 'IBoardSetting', $iboard->settingTable );
	iboard_model_to_db( 'IBoardMeta', $iboard->metaTable );
	iboard_model_to_db( 'IBoardComment', $iboard->commentTable );
}

function iboard_get_annotation( $doc ) {
	if ( $doc ) {
		preg_match_all( '#@(.*?)\n#s', $doc, $annotations );
		$ann = $annotations[1];

		$result = array();

		foreach ( $ann as $annotationValue ) {
			$var = explode( "=", $annotationValue );

			$result[ trim( $var[0] ) ] = trim( $var[1] );
		}

		return $result;
	}
}

function iboard_model_to_db( $className, $tableName ) {
	$db = IBoardWpdb::getInstance();

	$reflection = new ReflectionClass( $className );

	foreach ( $reflection->getProperties() as $var ) {
		if ( $var->isPublic() ) {
			$key = $var->getName();

			$doc = $var->getDocComment();

			$annotation = iboard_get_annotation( $doc );

			if ( ! is_null( $annotation ) ) {
				$db->addColumn( $tableName, $key, $annotation['column'] );
			} else {
				$db->addColumn( $tableName, $key, 'VARCHAR (255) DEFAULT NULL' );
			}
		}
	}
}