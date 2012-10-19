<?php
/*
 * コントローラ抽象クラス
 * /app/LIP/include/controller.php
 */

class LIP_Controler extends LIP_Object {
	protected	$template,
				$post,
				$get,
				$table = "",
				$m;

	/* ####################################
	   PUBLIC FUNCTION
	#################################### */
	public function __construct() {
		$this->get_post();
	}
	public function load_func( $func, $param ) {
		if( is_callable( array( $this, $func ) ) )
			call_user_func_array( array( $this, $func ), is_array( $param ) ? $param : array( $param ) );
	}
	public function load_model( $model ) {
		$file = sprintf( "%s/model/%s.php", app_dir(), $model );
		if( ! file_exists( $file ) ) {
			return FALSE;
		}
		require_once( sprintf( "%s/model/%s.php", app_dir(), $model ) );
		$m = "LM_".ucfirst( $model );
		$m = new $m();
		$this->m[$model] =& $m;
		return TRUE;
	}

	/* ####################################
	   PUBLIC FUNCTION
	#################################### */
	protected function set_template( $path, $ext = "php" ) {
		$this->template = get_template( $path, $ext );
	}
	protected function view( $path = NULL, $var = NULL ) {
		if(! empty( $path ) )
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
	protected function check_nonce( $key ) {
		if( ! empty( $this->post[$key] ) ) {
			$pnonce = $this->post[$key];
			$snonce = $this->session->get_session($key);
			
			/* nonceキー削除 */
			unset( $this->post[$key] );
			$this->session->set_session($key, "");
			
			return ( $snonce == $pnonce );
		} else return FALSE;
	}

	/* ####################################
	   PRIVATE FUNCTION
	#################################### */
	private function get_post() {
		$this->post = sanityze( $_POST );
		$this->get = sanityze( $_GET );
	}
}