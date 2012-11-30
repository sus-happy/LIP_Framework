<?php
/* -----------------------------
 LIP_Controler : コントローラ抽象クラス
 /app/LIP/include/controller.php
 --
 @written 12-11-30 SUSH
----------------------------- */

class LIP_Controler extends LIP_Object {
	protected	$template,
				$post,
				$get,
				$table = "",
				$m;

	/* -----------------------------
	 コンストラクタ
	 Void __construct()
	----------------------------- */
	public function __construct() {
		$this->get_post();
	}

	/* -----------------------------
	 発火
	 Void load_func( $func, $param )
	 --
	 @param Function $func
	 @param Mixed $param
	----------------------------- */
	public function load_func( $func, $param ) {
		if( is_callable( array( $this, $func ) ) )
			call_user_func_array( array( $this, $func ), is_array( $param ) ? $param : array( $param ) );
	}

	/* -----------------------------
	 モデルクラス読み込み
	 Void load_model( $model )
	 --
	 @param String $model
	----------------------------- */
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

	/* -----------------------------
	 テンプレートセット
	 Void set_template( $path, $ext = "php" )
	 --
	 @param String $path
	 @param String $ext
	----------------------------- */
	protected function set_template( $path, $ext = "php" ) {
		$this->template = get_template( $path, $ext );
	}

	/* -----------------------------
	 テンプレート読み込み
	 	$path, $varが空だとset_templateで設定したテンプレートを読み込み
	 	入力した場合は内部でset_templateを読み込み
	 String view( $path = NULL, $var = NULL )
	 --
	 @param String $path
	 @param String $var
	 @see $this->set_template()
	----------------------------- */
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

	/* -----------------------------
	 nonceをチェック
	 Boolean check_nonce( $key )
	 --
	 @param String $key
	----------------------------- */
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

	/* -----------------------------
	 $_GET, $_POSTをサニタイズ
	 	@todo サニタイズ言うな
	 Boolean get_post( $key )
	 --
	 @param String $key
	----------------------------- */
	private function get_post() {
		$this->post = sanityze( $_POST );
		$this->get = sanityze( $_GET );
	}
}