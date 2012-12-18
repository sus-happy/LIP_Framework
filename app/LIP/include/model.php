<?php
/* -----------------------------
 LIP_Model : モデル抽象クラス
 /app/LIP/include/model.php
 --
 @written 12-11-30 SUSH
 @last updated 12-12-18 SUSH
----------------------------- */

class LIP_Model extends LIP_Object {
	protected $table = NULL;

	/* -----------------------------
	 コンストラクタ
	 Void __construct()
	----------------------------- */
	public function __construct() {
		$LIP =& get_instance();
		$this->db = $LIP->db->get_database();
	}

	/* -----------------------------
	 SQLクエリを直接実行する
	 PDOStatement query( $query )
	 --
	 @param String $query
	----------------------------- */
	public function query( $query ) {
		return $this->db->query( $query );
	}

	/* -----------------------------
	 SELECT文の生成
	 	get_resultで前取得、get_lineで一行取得
	 Void select( $column = '*', $where = NULL )
	 --
	 @param String $column
	 @param Array $where
	----------------------------- */
	public function select( $column = '*', $where = NULL ) {
		$this->db->select_from( $this->table, $column, $where );
	}
		/* -----------------------------
		 selectのラッパー
		 	テーブルを指定する際はこちらを使う
		 	get_resultで前取得、get_lineで一行取得
		 Void select_from( $table, $column = '*', $where = NULL )
		 --
		 @param String $table
		 @param String $column
		 @param Array $where
		----------------------------- */
		public function select_from( $table, $column = "*", $where = NULL ) {
			$this->db->select_from( $table, $column, $where );
		}

	/* -----------------------------
	 INSERT文の生成->実行
	 PDOStatement insert( $data )
	 --
	 @param Array $data
	----------------------------- */
	public function insert( $data ) {
		return $this->db->insert_to( $this->table, $data );
	}
		/* -----------------------------
		 insertのラッパー
		 	テーブルを指定する際はこちらを使う
		 PDOStatement insert_to( $table, $data )
		 --
		 @param String $table
		 @param Array $data
		----------------------------- */
		public function insert_to( $table, $data ) {
			return $this->db->insert_to( $table, $data );
		}

	/* -----------------------------
	 UPDATE文の生成->実行
	 PDOStatement update( $data, $where = NULL )
	 --
	 @param Array $data
	 @param Array $where
	----------------------------- */
	public function update( $data, $where = NULL ) {
		return $this->db->update_to( $this->table, $data, $where );
	}
		/* -----------------------------
		 updateのラッパー
		 	テーブルを指定する際はこちらを使う
		 PDOStatement update_to( $table, $data, $where = NULL )
		 --
		 @param String $table
		 @param Array $data
		 @param Array $where
		----------------------------- */
		public function update_to( $table, $data, $where = NULL ) {
			return $this->db->update_to( $table, $data, $where );
		}

	/* -----------------------------
	 UPDATEを利用して置換処理を行う
	 PDOStatement replace( $column, $from, $to, $where = NULL )
	 --
	 @param String $column
	 @param String $from
	 @param String $to
	 @param Array $where
	----------------------------- */
	public function replace( $column, $from, $to, $where = NULL ) {
		return $this->db->replace_to( $this->table, $column, $from, $to, $where );
	}
		/* -----------------------------
		 replaceのラッパー
		 	テーブルを指定する際はこちらを使う
		 PDOStatement replace_to( $table, $column, $from, $to, $where = NULL )
		 --
		 @param String $table
		 @param String $column
		 @param String $from
		 @param String $to
		 @param Array $where
		----------------------------- */
		public function replace_to( $table, $column, $from, $to, $where = NULL ) {
			return $this->db->replace_to( $table, $column, $from, $to, $where );
		}

