<?php
/* -----------------------------
 HTML生成関数
 /app/LIP/function/html.php
 --
 @written 12-11-30 SUSH
----------------------------- */

/* -----------------------------
 エスケープ
 	htmlspecialchars( $str, ENT_QUOTES )のエイリアス
 String _H( $str )
 --
 @param String $str
----------------------------- */
function _H( $str ) {
	return htmlspecialchars( $str, ENT_QUOTES );
}

/* -----------------------------
 エスケープして出力
 String _E( $str )
 --
 @param String $str
----------------------------- */
function _E( $str ) {
	echo _H( $str );
}

/* -----------------------------
 エスケープせずに出力
 String _EC( $str )
 --
 @param String $str
----------------------------- */
function _R( $str ) {
	echo $str;
}

/* -----------------------------
 HTMLタグ生成
 String makeHTML( $tag, $attributes=NULL, $inner=NULL, $flag=TRUE )
 --
 @param String $tag
 	タグ名
 @param Array $attributes
 	属性
 @param String $inner
 	タグ内要素
 @param Boolean $flag
 	$innerをエスケープするかどうか
----------------------------- */
function makeHTML( $tag, $attributes=NULL, $inner=NULL, $flag=TRUE ) {
	$attr = make_attribute($attributes);
	return sprintf( '<%s%s>%s</%s>', _H($tag), $attr, $flag?_H($inner):$inner, _H($tag) );
}

/* -----------------------------
 空要素HTMLタグ生成
 String makeHTML_single( $tag, $attributes=NULL )
 --
 @param String $tag
 	タグ名
 @param Array $attributes
 	属性
----------------------------- */
function makeHTML_single( $tag, $attributes=NULL ) {
	$attr = make_attribute($attributes);
	return sprintf( '<%s%s />', _H($tag), $attr );
}

/* -----------------------------
 HTML属性生成
 String make_attribute( $attributes )
 --
 @param Array $attributes
----------------------------- */
function make_attribute( $attributes ) {
	if( $attributes ) {
		$attribute = "";
		foreach( $attributes as $key => $val ) {
			if(! empty($key) && ! empty($val) ) {
				$attribute .= sprintf(' %s="%s"', _H($key), _H($val) );
			}
		}
		return $attribute;
	} else return "";
}

/* -----------------------------
 input type="checkbox"生成
 String checkbox( $name, $value, $flag=FALSE, $o=array() )
 --
 @param String $name
 @param String $value
 @param String $flag
 	TRUEの場合checked="checked"を付与する
 @param Array $o
 	HTML属性
----------------------------- */
function checkbox( $name, $value, $flag=FALSE, $o=array() ) {
	$o = array_merge( $o, array( "type"=>"checkbox", "name"=>$name, "value"=>$value ) );
	if( $flag ) $o["checked"] = "checked";
	return makeHTML_single( "input", $o );
}

/* -----------------------------
 input type="radio"生成
 String radio( $name, $value, $flag=FALSE, $o=array() )
 --
 @param String $name
 @param String $value
 @param String $flag
 @param Array $o
----------------------------- */
function radio( $name, $value, $flag=FALSE, $o=array() ) {
	$o = array_merge( $o, array( "type"=>"radio", "name"=>$name, "value"=>$value ) );
	if( $flag ) $o["checked"] = "checked";
	return makeHTML_single( "input", $o );
}

/* -----------------------------
 input type="text"生成
 String input( $name, $value, $o=array() )
 --
 @param String $name
 @param String $value
 @param Array $o
----------------------------- */
function input( $name, $value, $o=array() ) {
	$o = array_merge( $o, array( "name"=>$name, "value"=>$value, "type"=>"text" ) );
	return makeHTML_single( "input", $o );
}

/* -----------------------------
 input type="password"生成
 String password( $name, $value, $o=array() )
 --
 @param String $name
 @param String $value
 @param Array $o
----------------------------- */
function password( $name, $value, $o=array() ) {
	$o = array_merge( $o, array( "name"=>$name, "value"=>$value, "type"=>"password" ) );
	return makeHTML_single( "input", $o );
}

/* -----------------------------
 input type="hidden"生成
 String hidden( $name, $value, $o=array() )
 --
 @param String $name
 @param String $value
 @param Array $o
----------------------------- */
function hidden( $name, $value, $o=array() ) {
	$o = array_merge( $o, array( "type"=>"hidden", "name"=>$name, "value"=>$value ) );
	return makeHTML_single( "input", $o );
}

/* -----------------------------
 textarea生成
 String textarea( $name, $value, $o=array() )
 --
 @param String $name
 @param String $value
 @param Array $o
----------------------------- */
function textarea( $name, $value, $o=array() ) {
	$o["name"] = $name;
	return makeHTML( "textarea", $o, $value, FALSE );
}

/* -----------------------------
 input type="file"生成
 String upload( $name, $value, $o=array() )
 --
 @param String $name
 @param Array $o
----------------------------- */
function upload( $name, $o=array() ) {
	$o = array_merge( $o, array( "name"=>$name, "type"=>"file" ) );
	return makeHTML_single( "input", $o );
}

/* -----------------------------
 option生成
 String option( $label, $value, $flag = FALSE )
 --
 @param String $label
 	<option>ココ</option>
 @param String $value
 @param Boolean $flag
----------------------------- */
function option( $label, $value, $flag = FALSE ) {
	$o = array( "value"=>$value );
	if( $flag ) $o["selected"] = "selected";
	return makeHTML( "option", $o, $label, FALSE );
}