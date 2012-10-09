<?php
/*
 * モデル抽象クラス
 * /app/LIP/include/model.php
 */

class LIP_Model extends LIP_Object {
	var $table = NULL,
		$db    = NULL,
		$sql   = array(),
		$args  = array(),
		$save  = FALSE,
		$unsave_group = array( "SELECT", "INSERT", "UPDATE", "DELETE" );
	
	function LIP_Model() {
		$LIP =& get_instance();
		$this->db = $LIP->db->get_database();
	}
	
	/* クエリ直接実行 */
	function query($query) {
		return $this->db->query( $query );
	}
	/* SELECT作成 */
	function select( $column = "*", $where = NULL ) {
		return $this->select_from( $this->table, $column, $where );
	}
	function select_from( $table, $column = "*", $where = NULL ) {
		if(! empty( $where ) )
			$this->add_where( $where );
		$this->sql["SELECT"] = sprintf( "SELECT %s FROM `%s`", $column, $table );
		return $this->sql["SELECT"];
	}
	/* INSERT作成 */
	function insert( $data ) {
		return $this->insert_to( $this->table, $data );
	}
	function insert_to( $table, $data ) {
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
	/* UPDATE作成 */
	function update( $data, $where = NULL ) {
		return $this->update_to( $this->table, $data, $where );
	}
	function update_to( $table, $data, $where = NULL ) {
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
	function replace( $column, $from, $to, $where = NULL ) {
		return $this->replace_to( $this->table, $column, $from, $to, $where );
	}
	function replace_to( $table, $column, $from, $to, $where ) {
		if(! empty( $where )  )
			$this->add_where( $where );
		$this->sql["UPDATE"] = sprintf( "UPDATE `%s` SET `%s` = REPLACE( `%s`, ?, ? )", $table, $column, $column );
		$this->args["UPDATE"][] = $from;
		$this->args["UPDATE"][] = $to;
		return $this->exec();
	}
	/* DELETE作成 */
	function delete( $where = NULL ) {
		return $this->delete_to( $this->table, $where );
	}
	function delete_to( $table, $where = NULL ) {
		if(! empty( $where ) )
			$this->add_where( $where );
		$this->sql["DELETE"] = sprintf( "DELETE FROM `%s`", $table );
		return $this->exec();
	}
	/* WHERE追加 */
	function add_where( $target, $param = NULL ) {
		if( empty( $param ) && ( is_array( $target ) || is_object( $target ) ) ) {
			foreach( $target as $field => $param ) {
				$this->add_where( $field, $param );
			}
		} else {
			$this->sql["WHERE"][] = sprintf( "`%s`=?", $target );
			$this->args["WHERE"][] = $param;
		}
	}
	function add_where_in( $target, $param ) {
		if( is_array( $param ) ) {
			foreach( $param as $val ) {
				$pcnt[] = "?";
				$this->args["WHERE"][] = $val;
			}
			$this->sql["WHERE"][] = sprintf( "`%s` IN (%s)", $target, implode(',', $pcnt) );
		} else {
			// エラー
		}
	}
	function add_where_like( $target, $param = NULL ) {
		if( empty( $param ) && ( is_array( $target ) || is_object( $target ) ) ) {
			foreach( $target as $field => $param ) {
				$this->add_where_like( $field, $param );
			}
		} else {
			$this->sql["WHERE"][] = sprintf( "`%s` LIKE ?", $target );
			$this->args["WHERE"][] = "%".$param."%";
		}
	}
	function add_option_where( $target, $param ) {
		if( is_array( $param ) || is_object( $param ) ) {
			preg_match( '/{(.*)}/', serialize( $param ), $match );
			$this->add_where_like( $target, $match[1] );
		} else {
			// エラー
		}
	}
	/* ORDER追加 */
	function add_order( $target, $duration="ASC" ) {
		$this->sql["ORDER"][] = sprintf( "`%s` %s", $target, $duration );
	}
	/* JOIN追加 */
	function add_join( $duration, $query, $param=NULL ) {
		$this->sql["JOIN"][] = sprintf( " %s JOIN %s", $duration, $query );
		if( is_array( $param ) )
			$this->args["JOIN"] = $param;
	}
	function left_join( $table, $key ) {
		$this->sql["JOIN"][] = " LEFT ".$this->make_join( $table, $key );
	}
	function right_join( $table, $key ) {
		$this->sql["JOIN"][] = " RIGHT ".$this->make_join( $table, $key );
	}
	function inner_join( $table, $key ) {
		$this->sql["JOIN"][] = " INNER ".$this->make_join( $table, $key );
	}
	function make_join( $table, $key ) {
		if( is_array( $key ) ) {
			return sprintf( "JOIN `%s` ON `%s`.`%s` = `%s`.`%s`", $table, $this->table, $key[0], $table, $key[1] );
		} else {
			return sprintf( "JOIN `%s` USING( `%s` )", $table, $key );
		}
	}
	/* GROUP設定 */
	function add_group( $key ) {
		if( is_object( $key ) ) {
			return FALSE;
		} else if( is_array( $key ) ) {
			if(! is_array( $this->sql["GROUP"] ) ) $this->sql["GROUP"] = array();
			$this->sql["GROUP"] = array_merge( $this->sql["GROUP"], $key);
		} else {
			$this->sql["GROUP"][] = $key;
		}
	}
	/* LIMIT設定 */
	function set_limit( $limit, $offset=NULL ) {
		if( is_int( $limit ) )
			$this->limit = $limit;
		if( is_int( $offset ) )
			$this->offset = $offset;
		// $this->db->setLimit( $limit, $offset );
	}
	
	/* 全結果取得 */
	function get_result() {
		return $this->exec();
	}
	/* 一行取得 */
	function get_line() {
		$result = $this->get_result();
		if( $result ) {
			return $result->fetch();
		} else return FALSE;
	}

	function save_condition() {
		$this->save = TRUE;
	}
	function unsave_condition() {
		$this->save = FALSE;
	}
	
	/* 実行 */
	function exec() {
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
	/* SQL組み立て */
	function make_sql() {
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
	function push_args( &$args, $mode ) {
		$args_group = $this->args[$mode];
		if( $args_group ) { foreach( $args_group as $row ) {
			$args[] = $row;
		} }
	}
	/* 変数初期化 */
	function sql_init() {
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
	
	function get_count() {
		$this->get_count_from( $this->table );
	}
	function get_count_from( $table ) {
		$this->select_from( $table, "COUNT(*) AS line" );
		$result = $this->get_line();
		return $result["line"];
	}
	function get_last_insertid() {
		//return $this->db->queryOne("SELECT LAST_INSERT_ID() AS id");
		return $this->db->lastInsertId();
	}
}
