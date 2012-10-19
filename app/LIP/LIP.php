<?php
/*
 * LIPベースクラス
 * /app/include/import.php
 * Version 0.0.1
 */

class LIP {
	private static $instance;
	public function __construct() {
		self::$instance =& $this;
	}
	public static function &get_instance() {
		if( empty( self::$instance ) )
			new LIP();
		return self::$instance;
	}
	public function set_method( $key, $class ) {
		$this->{$key} = $class;
	}
}

function &get_instance() {
	return LIP::get_instance();
}