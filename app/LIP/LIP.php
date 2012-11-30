<?php
/* -----------------------------
 LIP : LIPベースクラス
 /app/LIP.php
 --
 @written 12-11-30 SUSH
----------------------------- */

class LIP {
	private static $instance;

	/* -----------------------------
	 コンストラクタ
	 Void __construct()
	----------------------------- */
	public function __construct() {
		self::$instance =& $this;
	}

	/* -----------------------------
	 インスタンスの取得
	 Void get_instance()
	----------------------------- */
	public static function &get_instance() {
		if( empty( self::$instance ) )
			new LIP();
		return self::$instance;
	}

	/* -----------------------------
	 メソッドの登録
	 Void set_method( $key, $class )
	 --
	 @param String $key
	 @param Class $class
	----------------------------- */
	public function set_method( $key, $class ) {
		$this->{$key} = $class;
	}
}

/* -----------------------------
 インスタンスの取得
 Void get_instance()
----------------------------- */
function &get_instance() {
	return LIP::get_instance();
}