<?php
/*
 * アクションフッククラス
 * /app/LIP/include/hook.php
 */

class LIP_Hook extends LIP_Object {
	private $hook	= array(),
			$ss		= NULL;

	/* ####################################
	   PUBLIC FUNCTION
	#################################### */
	public function __construct() {
		$this->ss = load_library( "session" );
	}

	public function action_hook( $hook, $action ) {
		if( is_array( $this->hook[$hook] ) )
			$this->hook[$hook] = arrau();
		$this->hook[$hook][] = $action;
	}

	public function run_hook( $hook ) {
		$params = $this->_get_hook_params( $hook );

		if( $this->hook[$hook] ) { 
			foreach( $this->hook[$hook] as $action ) {
				if( is_callable( $action ) )
					call_user_func_array( $action, is_array( $params ) ? $params : array( $params ) );
			}
		}
	}

	/* ####################################
	   PRIVATE FUNCTION
	#################################### */
	private function _get_hook_params( $hook ) {
		switch( $hook ) {
			case 'AUTH_SUCCESS':
				return LIP_AUTH_DEBUG_MODE === TRUE ? 1 : $this->ss->get_session("user_id");
			break;
		}
	}
}

if(! function_exists( 'action_hook' ) ) {
	function action_hook( $hook, $action ) {
		$LIP =& get_instance();
		$LIP->hook->action_hook( $hook, $action );
	}
}

if(! function_exists( 'run_hook' ) ) {
	function run_hook( $hook ) {
		$LIP =& get_instance();
		$LIP->hook->run_hook( $hook );
	}
}