	/* -----------------------------
	 DELETE文の生成->実行
	 PDOStatement delete( $where = NULL )
	 --
	 @param Array $where
	----------------------------- */
	public function delete( $where = NULL ) {
		return $this->db->delete_from( $this->table, $where );
	}
		/* -----------------------------
		 deleteのラッパー
		 	テーブルを指定する際はこちらを使う
		 PDOStatement delete_from( $table, $where = NULL )
		 --
		 $table String
		 @param Array $where
		----------------------------- */
		public function delete_from( $table, $where = NULL ) {
			return $this->db->delete_from( $table, $where );
		}

	/* -----------------------------
	 検索対象を追加する
	 	$targetが配列、$paramが空の場合は、
	 	add_where( $key, $val )
	 	として複数指定を行う
	 Boolean add_where( $target, $param )
	 --
	 @param Mixed $target
	 @param String $param
	----------------------------- */
	public function add_where( $target, $param = NULL ) {
		return $this->db->add_where( $target, $param );
	}

	/* -----------------------------
	 WHERE IN句の検索対象を追加する
	 Boolean add_where_in( $target, $param )
	 --
	 @param String $target
	 @param Array $param
	----------------------------- */
	public function add_where_in( $target, $param ) {
		return $this->db->add_where_in( $target, $param );
	}

	/* -----------------------------
	 WHERE LIKE句の検索対象を追加する
	 	$targetが配列、$paramが空の場合は、
	 	add_where_like( $key, $val )
	 	として複数指定を行う
	 	'%'は内部処理で自動的に付与される
	 Boolean add_where_like( $target, $param )
	 --
	 @param Array $target
	 @param String $param
	----------------------------- */
	public function add_where_like( $target, $param = NULL ) {
		return $this->db->add_where_like( $target, $param );
	}

	/* -----------------------------
	 データ格納時にシリアライズしたデータを検索する
	 Boolean add_option_where( $target, $param )
	 --
	 @param String $target
	 @param Array $param
	----------------------------- */
	public function add_option_where( $target, $param ) {
		return $this->db->add_option_where( $target, $param );
	}

	/* -----------------------------
	 ORDER句を追加する
	 Boolean add_order( $target, $duration = 'ASC' )
	 --
	 @param String $target
	 @param String $duration
	----------------------------- */
	public function add_order( $target, $duration = "ASC" ) {
		return $this->db->add_order( $target, $duration );
	}
		/* -----------------------------
		 $filedの配列順で並び替える
		 Boolean add_order( $target, $field )
		 --
		 @param String $target
		 @param Array $field
		----------------------------- */
		public function add_order_field( $target, $field ) {
			return $this->db->add_order_field( $target, $field );
		}

	/* -----------------------------
	 LEFT JOIN句を追加する
	 	$keyの値を配列にした場合、$key[0]が親要素、$key[1]が子要素になる
	 	$keyの値を文字列にした場合、USINGを利用する
	 Void left_join( $table, $key )
	 --
	 @param String $table
	 @param Mixed $key
	----------------------------- */
	public function left_join( $table, $key ) {
		$this->db->left_join_pair( $this->table, $table, $key );
	}
		/* -----------------------------
		 left_joinのラッパー
		 	テーブルを指定する際はこちらを使う
		 Void left_join_pair( $parent, $child, $key )
		 --
		 @param String $parent
		 @param String $child
		 @param Mixed $key
		----------------------------- */
		public function left_join_pair( $parent, $child, $key ) {
			$this->db->left_join_pair( $parent, $child, $key );
		}

	/* -----------------------------
	 RIGHT JOIN句を追加する
	 Void right_join( $table, $key )
	 --
	 @param String $table
	 @param Mixed $key
	----------------------------- */
	public function right_join( $table, $key ) {
		$this->db->right_join_pair( $this->table, $table, $key );
	}
		/* -----------------------------
		 right_joinのラッパー
		 	テーブルを指定する際はこちらを使う
		 Void right_join_pair( $parent, $child, $key )
		 --
		 @param String $parent
		 @param String $child
		 @param Mixed $key
		----------------------------- */
		public function right_join_pair( $parent, $child, $key ) {
			$this->db->right_join_pair( $parent, $child, $key );
		}

