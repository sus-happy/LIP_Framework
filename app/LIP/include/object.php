<?php
/* -----------------------------
 LIP_Object : 抽象クラス
 /app/LIP/include/object.php
 --
 @written 12-11-30 SUSH
----------------------------- */

class LIP_Object {
	private $error,
			$ex_flag = TRUE;

	/* -----------------------------
	 コンストラクタ
	 Void __construct()
	----------------------------- */
	public function __construct() {
	}

	/* -----------------------------
	 Exeptionエラーを発生させる
	 Void enable_exeption_flag()
	----------------------------- */
	public function enable_exeption_flag() {
		$this->ex_flag = TRUE;
	}

	/* -----------------------------
	 Exeptionエラーを発生させない
	 	get_error_text()で一覧を取得できる
	 Void disable_exeption_flag()
	----------------------------- */
	public function disable_exeption_flag() {
		$this->ex_flag = FALSE;
	}

	/* -----------------------------
	 エラーメッセージの追加
	 Void push_error( $key, $message )
	 --
	 @param String $key
	 @param String $message
	----------------------------- */
	public function push_error( $key, $message ) {
		if( !empty( $this->error[$key] ) ) {
			if( is_array( $this->error[$key] ) ) {
			if( is_array( $message ) )
				$this->error[$key] += $message;
			else
				$this->error[$key][] = $message;
			} else {
			$tmp = $this->error[$key];
			unset( $this->error[$key] );
			if( is_array( $message ) ) {
				$this->error[$key][] = $tmp;
				$this->error[$key] += $message;
			} else
				$this->error[$key] = array( $tmp, $message );
			}
		} else
			$this->error[$key] = $message;

		if( LIP_DEBUG_MODE === TRUE && $this->ex_flag ) {
			throw new Exception( 'CLASS : ' . get_class($this) . ' ERROR -> ' . $key . ' : ' . $message );
		}
	}

	/* -----------------------------
	 エラーメッセージを取得
	 	$key が空の場合は、全てのエラーメッセージを配列で取得
	 Mixed get_error_text( $key = NULL )
	 --
	 @param String $key
	----------------------------- */
	public function get_error_text( $key = NULL ) {
		if( !empty( $this->error ) ) {
			if(! $key ) return $this->error;
			if(! empty($this->error[$key]) ) {
				return $this->error[$key];
			}
		}
		return NULL;
	}

	/* -----------------------------
	 エラーが発生しているかチェック
	 	$key が空の場合は、全てのエラーで確認
	 Boolean get_check_error( $key = NULL )
	 --
	 @param String $key
	----------------------------- */
	public function get_check_error( $key = NULL ) {
		if( !empty( $this->error ) ) {
			if(! $key ) return TRUE;
			if(! empty($this->error[$key]) ) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/* -----------------------------
	 エラー変数を全表示
	 	$hideが正の時は、HTMLのコメントアウト内で出力する
	 Boolean error_dump( $hide = FALSE )
	 --
	 @param Boolean $hide
	----------------------------- */
	public function error_dump( $hide = FALSE ) {
		echo $hide ? '<!--' : '<pre>';
		var_dump($this->check);
		echo $hide ? '-->' : '</pre>';
	}
}