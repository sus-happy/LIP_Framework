<?php
/*
 * モデル抽象クラス
 * /app/LIP/include/model.php
 */

class LIP_Model extends LIP_Object {
	var $table = NULL,
		$db    = NULL,
		$sql   = array(),
		$args  = array();
	
	function LIP_Model() {
		$LIP =& get_instance();
		$this->db = $LIP->db;
	}
	
	/* クエリ直接実行 */
	function query($query) {
		return $this->db->query( $query );
	}
	/* SELECT作成 */
	function select( $column = "*", $where = NULL ) {
		if(! empty( $where )  )
			$this->add_where( $where );
		$this->sql["SELECT"] = sprintf( "SELECT %s FROM `%s`", $column, $this->table );
		return $this->sql["SELECT"];
	}
	/* INSERT作成 */
	function insert( $data ) {
		if( is_array( $data ) || is_object( $data ) ) {
			$fields = array();
			$keys = array();
			foreach( $data as $field=>$param ) {
				$fields[] = "`".$field."`";
				$keys[] = "?";
				$this->args["INSERT"][] = $param;
			}
			$this->sql["INSERT"] = sprintf( "INSERT INTO `%s` (%s) VALUES(%s)", $this->table, implode(",", $fields), implode(",", $keys) );
			return $this->exec();
		} else return FALSE;
	}
	/* UPDATE作成 */
	function update( $data, $where = NULL ) {
		if( is_array( $data ) || is_object( $data ) ) {
			if(! empty( $where )  )
				$this->add_where( $where );
			$upparams = array();
			foreach( $data as $field=>$param ) {
				$upparams[] = sprintf( "`%s`=?", $field );
				$this->args["UPDATE"][] = $param;
			}
			$this->sql["UPDATE"] = sprintf( "UPDATE `%s` SET %s", $this->table, implode(",", $upparams) );
			return $this->exec();
		} else return FALSE;
	}
	/* DELETE作成 */
	function delete( $where ) {
		$this->add_where( $where );
		$this->sql["DELETE"] = sprintf( "DELETE FROM `%s`", $this->table );
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
	/* LIMIT設定 */
	function set_limit( $limit, $offset=NULL ) {
		$this->db->setLimit( $limit, $offset );
	}
	
	/* 全結果取得 */
	function get_result() {
		return $this->exec();
	}
	/* 一行取得 */
	function get_line() {
		$result = $this->get_result();
		return $result->fetchRow();
	}
	
	/* 実行 */
	function exec() {
		$query = $this->make_sql();
		if(! empty( $query ) ) {
			$this->sql_init();

			$sth = $this->db->prepare( $query["sql"] );
			if (PEAR::isError($sth)){
				echo $sth->getDebugInfo();
				exit();
			}
			return $sth->execute( $query["args"] );
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
		$this->sql = array();
		$this->args = array();
	}
	
	function get_count() {
		$this->select( "COUNT(*) AS line" );
		$result = $this->get_line();
		return $result["line"];
	}
	function get_last_insertid() {
		return $this->db->queryOne("SELECT LAST_INSERT_ID() AS id");
	}
}
