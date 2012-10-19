<?php
/*
 * 汎用コントローラクラス
 * /app/LIP/control/404.php
 * --
 * 404 Not Foundページ
 */

class LC_Notfound extends LIP_Controler {
	function __construct() {
		parent::__construct();
	}
	function index() {
		echo $this->view("404");
	}
}