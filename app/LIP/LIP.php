<?php
/*
 * LIPベースクラス
 * /app/include/import.php
 * Version 0.0.1
 */

class LIP {
	private static $instance;
	public function LIP() {
		self::$instance =& $this;
	}
	public static function &get_instance() {
		return self::$instance;
	} 
}

function &get_instance() {
	return LIP::get_instance();
}