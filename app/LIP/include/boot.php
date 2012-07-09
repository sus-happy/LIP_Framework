<?php
/*
 * 初期読み込みクラス
 * /app/LIP/include/boot.php
 * --
 * PATH_INFOを解析してコントローラーに割り当てる
 * index.php/control/func/val1/val2...
 */

class LIP_Boot extends LIP_Object {
	var $control, $mode, $func, $param, $file, $class;
	function LIP_Boot() {
		$LIP =& get_instance();
		
		/* 設定ファイル読み込み */
		$LIP->config = new LIP_Config();
		/* ライブラリローダー追加 */
		$LIP->load = new LIP_Load();

		/* PEAR::MDB2 */
		if( $LIP->config->config("database", "enable") ) {
			set_include_path(get_include_path() .PATH_SEPARATOR. $LIP->config->config("system", "app_dir"). "/LIP/include/PEAR/" );
			require_once 'MDB2.php';
			$LIP->db = MDB2::connect(
				sprintf( '%s://%s:%s@%s/%s?charset=%s',
					$LIP->config->config("database", "type"),
					$LIP->config->config("database", "user"),
					$LIP->config->config("database", "pass"),
					$LIP->config->config("database", "host"),
					$LIP->config->config("database", "dbname"),
					$LIP->config->config("database", "charset")
				)
			);
		}
		/* PEAR::MDB2 */

		$this->use_plugin();
		$this->use_library();
		
		if( RIP_AUTO_CONTROL === TRUE )
			$this->url_analyze();
		
		switch( $this->check_auth() ) {
			case "LOGIN":
				// It is Logined :D
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
			$this->get_control();
	}

	/*
	 * void use_plugin()
	 * プラグインファイル読み込み
	 */
	function use_plugin() {
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
	function use_library() {
		foreach ( config( "library", "use" ) as $value ) {
			load_library( $value );
		}
	}
	
	/*
	 * void url_analyze()
	 * PATH_INFOを解析
	 */
	function url_analyze() {
		switch( config("site", "analyze") ) {
			case "PATH_INFO":
				$url = $_SERVER["PATH_INFO"];
			break;
			case "MOD_REWRITE":
				$url = $_SERVER["REQUEST_URI"];
			break;
		}
		if(! empty( $url ) && $url !== "/" ) {
			$this->param = explode( "/", $url );
			array_shift( $this->param );
			$this->mode = array_shift( $this->param );
			$this->func = array_shift( $this->param );
			if( empty( $this->func ) )
				$this->func = config( "index", "func" );
		} else {
			$this->mode = config( "index", "path" );
			$this->func = config( "index", "func" );
		}
	}
	
	
	function check_auth() {
		$pass = config( "auth", "check" );
		if( $pass ) { foreach( $pass as $p ) {
			if(! preg_match( $p[0], $this->mode ) && $p[0] !== "*" ) {
				return "LOGIN";
			} else {
				if (! preg_match( $p[1], $this->func ) && $p[1] !== "*" ) {
					return "LOGIN";
				}
			}
		} } else return "LOGIN";
		
		/*$ss = new LIP_Session( config("session", "sess_cookie_name") );
		if( $ss->get_session("user_id") ) {
			return "LOGIN";
		}*/
		return "NO_LOGIN";
	}

	function get_control( $mode=NULL, $func=NULL ) {
		if( $mode ) $this->mode = $mode;
		if( $func ) $this->func = $func;

		$this->mode_convert();
		
		$file = sprintf( "%s/control/%s.php", app_dir(), $this->mode );
		if(! file_exists($file) ) {
			$file = sprintf( "%s/LIP/control/%s.php", app_dir(), $this->mode );
			if(! file_exists($file) ) {
				$this->mode = "notfound";
				$this->class = "LC_".ucfirst( $this->mode );
				$this->func = "index";
				$file = sprintf( "%s/LIP/control/404.php", app_dir() );
			}
		}
		
		require_once( $file );
		$cls = new $this->class();
		$cls->load_func( $this->func, $this->param );
		return $cls;
	}

	function mode_convert() {
		$param = explode( ".", $this->mode );
		$this->mode = implode( "/", $param );
		$this->class = "LC_".implode( "_", array_map( "ucfirst", $param ) );
	}
}