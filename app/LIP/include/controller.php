<?php
/*
 * コントローラ抽象クラス
 * /app/LIP/include/controller.php
 */

class LIP_Controler extends LIP_Object {
	var $template, $post, $get, $table = "", $session, $file, $m;
	function LIP_Controler() {
		$this->get_post();
		/*
		$this->file = new LIP_File();
		$this->pager = new LIP_HTML_Pager();
		$this->session = new LIP_Session( config("session", "sess_cookie_name") );
		*/
	}
	function load_func( $func, $param ) {
		if( is_callable( array( $this, $func ) ) )
			call_user_func_array( array( $this, $func ), is_array( $param ) ? $param : array( $param ) );
	}
	function load_model( $model ) {
		$file = sprintf( "%s/model/%s.php", app_dir(), $model );
		if( ! file_exists( $file ) ) {
			return FALSE;
		}
		require_once( sprintf( "%s/model/%s.php", app_dir(), $model ) );
		$m = "LM_".ucfirst( $model );
		$m = new $m();
		if(! isset( $this->$model ) ) {
			$this->$model =& $m;
		}
		$this->m[$model] =& $m;
		return TRUE;
	}
	function set_template( $path, $ext = "php" ) {
		$this->template = get_template( $path, $ext );
	}
	function view( $path = NULL, $var = NULL ) {
		if( !empty( $path ) )
			$this->set_template( $path );
		if( $this->template ) {
			if( is_array( $var ) || is_object( $var ) )
				extract( $var );
			ob_start();
			require_once( $this->template );
			$result = ob_get_contents();
			ob_end_clean();
			return $result;
		} else return FALSE;
	}
	function get_post() {
		$this->post = $this->sanityze( $_POST );
		$this->get = $this->sanityze( $_GET );
	}
	function check_nonce( $key ) {
		if( ! empty( $this->post[$key] ) ) {
			$pnonce = $this->post[$key];
			$snonce = $this->session->get_session($key);
			
			/* nonceキー削除 */
			unset( $this->post[$key] );
			$this->session->set_session($key, "");
			
			return ( $snonce == $pnonce );
		} else return FALSE;
	}
	function sanityze( $obj ) {
		if( is_array( $obj ) || is_object( $obj ) ) {
			foreach( $obj as $key => $val ) {
				$obj[$key] = $this->sanityze( $val );
			}
		} else {
			return htmlspecialchars( $obj, ENT_QUOTES );
		}
	}
	function checkFile() {
	}
}