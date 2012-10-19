<?php
/*
 * ローダークラス
 * /app/LIP/include/load.php
 */

class LIP_Load extends LIP_Object {
	/* ####################################
	   PUBLIC FUNCTION
	#################################### */
	public function __construct() {
	}
	public function load_library( $library ) {
		$LIP =& get_instance();
		if( @array_key_exists( $library, $LIP->l ) ) {
			$l =& $LIP->l[$library];
		} else {
			$file = sprintf( "%s/library/%s.php", app_dir(), $library );
			if( ! file_exists( $file ) ) {
				$file = sprintf( "%s/LIP/library/%s.php", app_dir(), $library );
				if( ! file_exists( $file ) ) {
					return FALSE;
				}
			}
			require_once( $file );


			$l = "LL_".ucfirst( $library );
			$l = new $l();
			if(! isset( $this->$library ) ) {
				$LIP->$library =& $l;
			}
			$LIP->l[$library] =& $l;
		}
		return $l;
	}
}

if(! function_exists( 'load_library' ) ) {
	function load_library( $library ) {
		$LIP =& get_instance();
		return $LIP->load->load_library( $library );
	}
}