<?php
/* -----------------------------
 LL_Session : バリデート拡張クラス
 /app/LIP/library/validator.php
 --
 @written 12-11-30 SUSH
 @todo クラス上でルール登録できるようにする
----------------------------- */

class LL_Validator extends LIP_Object {
	private $check,
			$data,
			$character = "utf-8";

	/* -----------------------------
	 コンストラクタ
	 Void __construct()
	----------------------------- */
	public function __construct() {
		$this->disable_exeption_flag();
	}

	/* -----------------------------
	 バリデートルールの登録
	 Void set_validate_data( $check )
	 --
	 @param Array $check
	----------------------------- */
	public function set_validate_data($check) {
		$this->check = $check;
	}

	/* -----------------------------
	 バリデーション開始
	 Void check_validation( $data )
	 --
	 @param Array $check
	----------------------------- */
	public function check_validation($data) {
		$this->data = $data;
		$flag = TRUE;
		if( !empty($this->check) ) { foreach( $this->check as $key=>$val ) {
			if( !$err = $this->check_rule( $key ) ) {
				/* success :) */
			} else {
				$flag = FALSE;
				$this->push_error( $key, $err );
			}
		} }
		return $flag;
	}

	/* -----------------------------
	 ルール検証
	 Boolean check_rule( $key )
	 --
	 @param String $key
	----------------------------- */
	public function check_rule( $key ) {
		/*
			require
			必須項目指定
		*/
		if( $this->check[$key]["require"]["check"] )
			if( !$this->check_require($key) )
				return $this->check[$key]["require"]["error"];

		/*
			isset
			指定項目入力時に必須項目
		*/
		if( !empty( $this->check[$key]["isset"]["check"] ) )
			if( !empty( $this->check[$key]["isset"]["val"] ) ) {
				if( $this->data[ $this->check[$key]["isset"]["key"] ] == $this->check[$key]["isset"]["val"] )
					if( !$this->check_require($key) )
						return $this->check[$key]["isset"]["error"];
			} else {
				if( !empty( $this->data[ $this->check[$key]["isset"]["key"] ] ) )
					if( !$this->check_require($key) )
						return $this->check[$key]["isset"]["error"];
			}

		/*
			equal
			指定項目と同値
		*/
		if( !empty( $this->check[$key]["equal"]["check"] ) )
			if( $this->data[ $key ] !== $this->data[ $this->check[$key]["equal"]["key"] ] )
				return $this->check[$key]["equal"]["error"];

		/*
			mail
			メールアドレスチェック
		*/
		if( !empty( $this->check[$key]["mail"]["check"] ) ) {
			if( !empty($this->data[ $key ]) && !preg_match("/^([a-z0-9_]|\-|\.|\+)+@(([a-z0-9_]|\-)+\.)+[a-z]{2,6}$/i", $this->data[ $key ]) )
				return $this->check[$key]["mail"]["error"];
		}

		/*
			num
			数字入力チェック
		*/
		if( !empty( $this->check[$key]["num"]["check"] ) )
			if( !$this->check_numeric($key) )
				return $this->check[$key]["num"]["error"];

		/*
			kana
			カタカナ入力チェック
		*/
		if( !empty( $this->check[$key]["kana"]["check"] ) )
			if( !$this->check_kana($key) )
				return $this->check[$key]["kana"]["error"];

		return FALSE;
	}

	/* -----------------------------
	 未入力検証
	 Boolean check_require( $key )
	 --
	 @param String $key
	----------------------------- */
	private function check_require( $key ) {
		if( count($this->check[$key]["require"]["and"])>0 ) {
			if( $this->check_deep_empty( $this->data[ $key ] ) ) {
				return TRUE;
			} else {
				foreach( $this->check[$key]["require"]["and"] as $aKey ) {
					if( $this->check_deep_empty( $this->data[ $aKey ] ) ) {
						return TRUE;
					}
				}
			}
			return FALSE;
		} else {
			if( $this->check_deep_empty( $this->data[ $key ] ) ) {
				if( count($this->check[$key]["require"]["or"])>0 ) {
					foreach( $this->check[$key]["require"]["or"] as $oKey ) {
						if( $this->check_deep_empty( $this->data[ $oKey ] ) ) {
						} else return FALSE;
					}
				}
				return TRUE;
			} else return FALSE;
		}
		return FALSE;
	}

	/* -----------------------------
	 空白文字も空として扱う
	 Boolean check_deep_empty( $str )
	 --
	 @param String $str
	----------------------------- */
	private function check_deep_empty( $str ) {
		$str = $this->space_remove( $str );
		return !empty( $str );
	}

	/* -----------------------------
	 空白文字を削除
	 String space_remove( $str )
	 --
	 @param String $str
	----------------------------- */
	private function space_remove( $str ) {
		return str_replace( " ", "", str_replace( "　", "", $str ) );
	}

	/* -----------------------------
	 数字判定検証
	 Boolean check_numeric( $key )
	 --
	 @param String $key
	----------------------------- */
	private function check_numeric($key) {
		if( empty($this->data[ $key ]) || ( !empty($this->data[ $key ]) && strval($this->data[ $key ]) == strval(intval($this->data[ $key ])) ) ) {
			if( count($this->check[$key]["num"]["or"])>0 ) {
				foreach( $this->check[$key]["num"]["or"] as $oKey ) {
					if( empty($this->data[ $oKey ]) || ( !empty($this->data[ $oKey ]) && strval($this->data[ $oKey ]) == strval(intval($this->data[ $oKey ])) ) ) {
					} else return FALSE;
				}
			}
			if( count($this->check[$key]["num"]["and"])>0 ) {
				foreach( $this->check[$key]["num"]["and"] as $aKey ) {
					if( empty($this->data[ $aKey ]) || ( !empty($this->data[ $aKey ]) && strval($this->data[ $aKey ]) == strval(intval($this->data[ $aKey ])) ) ) {
					} else return FALSE;
				}
			}
			return TRUE;
		} else return FALSE;
	}

	/* -----------------------------
	 カナ文字判定検証
	 Boolean check_kana( $key )
	 --
	 @param String $key
	 @todo ひらがな検証とかも作ったほうがいいかな？
	----------------------------- */
	private function check_kana( $key ) {
		if( !empty($this->data[$key]) )
			return $this->kh_check( $this->data[$key], "K" );
		return TRUE;
	}

	/* -----------------------------
	 カナ文字判定
	 Boolean kh_check( $str, $flag )
	 --
	 @param String $str
	 @param String $flag
	----------------------------- */
	private function kh_check( $str, $flag ){
		mb_regex_encoding($this->character);
		switch ( $flag ) {
			case "H":
				//ひらがなチェック
				if (!mbereg('^([あ-ん]|[ー 　]){1,16}$',$str,$this->character)) {
					return FALSE;
				}
				break;
			case "K":
				//カタカナチェック
				if (!mbereg('^([ァ-ヶ]|[ー 　]){1,16}$',$str,$this->character)) {
					return FALSE;
				}
				break;
			default:
				exit("specify 'H' or 'K'");
				break;
		}
		return TRUE;
	}
}