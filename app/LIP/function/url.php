<?php
/* -----------------------------
 URL関数群
 /app/LIP/function/url.php
 --
 @written 12-11-30 SUSH
----------------------------- */

/* -----------------------------
 CMSルートディレクトリパス
 String base_dir()
 --
 @see LIP_Config->base_dir()
----------------------------- */
function base_dir() {
	$LIP =& get_instance();
	return $LIP->config->base_dir();
}

/* -----------------------------
 CMSアプリケーションディレクトリパス
 String app_dir()
 --
 @see LIP_Config->app_dir()
----------------------------- */
function app_dir() {
	$LIP =& get_instance();
	return $LIP->config->app_dir();
}

/* -----------------------------
 CMSルートURL
 String base_url()
 --
 @see LIP_Config->base_url()
----------------------------- */
function base_url() {
	$LIP =& get_instance();
	return $LIP->config->base_url();
}

/* -----------------------------
 CMS内URL生成
 String site_url( $path=NULL )
 --
 @param String $path
 	ルートからの相対パス
 @see LIP_Config->site_url()
----------------------------- */
function site_url( $path=NULL ) {
	$LIP =& get_instance();
	return $LIP->config->site_url( $path );
}

/* -----------------------------
 リダイレクト
 Void redirect( $path=NULL )
 --
 @param String $path
 	ルートからの相対パス
 @see LIP_Config->redirect()
----------------------------- */
function redirect( $path=NULL ) {
	$LIP =& get_instance();
	$LIP->config->redirect( $path );
}

/* -----------------------------
 テンプレートファイル取得
 String get_template( $path, $ext = "php" )
 --
 @param String $path
 	ルートからの相対パス
 @param String $ext
 	取得するファイルの拡張子
 @see LIP_Config->get_template()
----------------------------- */
function get_template( $path, $ext = "php" ) {
	$LIP =& get_instance();
	return $LIP->config->get_template( $path, $ext );
}

/* -----------------------------
 読込中のコントローラ名取得
 String get_mode()
 --
 @see LIP_Url->get_mode()
----------------------------- */
function get_mode() {
	$LIP =& get_instance();
	return $LIP->url->get_mode();
}

/* -----------------------------
 読込中のコントローラ内関数名取得
 String get_func()
 --
 @see LIP_Url->get_func()
----------------------------- */
function get_func() {
	$LIP =& get_instance();
	return $LIP->url->get_func();
}