<?php
/*
 * セッション拡張クラス
 * /app/LIP/library/session.php
 * -
 * $ses = new LL_Session('session_id');
 */
class LL_Session {
	var $id;
	/* コンストラクタ */
	function LL_Session( $id = "session_id" ) {
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
	function change_id( $id ) {
		$this->id = $id;
		if( !empty($_SESSION[$this->id]) ) {
			foreach($_SESSION[$this->id] as $key => $val) {
				$this->data[$key] = $val;
			}
		} else {
			$this->data = NULL;
		}
	}
	function get_session( $key = NULL ) {
		if( empty($key) ) return $this->data;
		return $this->data[$key];
	}
	// =============セッション登録
	function set_session( $name, $data ) {
		$_SESSION[$this->id][$name] = $data;
		$this->data[$name] = $data;
	}
	// =============セッション削除
	function reset() {
		$_SESSION[$this->id] = null;
		$this->data = null;
	}
	// =============セッション全削除
	function remove() {
		$this->data = null;
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();
	}
}
?>