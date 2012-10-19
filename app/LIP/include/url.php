<?php
/*
 * URL解析クラス
 * /app/LIP/include/url.php
 */

class LIP_Url extends LIP_Object {
	private $mode, $func, $class, $param;
	
	public function __construct() {
	}

	public function get_func() {
		return $this->func;
	}

	public function get_mode() {
		return $this->mode;
	}
	
	/*
	 * void url_analyze()
	 * URLを解析 -> Controlerに渡す
	 */
	public function url_analyze() {
		switch( config("site", "analyze") ) {
			case "PATH_INFO":
				$url = getenv("PATH_INFO");
			break;
			case "MOD_REWRITE":
				$url = str_replace( base_dir(), "", $_SERVER["REQUEST_URI"] );
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

	/*
	 * class get_control( $mode=NULL, $func=NULL )
	 * Controlerクラスを取得
	 * $mode:クラス名、$func:メソッド名
	 */
	public function get_control( $mode=NULL, $func=NULL ) {
		if( $mode ) $this->mode = $mode;
		if( $func ) $this->func = $func;

		$param = explode( ".", $this->mode );
		$this->mode = implode( "/", $param );
		$this->class = "LC_".implode( "_", array_map( "ucfirst", $param ) );
		
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
		if( $this->func )
			$cls->load_func( $this->func, $this->param );
		return $cls;
	}
	
	/*
	 * boolean check_auth()
	 * 認証確認 セッション（user_id）が保存されているか否か？
	 */
	public function check_auth() {
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
		
		if( $ss = load_library( "session" ) ) {
			if( $ss->get_session("user_id") || LIP_AUTH_DEBUG_MODE === TRUE ) {
				define( 'LIP_AUTH_CHECKED', config( 'auth', 'key' ) );
				return "LOGIN";
			}
		}
		return "NO_LOGIN";
	}
}