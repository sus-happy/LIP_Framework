<?php
/* -----------------------------
 汎用関数群
 /app/LIP/function/common.php
 --
 @written 12-11-30 SUSH
----------------------------- */

/* -----------------------------
 CMS設定データ読み込み
 Mixed config( $data )
 --
 @param String $data
 @param String $param
----------------------------- */
function config( $data, $param ) {
	$LIP =& get_instance();
	return $LIP->config->config( $data, $param, TRUE );
}

/* -----------------------------
 乱数の生成
 String make_nonce( $label )
 --
 @param String $label
----------------------------- */
function make_nonce( $label ) {
	return sha1( $label.mt_rand() );
}

/* -----------------------------
 これ何だったっけ…
 	@todo 思い出す
 Boolean is_authorized()
----------------------------- */
function is_authorized() {
	return ( LIP_AUTH_CHECKED == config( 'auth', 'key' ) );
}

/* -----------------------------
 サニタイズ
 	@todo サニタイズ言うな
 Mixed sanityze()
 --
 @param Mixed $obj
----------------------------- */
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