<?php
/*
 * HTML関数群
 * /app/LIP/function/html.php
 */

function _H( $str ) {
	return htmlspecialchars( $str, ENT_QUOTES );
}

function makeHTML( $tag, $attributes=NULL, $inner=NULL, $flag=TRUE ) {
	$attr = make_attribute($attributes);
	return sprintf( '<%s%s>%s</%s>', _H($tag), $attr, $flag?_H($inner):$inner, _H($tag) );
}
function makeHTML_single( $tag, $attributes=NULL ) {
	$attr = make_attribute($attributes);
	return sprintf( '<%s%s />', _H($tag), $attr );
}
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

function checkbox( $name, $value, $flag=FALSE, $o=array() ) {
	$o = array_merge( $o, array( "type"=>"checkbox", "name"=>$name, "value"=>$value ) );
	if( $flag ) $o["checked"] = "checked";
	return makeHTML_single( "input", $o );
}

function radio( $name, $value, $flag=FALSE, $o=array() ) {
	$o = array_merge( $o, array( "type"=>"radio", "name"=>$name, "value"=>$value ) );
	if( $flag ) $o["checked"] = "checked";
	return makeHTML_single( "input", $o );
}

function input( $name, $value, $o=array() ) {
	$o = array_merge( $o, array( "name"=>$name, "value"=>$value, "type"=>"text" ) );
	return makeHTML_single( "input", $o );
}

function password( $name, $value, $o=array() ) {
	$o = array_merge( $o, array( "name"=>$name, "value"=>$value, "type"=>"password" ) );
	return makeHTML_single( "input", $o );
}

function hidden( $name, $value, $o=array() ) {
	$o = array_merge( $o, array( "type"=>"hidden", "name"=>$name, "value"=>$value ) );
	return makeHTML_single( "input", $o );
}

function textarea( $name, $value, $o=array() ) {
	$o["name"] = $name;
	return makeHTML( "textarea", $o, $value, FALSE );
}

function upload( $name, $o=array() ) {
	$o = array_merge( $o, array( "name"=>$name, "type"=>"file" ) );
	return makeHTML_single( "input", $o );
}

function option( $label, $value, $flag = FALSE ) {
	$o = array( "value"=>$value );
	if( $flag ) $o["selected"] = "selected";
	return makeHTML( "option", $o, $label, FALSE );
}