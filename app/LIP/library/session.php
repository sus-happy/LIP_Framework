<?php
/* -----------------------------
 LL_Session : セッション拡張クラス
 /app/LIP/library/session.php
 --
 @written 12-11-30 SUSH
----------------------------- */

class LL_Session {
	private $id;

	/* -----------------------------
	 コンストラクタ
	 Void __construct( $id )
	 --
	 @param String $id
	----------------------------- */
	public function __construct( $id = "session_id" ) {
		$this->id = $id;
		if(! session_id() ) {
			session_start();
		}
		if( !empty($_SESSION[$this->id]) ) {
			foreach($_SESSION[$this->id] as $key => $val) {
				$this->data[$key] = $val;
			}
		}
	}

	/* -----------------------------
	 IDの変更
	 Void change_id( $id )
	 --
	 @param String $id
	----------------------------- */
	public function change_id( $id ) {
		$this->id = $id;
		if( !empty($_SESSION[$this->id]) ) {
			foreach($_SESSION[$this->id] as $key => $val) {
				$this->data[$key] = $val;
			}
		} else {
			$this->data = NULL;
		}
	}

	/* -----------------------------
	 セッション変数の取得
	 	$keyが空の場合は全取得
	 Mixied get_session( $key )
	 --
	 @param String $key
	----------------------------- */
	public function get_session( $key = NULL ) {
		if( empty($key) ) return $this->data;
		return $this->data[$key];
	}

	/* -----------------------------
	 セッション変数の登録
	 Void set_session( $name, $data )
	 --
	 @param String $name
	 @param String $data
	----------------------------- */
	public function set_session( $name, $data ) {
		$_SESSION[$this->id][$name] = $data;
		$this->data[$name] = $data;
	}

	/* -----------------------------
	 セッション変数の削除
	 	現在のIDに関連する情報だけ削除
	 Void reset()
	----------------------------- */
	public function reset() {
		$_SESSION[$this->id] = null;
		$this->data = null;
	}

	/* -----------------------------
	 セッション変数の削除
	 	全て削除
	 Void remove()
	----------------------------- */
	public function remove() {
		$this->data = null;
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();
	}
}
