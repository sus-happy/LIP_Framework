<?php
/*
 * 初期読み込みクラス
 * /app/LIP/include/boot.php
 * --
 * PATH_INFOを解析してコントローラーに割り当てる
 * index.php/control/func/val1/val2...
 */

class LIP_Boot extends LIP_Object {
	private $control,
			$LIP,
			$file,
			$class;

	/* ####################################
	   PUBLIC FUNCTION
	#################################### */
	public function __construct() {
		$this->LIP =& get_instance();

		/* 設定ファイル読み込み */
		$this->LIP->set_method( 'config', new LIP_Config() );
		/* ライブラリローダー追加 */
		$this->LIP->set_method( 'load', new LIP_Load() );
		/* URL解析追加 */
		$this->LIP->set_method( 'url', new LIP_Url() );
		/* アクションフック追加 */
		$this->LIP->set_method( 'hook', new LIP_Hook() );

		/* PEAR::MDB2 */
		if( $this->LIP->config->config("database", "enable") ) {
			/* データベースクラス追加 */
			require_once( app_dir().'/LIP/include/database.php' );
			$this->LIP->set_method( 'db', new LIP_Database( $this->LIP->config->config( 'database' ) ) );
		}
		/* PEAR::MDB2 */

		$this->use_plugin();
		$this->use_library();

		if( RIP_AUTO_CONTROL === TRUE )
			$this->LIP->url->url_analyze();

		switch( $this->LIP->url->check_auth() ) {
			case "LOGIN":
				// It is Logined :D
				run_hook( 'AUTH_SUCCESS' );
			break;
			case "NO_LOGIN":
				redirect( "login" );
				return;
			break;
			case "NO_INSTALL":
				redirect( "install" );
				return;
			break;
		}

		if( RIP_AUTO_CONTROL === TRUE )
			return $this->LIP->url->get_control();
		return TRUE;
	}

	/* ####################################
	   PRIVATE FUNCTION
	#################################### */
	/*
	 * void use_plugin()
	 * プラグインファイル読み込み
	 */
	private function use_plugin() {
		$dir = config( "plugin", "dir" );
		foreach ( config( "plugin", "use" ) as $value ) {
			$fname = sprintf( "%s/%s.php", $dir, $value );
			if( file_exists( $fname ) )
				require_once( $fname );
		}
	}

	/*
	 * void use_library()
	 * ライブラリーファイル読み込み
	 */
	private function use_library() {
		foreach ( config( "library", "use" ) as $value ) {
			load_library( $value );
		}
	}
}