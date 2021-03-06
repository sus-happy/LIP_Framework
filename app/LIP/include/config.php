<?php
/* -----------------------------
 LIP_Config : 設定ファイル読み込みクラス
 /app/LIP/include/config.php
 --
 @written 12-11-30 SUSH
----------------------------- */

class LIP_Config extends LIP_Object {
	private $c_data = array();

	/* -----------------------------
	 コンストラクタ
	 Void __construct()
	----------------------------- */
	public function __construct() {
		global $config;
		$this->c_data = $config;
	}

	/* -----------------------------
	 設定情報読み込み
	 	基本的に設定ファイル上で「hide」に入れたものは取得させない
	 Mixed config()
	 --
	 @param String $data
	 @param String $param
	 	上書き
	 @param Boolean $outer
	 	TRUEの時は全ての内容にアクセス出来る
	----------------------------- */
	public function config( $data, $param = NULL, $outer = FALSE ) {
		if( $outer ) {
			if( in_array( $data, $this->c_data["hide"] ) )
				return FALSE;
		}
		if (! empty( $param ) )
			return $this->c_data[$data][$param];
		else
			return $this->c_data[$data];
	}

	/* -----------------------------
	 CMSルートディレクトリパス
	 String base_dir()
	----------------------------- */
	public function base_dir() {
		return $this->config( "system", "base_dir" );
	}

	/* -----------------------------
	 CMSアプリケーションディレクトリパス
	 String app_dir()
	----------------------------- */
	public function app_dir() {
		return $this->config( "system", "app_dir" );
	}

	/* -----------------------------
	 CMSルートURL
	 String base_url()
	----------------------------- */
	public function base_url() {
		return $this->config( "system", "base_url" );
	}

	/* -----------------------------
	 CMS内URL生成
	 String site_url( $path=NULL )
	 --
	 @param String $path
	 	ルートからの相対パス
	----------------------------- */
	public function site_url( $path=NULL ) {
		if(! empty( $path ) ) {
			switch( $this->config("site", "analyze") ) {
				case "PATH_INFO":
					return sprintf( "%sindex.php/%s", $this->base_url(), $path );
				break;
				case "MOD_REWRITE":
					return sprintf( "%s%s", $this->base_url(), $path );
				break;
			}
			return FALSE;
		} else
			return $this->base_url();
	}

	/* -----------------------------
	 リダイレクト
	 Void redirect( $path=NULL )
	 --
	 @param String $path
	 	ルートからの相対パス
	----------------------------- */
	public function redirect( $path=NULL ) {
		header( sprintf("Location:%s", $this->site_url( $path ) ) );
		exit();
	}

	/* -----------------------------
	 テンプレートファイル取得
	 	ファイルが存在しない場合は
	 	/app/view/404.php -> /app/LIP/view/404.php
	 	の順で読みに行く
	 String get_template( $path, $ext = "php" )
	 --
	 @param String $path
	 @param String $ext
	 @todo ちょっとセキュリティ的に怪しいかも。
	----------------------------- */
	public function get_template( $path, $ext = "php" ) {
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