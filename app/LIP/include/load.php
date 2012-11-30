<?php
/* -----------------------------
 LIP_Load : ローダークラス
 /app/LIP/include/load.php
 --
 @written 12-11-30 SUSH
----------------------------- */

class LIP_Load extends LIP_Object {

	/* -----------------------------
	 コンストラクタ
	 Void __construct()
	----------------------------- */
	public function __construct() {
	}

	/* -----------------------------
	 ライブラリーの読み込み
	 Void load_library( $library )
	 --
	 @param String $library
	----------------------------- */
	static public function load_library( $library ) {
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
			if(! isset( $LIP->$library ) ) {
				$LIP->$library =& $l;
			}
			$LIP->l[$library] =& $l;
		}
		return $l;
	}
}


/* -----------------------------
 LIP_Load->load_libraryのエイリアス
 Void load_library( $library )
 --
 @param String $library
----------------------------- */
if(! function_exists( 'load_library' ) ) {
	function load_library( $library ) {
		return LIP_Load::load_library( $library );
	}
}