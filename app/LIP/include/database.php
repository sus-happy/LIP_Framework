<?php
/* -----------------------------
 LIP_Database : データベース接続クラスクラス
 /app/LIP/include/database.php
 --
 @written 12-11-30 SUSH
 @todo 各DBに対応しないと行けないなー
----------------------------- */

class LIP_Database extends LIP_Object {
	private $db;

	/* -----------------------------
	 コンストラクタ
	 Void __construct( $db_info = NULL )
	 --
	 @param Array $db_info
	 	データベース設定情報
	----------------------------- */
	public function __construct( $db_info = NULL ) {
		if(! empty( $db_info ) )
			$this->set_database( $db_info );
	}

	/* -----------------------------
	 設定したPDOを取得
	 PDO get_database()
	----------------------------- */
	public function get_database() {
		return $this->db;
	}

	/* -----------------------------
	 PDOを設定
	 Void set_database( $db_info )
	 --
	 @param Array $db_info
	----------------------------- */
	private function set_database( $db_info ) {
		try {
			if( $db_info['type'] === 'sqlite' ) {
				$this->db = new PDO( sprintf( '%s:%s', $db_info['type'], $db_info['dbname'] ) );
			} else {
				$this->db = new PDO(
					sprintf( '%s:host=%s;dbname=%s',
						$db_info["type"],
						$db_info["host"],
						$db_info["dbname"]
					),
					$db_info["user"],
					$db_info["pass"]
				);
			}
		} catch( PDOException $e ) {
			echo 'Connection failed: ' . $e->getMessage();
		}
	}

}