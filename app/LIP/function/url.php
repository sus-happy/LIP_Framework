<?php
/*
 * URL関数群
 * /app/LIP/function/url.php
 */

/* CMSルートディレクトリパス */
function base_dir() {
	$LIP =& get_instance();
	return $LIP->config->base_dir();
}

/* CMSアプリケーションディレクトリパス */
function app_dir() {
	$LIP =& get_instance();
	return $LIP->config->app_dir();
}

/* CMSルートディレクトリURL */
function base_url() {
	$LIP =& get_instance();
	return $LIP->config->base_url();
}

/* CMS内URL生成 */
function site_url( $path=NULL ) {
	$LIP =& get_instance();
	return $LIP->config->site_url( $path );
}

/* リダイレクト */
function redirect( $path=NULL ) {
	$LIP =& get_instance();
	return $LIP->config->redirect( $path );
}

/* テンプレートファイル取得 */
function get_template( $path, $ext = "php" ) {
	$LIP =& get_instance();
	return $LIP->config->get_template( $path, $ext );
}

function get_mode() {
	$LIP =& get_instance();
	return $LIP->url->get_mode();
}

function get_func() {
	$LIP =& get_instance();
	return $LIP->url->get_func();
}