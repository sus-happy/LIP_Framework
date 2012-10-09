<?php
/*
 * 汎用コントローラクラス
 * /app/LIP/control/404.php
 * --
 * 404 Not Foundページ
 */

class LC_Notfound extends LIP_Controler {
	function LC_Notfound() {
		parent::LIP_Controler();
	}
	function index() {
		echo $this->view("404");
	}
}