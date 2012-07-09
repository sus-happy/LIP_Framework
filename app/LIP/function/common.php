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

function load_library( $library ) {
	$LIP =& get_instance();
	return $LIP->load->load_library( $library );
}

function make_nonce( $label ) {
	return sha1( $label.mt_rand() );
}