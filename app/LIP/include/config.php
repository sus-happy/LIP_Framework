<?php
/*
 * 設定ファイル読み込みクラス
 * /app/LIP/include/config.php
 */

class LIP_Config {
	var $c_data = array();
	function LIP_Config() {
		global $config;
		$this->c_data = $config;
	}
	function config( $data, $param, $outer = FALSE ) {
		if( $outer ) {
			if( in_array( $data, $this->c_data["hide"] ) )
				return FALSE;
		}
		return $this->c_data[$data][$param];
	}

	
	/* CMSルートディレクトリパス */
	function base_dir() {
		return $this->config( "system", "base_dir" );
	}

	/* CMSアプリケーションディレクトリパス */
	function app_dir() {
		return $this->config( "system", "app_dir" );
	}

	/* CMSルートディレクトリURL */
	function base_url() {
		return $this->config( "system", "base_url" );
	}

	/* CMS内URL生成 */
	function site_url( $path=NULL ) {
		if(! empty( $path ) ) {
			switch( $this->config("site", "analyze") ) {
				case "PATH_INFO":
					return sprintf( "%sindex.php/%s", $this->base_url(), $path );
				break;
				case "MOD_REWRITE":
					return sprintf( "%s%s/", $this->base_url(), $path );
				break;
			}
			return FALSE;
		} else
			return $this->base_url();
	}

	/* リダイレクト */
	function redirect( $path=NULL ) {
		header( sprintf("Location:%s", $this->site_url( $path ) ) );
		exit();
	}

	/*
	 * テンプレートファイル取得
	 * ファイルが存在しない場合は
	 * "/app/view/404.php" -> "/app/LIP/view/404.php"
	 * の順で読みに行く
	 * 
	 * @todo ちょっとセキュリティ的に怪しいかも。
	 */
	function get_template( $path, $ext = "php" ) {
		$file = sprintf( "%s/view/%s.%s", $this->app_dir(), $path, $ext );
		if(! file_exists( $file ) ) {
			$file = sprintf( "%s/LIP/view/%s.%s", $this->app_dir(), $path, $ext );
			if(! file_exists( $file ) ) {
				if( $path != "404" ) {
					$file = $this->get_template( "404" );
				} else return FALSE;
			}
		}
		return $file;
	}
}