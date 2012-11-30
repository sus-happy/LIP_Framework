<?php
/* -----------------------------
 LIP_Hook : アクションフッククラス
 /app/LIP/include/hook.php
 --
 @written 12-11-30 SUSH
----------------------------- */

class LIP_Hook extends LIP_Object {
	private $hook	= array(),
			$ss		= NULL;

	/* -----------------------------
	 コンストラクタ
	 Void __construct()
	----------------------------- */
	public function __construct() {
	}

	/* -----------------------------
	 フックの設定
	 Void action_hook( $hook, $action, $params = NULL )
	 --
	 @param String $hook
	 @param String $action
	 @param Mixed $params
	----------------------------- */
	public function action_hook( $hook, $action, $params = NULL ) {
		if(! is_array( $this->hook[$hook] ) )
			$this->hook[$hook] = array();
		$this->hook[$hook][] = array( 'func' => $action, 'params' => $params );
	}


	/* -----------------------------
	 フックの発火
	 Mixed run_hook( $hook )
	 --
	 @param String $hook
	----------------------------- */
	public function run_hook( $hook ) {
		if( $this->hook[$hook] ) {
			foreach( $this->hook[$hook] as $action ) {
				if( is_callable( $action['func'] ) )
					return call_user_func_array( $action['func'], is_array( $action['params'] ) ? $action['params'] : array( $action['params'] ) );
			}
		}
		return FALSE;
	}
}

/* -----------------------------
 LIP_Hook->action_hook のエイリアス
 Void action_hook( $hook, $action, $params = NULL )
 --
 @param String $hook
 @param String $action
 @param Mixed $params
----------------------------- */
if(! function_exists( 'action_hook' ) ) {
	function action_hook( $hook, $action, $params = NULL ) {
		$LIP =& get_instance();
		$LIP->hook->action_hook( $hook, $action, $params );
	}
}

/* -----------------------------
 LIP_Hook->run_hook のエイリアス
 Mixed action_hook( $hook )
 --
 @param String $hook
----------------------------- */
if(! function_exists( 'run_hook' ) ) {
	function run_hook( $hook ) {
		$LIP =& get_instance();
		return $LIP->hook->run_hook( $hook );
	}
}