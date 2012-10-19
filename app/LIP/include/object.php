<?php

class LIP_Object {
	private $error;

	public function __construct() {
	}

	/*
		Void push_error( $key, $message )
		$key String
		$message String
		--
		エラーメッセージの追加
	*/
	public function push_error( $key, $message ) {
		if( !empty( $this->error[$key] ) ) {
			if( is_array( $this->error[$key] ) ) {
			if( is_array( $message ) )
				$this->error[$key] += $message;
			else
				$this->error[$key][] = $message;
			} else {
			$tmp = $this->error[$key];
			unset( $this->error[$key] );
			if( is_array( $message ) ) {
				$this->error[$key][] = $tmp;
				$this->error[$key] += $message;
			} else
				$this->error[$key] = array( $tmp, $message );
			}
		} else
		$this->error[$key] = $message;
	}

	/*
		String get_error_text( $key = NULL )
		$key String = NULL
		--
		エラーメッセージを取得。
		"$key"が空の場合は、全てのエラーメッセージを取得。
	*/
	public function get_error_text( $key = NULL ) {
		if( !empty( $this->error ) ) {
			if(! $key ) return $this->error;
			if(! empty($this->error[$key]) ) {
				return $this->error[$key];
			}
		}
		return NULL;
	}

	/*
		Boolean get_check_error( $key = NULL )
		$key String = NULL
		--
		エラーが発生しているかチェック。
		"$key"が空の場合は、全てのエラーで確認。
	*/
	public function get_check_error( $key = NULL ) {
		if( !empty( $this->error ) ) {
			if(! $key ) return TRUE;
			if(! empty($this->error[$key]) ) {
				return TRUE;
			}
		}
		return FALSE;
	}



	/*
		Boolean error_dump( $hide = FALSE )
		$hide Boolean = FALSE
		--
		エラー変数を全表示
		$hideが正の時は、HTMLのコメントアウトを行なって表示する。
	*/
	public function error_dump( $hide = FALSE ) {
		echo $hide ? '<!--' : '<pre>';
		var_dump($this->check);
		echo $hide ? '-->' : '</pre>';
	}
}