	/* -----------------------------
	 INNER JOIN句を追加する
	 Void inner_join( $table, $key )
	 --
	 @param String $table
	 @param Mixed $key
	----------------------------- */
	public function inner_join( $table, $key ) {
		$this->db->inner_join_pair( $this->table, $table, $key );
	}
		/* -----------------------------
		 inner_joinのラッパー
		 	テーブルを指定する際はこちらを使う
		 Void inner_join_pair( $parent, $child, $key )
		 --
		 @param String $parent
		 @param String $child
		 @param Mixed $key
		----------------------------- */
		public function inner_join_pair( $parent, $child, $key ) {
			$this->db->inner_join_pair( $parent, $child, $key );
		}

	/* -----------------------------
	 left / right / inner_join_pairからJOIN区の文字列を生成する
	 String make_join( $parent, $child, $key )
	 --
	 @param String $parent
	 @param String $child
	 @param Mixed $key
	----------------------------- */
	private function make_join( $parent, $child, $key ) {
		return $this->db->make_join( $parent, $child, $key );
	}

	/* -----------------------------
	 詳細なJOIN句を追加する
	 Boolean add_join( $duration, $query, $param = NULL )
	 --
	 @param String $duration
	 @param String $query
	 @param Mixed $param
	----------------------------- */
	public function add_join( $duration, $query, $param = NULL ) {
		return $this->db->add_join( $duration, $query, $param );
	}

	/* -----------------------------
	 GROUP句を追加する
	 Boolean add_group( $key )
	 --
	 @param Mixed $key
	----------------------------- */
	public function add_group( $key ) {
		return $this->db->add_group( $key );
	}

	/* -----------------------------
	 LIMIT句を追加する
	 Boolean set_limit( $limit, $offset = NULL )
	 --
	 @param Integer $limit
	 @param Integer $offset
	----------------------------- */
	public function set_limit( $limit, $offset = NULL ) {
		return $this->db->set_limit( $limit, $offset );
	}

	/* -----------------------------
	 SELECT実行用関数 全取得
	 	というか、exec実行してるだけ…
	 PDOStatement get_result()
	----------------------------- */
	public function get_result() {
		return $this->db->get_result();
	}

	/* -----------------------------
	 SELECT実行用関数 一行取得
	 	というか、exec実行してるだけ…
	 PDOStatement get_line()
	----------------------------- */
	public function get_line() {
		return $this->db->get_line();
	}

	/* -----------------------------
	 次回実行時にも検索対象などを保存する
	 	保存される設定は下記の通り
	 	JOIN, WHERE, GROUP, ORDER, LIMIT, OFFSET
	 Void save_condition()
	----------------------------- */
	public function save_condition() {
		$this->db->save_condition();
	}

	/* -----------------------------
	 次回実行時にも検索対象などを保存しない
	 	初期設定値はこちら
	 Void unsave_condition()
	----------------------------- */
	public function unsave_condition() {
		$this->db->unsave_condition();
	}

	/* -----------------------------
	 保存された内容を実行する
	 PDOStatement exec()
	----------------------------- */
	public function exec() {
		return $this->db->exec();
	}

	/* -----------------------------
	 SELECT文の行数を取得する
	 Void get_count()
	----------------------------- */
	public function get_count() {
		return $this->db->get_count_from( $this->table );
	}
		/* -----------------------------
		 SELECT文の行数を取得する
		 	テーブルを指定する際はこちらを使う
		 Void get_count_from( $table )
		 --
		 @param String $table
		----------------------------- */
		public function get_count_from( $table ) {
			return $this->db->get_count_from( $table );
		}

	/* -----------------------------
	 Auto Incrementで追加した値を取得する
	 Integer get_last_insertid()
	----------------------------- */
	public function get_last_insertid() {
		return $this->db->get_last_insertid();
	}

	/* -----------------------------
	 初期化する内部変数を設定する
	 Void set_unsave_group( $groups )
	 --
	 @param Array $groups
	----------------------------- */
	public function set_unsave_group( $groups ) {
		$this->db->set_unsave_group();
	}
}
