<?php
/* -----------------------------
 LIP_Model : モデル抽象クラス
 /app/LIP/include/model.php
 --
 @written 12-11-30 SUSH
----------------------------- */

class LIP_Model extends LIP_Object {
	protected $table = NULL;
	private $db    = NULL,
			$sql   = array(),
			$args  = array(),
			$save  = FALSE,
			$unsave_group = array( "SELECT", "INSERT", "UPDATE", "DELETE" );

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
		$this->select_from( $this->table, $column, $where );
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
			if(! empty( $where ) )
				$this->add_where( $where );
			$this->sql["SELECT"] = sprintf( "SELECT %s FROM `%s`", $column, $table );
		}

	/* -----------------------------
	 INSERT文の生成->実行
	 PDOStatement insert( $data )
	 --
	 @param Array $data
	----------------------------- */
	public function insert( $data ) {
		return $this->insert_to( $this->table, $data );
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
			if( is_array( $data ) || is_object( $data ) ) {
				$fields = array();
				$keys = array();
				foreach( $data as $field=>$param ) {
					$fields[] = "`".$field."`";
					$keys[] = "?";
					$this->args["INSERT"][] = $param;
				}
				$this->sql["INSERT"] = sprintf( "INSERT INTO `%s` (%s) VALUES(%s)", $table, implode(",", $fields), implode(",", $keys) );
				return $this->exec();
			} else return FALSE;
		}

	/* -----------------------------
	 UPDATE文の生成->実行
	 PDOStatement update( $data, $where = NULL )
	 --
	 @param Array $data
	 @param Array $where
	----------------------------- */
	public function update( $data, $where = NULL ) {
		return $this->update_to( $this->table, $data, $where );
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
			if( is_array( $data ) || is_object( $data ) ) {
				if(! empty( $where )  )
					$this->add_where( $where );
				$upparams = array();
				foreach( $data as $field=>$param ) {
					$upparams[] = sprintf( "`%s`=?", $field );
					$this->args["UPDATE"][] = $param;
				}
				$this->sql["UPDATE"] = sprintf( "UPDATE `%s` SET %s", $table, implode(",", $upparams) );
				return $this->exec();
			} else return FALSE;
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
		return $this->replace_to( $this->table, $column, $from, $to, $where );
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
			if(! empty( $where )  )
				$this->add_where( $where );
			$this->sql["UPDATE"] = sprintf( "UPDATE `%s` SET `%s` = REPLACE( `%s`, ?, ? )", $table, $column, $column );
			$this->args["UPDATE"][] = $from;
			$this->args["UPDATE"][] = $to;
			return $this->exec();
		}

	/* -----------------------------
	 DELETE文の生成->実行
	 PDOStatement delete( $where = NULL )
	 --
	 @param Array $where
	----------------------------- */
	public function delete( $where = NULL ) {
		return $this->delete_from( $this->table, $where );
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
			if(! empty( $where ) )
				$this->add_where( $where );
			$this->sql["DELETE"] = sprintf( "DELETE FROM `%s`", $table );
			return $this->exec();
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
		$_array = ( is_array( $target ) || is_object( $target ) );
		$_param = ! strlen( $param );

		if( ! $_array && $_param ) {
			$this->push_error( 'WHERE', 'Search Paramater is Empty' );
			return FALSE;
		}

		if( empty( $param ) && ( is_array( $target ) || is_object( $target ) ) ) {
			foreach( $target as $field => $param ) {
				if(! $this->add_where( $field, $param ) )
					return FALSE;
			}
		} else {
			$this->sql["WHERE"][] = sprintf( "`%s`=?", $target );
			$this->args["WHERE"][] = $param;
		}
		return TRUE;
	}

	/* -----------------------------
	 WHERE IN句の検索対象を追加する
	 Boolean add_where_in( $target, $param )
	 --
	 @param String $target
	 @param Array $param
	----------------------------- */
	public function add_where_in( $target, $param ) {
		if( is_array( $param ) ) {
			foreach( $param as $val ) {
				$pcnt[] = "?";
				$this->args["WHERE"][] = $val;
			}
			$this->sql["WHERE"][] = sprintf( "`%s` IN (%s)", $target, implode(',', $pcnt) );
		} else {
			$this->push_error( 'WHERE', 'Search Paramater should be an Array' );
			return FALSE;
		}
		return TRUE;
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
		$_array = ( is_array( $target ) || is_object( $target ) );
		$_param = ! strlen( $param );

		if( ! $_array && $_param ) {
			$this->push_error( 'WHERE', 'Search Paramater is Empty' );
			return FALSE;
		}

		if( $_param && $_array ) {
			foreach( $target as $field => $param ) {
				if(! $this->add_where_like( $field, $param ) )
					return FALSE;
			}
		} else {
			$this->sql["WHERE"][] = sprintf( "`%s` LIKE ?", $target );
			$this->args["WHERE"][] = "%".$param."%";
		}
		return TRUE;
	}

	/* -----------------------------
	 データ格納時にシリアライズしたデータを検索する
	 Boolean add_option_where( $target, $param )
	 --
	 @param String $target
	 @param Array $param
	----------------------------- */
	public function add_option_where( $target, $param ) {
		if( is_array( $param ) || is_object( $param ) ) {
			preg_match( '/{(.*)}/', serialize( $param ), $match );
			$this->add_where_like( $target, $match[1] );
		} else {
			$this->push_error( 'WHERE', 'Search Paramater should be an Array' );
			return FALSE;
		}
		return TRUE;
	}

	/* -----------------------------
	 ORDER句を追加する
	 Boolean add_order( $target, $duration = 'ASC' )
	 --
	 @param String $target
	 @param String $duration
	----------------------------- */
	public function add_order( $target, $duration = "ASC" ) {
		if(! in_array( $duration, array( 'ASC', 'DESC' ) ) ) {
			$this->push_error( 'ORDER', 'Duration is in "ASC" or "DESC"' );
			return FALSE;
		}
		$this->sql["ORDER"][] = sprintf( "`%s` %s", $target, $duration );
		return TRUE;
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
		$this->left_join_pair( $this->table, $table, $key );
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
			$this->sql["JOIN"][] = " LEFT ".$this->make_join( $parent, $child, $key );
		}

	/* -----------------------------
	 RIGHT JOIN句を追加する
	 Void right_join( $table, $key )
	 --
	 @param String $table
	 @param Mixed $key
	----------------------------- */
	public function right_join( $table, $key ) {
		$this->right_join_pair( $this->table, $table, $key );
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
			$this->sql["JOIN"][] = " RIGHT ".$this->make_join( $parent, $child, $key );
		}

	/* -----------------------------
	 INNER JOIN句を追加する
	 Void inner_join( $table, $key )
	 --
	 @param String $table
	 @param Mixed $key
	----------------------------- */
	public function inner_join( $table, $key ) {
		$this->inner_join_pair( $this->table, $table, $key );
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
			$this->sql["JOIN"][] = " INNER ".$this->make_join( $parent, $child, $key );
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
		if( is_array( $key ) ) {
			return sprintf( "JOIN `%s` ON `%s`.`%s` = `%s`.`%s`", $child, $parent, $key[0], $child, $key[1] );
		} else {
			return sprintf( "JOIN `%s` USING( `%s` )", $child, $key );
		}
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
		if( in_array( $duration, array( 'LEFT', 'RIGHT', 'INNER' ) ) ) {
			$this->push_error( 'JOIN', 'Duration is in "LEFT", "RIGHT" or "DESC"' );
			return FALSE;
		}
		$this->sql["JOIN"][] = sprintf( " %s JOIN %s", $duration, $query );

		if(! empty( $param ) ) {
			if( is_array( $param ) || is_object( $param ) )
				$this->args["JOIN"] = (array)$param;
			else
				$this->args["JOIN"][] = $param;
		}
		return TRUE;
	}

	/* -----------------------------
	 GROUP句を追加する
	 Boolean add_group( $key )
	 --
	 @param Mixed $key
	----------------------------- */
	public function add_group( $key ) {
		if( is_object( $key ) ) {
			$this->push_error( 'GROUP', 'Should be an Array or String' );
			return FALSE;
		} else if( is_array( $key ) ) {
			if(! is_array( $this->sql["GROUP"] ) ) $this->sql["GROUP"] = array();
			$this->sql["GROUP"] = array_merge( $this->sql["GROUP"], $key);
		} else {
			$this->sql["GROUP"][] = $key;
		}
		return TRUE;
	}

	/* -----------------------------
	 LIMIT句を追加する
	 Boolean set_limit( $limit, $offset = NULL )
	 --
	 @param Integer $limit
	 @param Integer $offset
	----------------------------- */
	public function set_limit( $limit, $offset = NULL ) {
		$flag = TRUE;
		if( is_int( $limit ) ) {
			$this->limit = $limit;
		} else {
			$flag = FALSE;
			$this->push_error( 'LIMIT', 'Limit is should be an Integer' );
		}
		if( is_int( $offset ) )
			$this->offset = $offset;
		else if(! empty( $offset ) ) {
			$flag = FALSE;
			$this->push_error( 'LIMIT', 'Offset is should be an Integer' );
		}

		return $flag;
	}

	/* -----------------------------
	 SELECT実行用関数 全取得
	 	というか、exec実行してるだけ…
	 PDOStatement get_result()
	----------------------------- */
	public function get_result() {
		return $this->exec();
	}

	/* -----------------------------
	 SELECT実行用関数 一行取得
	 	というか、exec実行してるだけ…
	 PDOStatement get_line()
	----------------------------- */
	public function get_line() {
		$result = $this->get_result();
		if( $result ) {
			return $result->fetch();
		} else return FALSE;
	}

	/* -----------------------------
	 次回実行時にも検索対象などを保存する
	 	保存される設定は下記の通り
	 	JOIN, WHERE, GROUP, ORDER, LIMIT, OFFSET
	 Void save_condition()
	----------------------------- */
	public function save_condition() {
		$this->save = TRUE;
	}

	/* -----------------------------
	 次回実行時にも検索対象などを保存しない
	 	初期設定値はこちら
	 Void unsave_condition()
	----------------------------- */
	public function unsave_condition() {
		$this->save = FALSE;
	}

	/* -----------------------------
	 保存された内容を実行する
	 PDOStatement exec()
	----------------------------- */
	public function exec() {
		$query = $this->make_sql();
		if(! empty( $query ) ) {
			if(! empty( $this->limit ) ) {
				if(! empty( $this->offset ) ) {
					$query["sql"] .= " LIMIT ?, ?";
				} else {
					$query["sql"] .= " LIMIT ?";
				}
			}

			try {
				$sth = $this->db->prepare( $query["sql"] );
			} catch( PDOException $e ) {
				echo 'Prepare failed: ' . $e->getMessage();
			}

			if(! $sth ) {
				echo "PDO::errorInfo():";
				print_r($this->db->errorInfo());
			}

			foreach ( $query["args"] as $key => $value ) {
				$sth->bindValue( $key+1, $value );
			}
			if(! empty( $this->limit ) ) {
				$lc = count( $query["args"] );
				if(! empty( $this->offset ) ) {
					$lc++;
					$sth->bindValue( $lc, $this->offset, PDO::PARAM_INT );
				}
				$lc++;
				$sth->bindValue( $lc, $this->limit, PDO::PARAM_INT );
			}
			if( $sth ) {
				if(! $sth->execute() ) {
					echo 'Execute failed: ';
					var_dump( $sth->errorInfo() );
					var_dump( $query );
					exit();
				}
				$sth->setFetchMode( PDO::FETCH_ASSOC );
			}

			$this->sql_init();
			return $sth;
		} return FALSE;
	}

	/* -----------------------------
	 保存された内容からSQL文を組み立てる
	 Array make_sql()
	----------------------------- */
	private function make_sql() {
		$flag = TRUE; $sql = ""; $args = array();
		if(! empty( $this->sql["SELECT"] ) ) {
			$sql = $this->sql["SELECT"];
			$this->push_args( $args, "SELECT" );
		} else if(! empty( $this->sql["INSERT"] ) ) {
			$sql = $this->sql["INSERT"];
			$this->push_args( $args, "INSERT" );
		} else if(! empty( $this->sql["UPDATE"] ) ) {
			$sql = $this->sql["UPDATE"];
			$this->push_args( $args, "UPDATE" );
		} else if(! empty( $this->sql["DELETE"] ) ) {
			$sql = $this->sql["DELETE"];
			$this->push_args( $args, "DELETE" );
		}
		if( $flag ) {
			if( isset( $this->sql["JOIN"] ) ) {
				$sql .= " ".implode( " ", $this->sql["JOIN"] );
				$this->push_args( $args, "JOIN" );
			}
			if( isset( $this->sql["WHERE"] ) ) {
				$sql .= " WHERE 1=1 AND ".implode( " AND ", $this->sql["WHERE"] );
				$this->push_args( $args, "WHERE" );
			}
			if( isset( $this->sql["GROUP"] ) ) {
				$sql .= sprintf( " GROUP BY `%s`", implode( "`,`", $this->sql["GROUP"] ) );
				$this->push_args( $args, "ORDER" );
			}
			if( isset( $this->sql["ORDER"] ) ) {
				$sql .= " ORDER BY ".implode( ",", $this->sql["ORDER"] );
				$this->push_args( $args, "ORDER" );
			}
			return array( "sql" => $sql, "args" => $args );
		}
		return FALSE;
	}

	/* -----------------------------
	 プリペアドステートメント用の変数を割り当てる
	 Void push_args( &$args, $mode )
	 --
	 @param Array $args
	 @param String $mode
	----------------------------- */
	private function push_args( &$args, $mode ) {
		$args_group = $this->args[$mode];
		if( $args_group ) { foreach( $args_group as $row ) {
			$args[] = $row;
		} }
	}

	/* -----------------------------
	 内部変数を初期化する
	 	save_conditionが実行されていると、一部保存される
	 Void sql_init()
	----------------------------- */
	public function sql_init() {
		if( $this->save ) {
			foreach( $this->unsave_group as $unsave ) {
				unset( $this->sql[$unsave] );
				unset( $this->args[$unsave] );
			}
		} else {
			$this->sql = array();
			$this->args = array();
			$this->limit = NULL;
			$this->offset = NULL;
		}
	}

	/* -----------------------------
	 SELECT文の行数を取得する
	 Void get_count()
	----------------------------- */
	public function get_count() {
		return $this->get_count_from( $this->table );
	}
		/* -----------------------------
		 SELECT文の行数を取得する
		 	テーブルを指定する際はこちらを使う
		 Void get_count_from( $table )
		 --
		 @param String $table
		----------------------------- */
		public function get_count_from( $table ) {
			$this->select_from( $table, "COUNT(*) AS line" );
			$result = $this->get_line();
			return (int)$result["line"];
		}

	/* -----------------------------
	 Auto Incrementで追加した値を取得する
	 Integer get_last_insertid()
	----------------------------- */
	public function get_last_insertid() {
		return $this->db->lastInsertId();
	}
}
