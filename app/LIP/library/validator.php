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
class LL_Validator {
	var $check,
		$error,
		$data,
		$character = "utf-8";

	function LL_Validator() {
	}

	function setValidateDate($check) {
		$this->check = $check;
	}
	
	function checkValidation($data) {
		$this->data = $data;
		$flag = TRUE;
		if( !empty($this->check) ) { foreach( $this->check as $key=>$val ) {
			if( !$err = $this->checkRule( $key ) ) {
				/* success :) */
			} else {
				$flag = FALSE;
				if( !is_array($this->error) ) {
					$this->error = array();
				}
				$this->error[ $key ] = $err;
			}
		} }
		return $flag;
	}
	
	function checkRule($key) {
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
	
	function checkRequire($key) {
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
	
	function checkForceEmpty($str) {
		$str = $this->spaceRemove( $str );
		return !empty( $str );
	}
	
	function spaceRemove( $str ) {
		return str_replace( " ", "", str_replace( "　", "", $str ) );
	}
	
	function checkNumeric($key) {
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
	
	function checkKana( $key ) {
		if( !empty($this->data[$key]) )
			return $this->khCheck( $this->data[$key], "K" );
		return TRUE;
	}
	
	function khCheck($str,$flag){
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

	function dump() {
		var_dump($this->check);
	}
	
	function pushError( $key, $message ) {
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
	function getErrorText( $key, $before = '<div class="caution">', $after = '</div>', $glue = '' ) {
		if( !empty( $this->error ) ) {
			if( !empty($this->error[$key]) ) {
				if( is_array( $this->error[$key] ) ) {
					return $before.implode( $glue, $this->error[$key] ).$after;
				}
				return $before.$this->error[$key].$after;
			}
		}
		return FALSE;
	}
	function getErrorTextList() {
		if( !empty( $this->error ) ) {
			$result = "<ul>";
			foreach ( $this->error as $key => $val ) {
				$result .= $this->getErrorText( $key, '<li>', '</li>', '</li><li>' );
			}
			return $result .= "</ul>";
		}
		return FALSE;
	}
	function getCheckError($key) {
		if( !empty( $this->error ) ) {
			if( !empty($this->error[$key]) ) {
				return "caution";
			}
		}
		return FALSE;
	}
}