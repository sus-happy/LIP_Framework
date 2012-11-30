<?php
/*
 * 汎用関数群
 * /app/LIP/function/common.php
 */

/* CMS設定データ読み込み */
function config( $data, $param ) {
	$LIP =& get_instance();
	return $LIP->config->config( $data, $param, TRUE );
}

function make_nonce( $label ) {
	return sha1( $label.mt_rand() );
}

function is_authorized() {
	return ( LIP_AUTH_CHECKED == config( 'auth', 'key' ) );
}

function sanityze( $obj ) {
	if( is_array( $obj ) || is_object( $obj ) ) {
		foreach( $obj as $key => $val ) {
			$obj[$key] = sanityze( $val );
		}
		return $obj;
	} else {
		return htmlspecialchars( $obj, ENT_QUOTES );
	}
}