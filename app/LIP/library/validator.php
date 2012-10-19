<?php
/*
 * バリデート拡張クラス
 * /app/LIP/library/validator.php
 * --
 * ValidateData
 * array( "key" =>
 *   array( "require" =>
 *     array( "check" => 1, "error" => ErrorMessage )
 *   ),
 *   array( "mail" =>
 *     array( "check" => 1, "error" => ErrorMessage )
 *   )
 * )
 */
class LL_Validator extends LIP_Object {
	private $check,
			$data,
			$character = "utf-8";

	public function __construct() {
	}

	public function setValidateDate($check) {
		$this->check = $check;
	}
	
	public function checkValidation($data) {
		$this->data = $data;
		$flag = TRUE;
		if( !empty($this->check) ) { foreach( $this->check as $key=>$val ) {
			if( !$err = $this->checkRule( $key ) ) {
				/* success :) */
			} else {
				$flag = FALSE;
				$this->push_error( $key, $err );
			}
		} }
		return $flag;
	}
	
	public function checkRule($key) {
		/*
			require
			必須項目指定
		*/
		if( $this->check[$key]["require"]["check"] )
			if( !$this->checkRequire($key) )
				return $this->check[$key]["require"]["error"];
		
		/*
			isset
			指定項目入力時に必須項目
		*/
		if( !empty( $this->check[$key]["isset"]["check"] ) )
			if( !empty( $this->check[$key]["isset"]["val"] ) ) {
				if( $this->data[ $this->check[$key]["isset"]["key"] ] == $this->check[$key]["isset"]["val"] )
					if( !$this->checkRequire($key) )
						return $this->check[$key]["isset"]["error"];
			} else {
				if( !empty( $this->data[ $this->check[$key]["isset"]["key"] ] ) )
					if( !$this->checkRequire($key) )
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
			if( !$this->checkNumeric($key) )
				return $this->check[$key]["num"]["error"];
		
		/*
			kana
			カタカナ入力チェック
		*/
		if( !empty( $this->check[$key]["kana"]["check"] ) )
			if( !$this->checkKana($key) )
				return $this->check[$key]["kana"]["error"];
		
		return FALSE;
	}
	
	private function checkRequire($key) {
		if( count($this->check[$key]["require"]["and"])>0 ) {
			if( $this->checkForceEmpty( $this->data[ $key ] ) ) {
				return TRUE;
			} else {
				foreach( $this->check[$key]["require"]["and"] as $aKey ) {
					if( $this->checkForceEmpty( $this->data[ $aKey ] ) ) {
						return TRUE;
					}
				}
			}
			return FALSE;
		} else {
			if( $this->checkForceEmpty( $this->data[ $key ] ) ) {
				if( count($this->check[$key]["require"]["or"])>0 ) {
					foreach( $this->check[$key]["require"]["or"] as $oKey ) {
						if( $this->checkForceEmpty( $this->data[ $oKey ] ) ) {
						} else return FALSE;
					}
				}
				return TRUE;
			} else return FALSE;
		}
		return FALSE;
	}
	
	private function checkForceEmpty($str) {
		$str = $this->spaceRemove( $str );
		return !empty( $str );
	}
	
	private function spaceRemove( $str ) {
		return str_replace( " ", "", str_replace( "　", "", $str ) );
	}
	
	private function checkNumeric($key) {
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
	
	private function checkKana( $key ) {
		if( !empty($this->data[$key]) )
			return $this->khCheck( $this->data[$key], "K" );
		return TRUE;
	}
	
	private function khCheck($str,$flag){
		mb_regex_encoding($this->character);
		switch ($flag) {
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