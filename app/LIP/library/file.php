<?php
/*
 * ファイル読み書き拡張クラス
 * /app/LIP/library/file.php
 */

class LL_File {
	/* アップロードディレクトリ */
	var $up_dir = "";
	/* アップ制限 */
	var $max_file_size = 2, $file_type = array("jpg", "jpeg", "png", "gif");
	/* アップファイル情報 */
	var $data = array();
	/* エラーメッセージ */
	var $error_message = array();
	
	function LL_File() {
	}
	
	function set_upload_dir( $dir ) {
		if( is_writable( $dir ) ) {
			$this->up_dir = $dir;
			return TRUE;
		} else return FALSE;
	}
	function get_upload_dir() {
		return $this->up_dir;
	}
	
	function set_max_file_size( $size ) {
		if( is_numeric( $size ) ) {
			$this->max_file_size = $size;
			return TRUE;
		} return FALSE;
	}
	function set_file_type( $type ) {
		if( is_array( $type ) ) {
			$this->file_type = $type;
			return TRUE;
		} return FALSE;
	}
	
	function setup_data( $name, $data ) {
		$this->data[$name] = $data;
	}
	function get_data( $name ) {
		return $this->data[$name];
	}
	
	function upload( $name, $up_name, $label = "ファイル" ) {
		$flag = TRUE;
		$this->error_message[$name] = NULL;
		
		if ( $_FILES[$name]["size"] !== 0 && ! empty( $_FILES[$name] ) ) {
			$extension = pathinfo($_FILES[$name]["name"], PATHINFO_EXTENSION);
			if( $_FILES[$name]["size"] > $this->max_file_size*1024*1024 ) {
				$flag = false;
				$this->error_message[$name][] .= sprintf( "%sのサイズが大きすぎます", $label );
			}
			if( !empty($this->file_type) ) {
				if(! in_array($extension, $this->file_type) ) {
					$flag = false;
					$this->error_message[$name][] .= sprintf( "%sが指定の型式で選択されていません", $label );
				}
			}
			
			if( $flag ) {
				$file_place = sprintf( "%s/%s.%s", $this->up_dir, $up_name, $extension );
				if( @move_uploaded_file( $_FILES[$name]["tmp_name"], $file_place ) ) {
					$data = array();
					list( $data["width"], $data["height"], $data["type"], $data["attr"] ) = getimagesize( $file_place );
					$data["type"] = image_type_to_mime_type( $data["type"] );
					$data["file_name"] = $_FILES[$name]["name"];
					$data["name"] = $up_name.".".$extension;
					$this->setup_data( $name, $data );
				} else {
					$flag = FALSE;
					$this->error_message[$name][] = sprintf( "%sのアップロードに失敗しました", $label );
				}
			}
		} else {
			$flag = FALSE;
			$this->error_message[$name][] = sprintf( "%sが選択されていません", $label );
		}
		return $flag;
	}
	
	function delete( $name ) {
		$file_place = sprintf( "%s/%s", $this->up_dir, $name );
		return @ unlink( $file_place );
	}
	
	function get_error_message( $name ) {
		return $this->error_message[$name];
	}
}