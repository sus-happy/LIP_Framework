<?php

// System Debug
define( 'LIP_DEBUG_MODE', FALSE );
// Authorize Skip Debug
define( 'LIP_AUTH_DEBUG_MODE', FALSE );
if( LIP_DEBUG_MODE === TRUE ) {
	ini_set( 'error_reporting', E_ALL ^E_NOTICE );
	ini_set( 'display_errors', TRUE );
}

$config = array();
$config["hide"] = array( "system", "database" );

/*
 * System Settings
 */
$config["system"]["base_dir"] = dirname(__FILE__);
$config["system"]["base_url"] = ( (false === empty($_SERVER['HTTPS']))&&('off' !== $_SERVER['HTTPS']) ) ? "https://" : "http://".getenv("HTTP_HOST").str_replace( getenv('DOCUMENT_ROOT'), "", str_replace("\\", "/", dirname(__FILE__) ) )."/";
$config["system"]["app_dir"] = dirname(__FILE__)."/app";

/*
 * Authorize Settings
 */
$config["auth"]["path"] = "admin";
$config["auth"]["login"] = "admin/login";
$config["auth"]["check"] = array(
	array( "(admin.*)", "(.*)" ),
);

/*
 * Index Settings
 */
$config["index"]["path"] = "gate";
$config["index"]["func"] = "index";

/*
 * Site Settings
 */
$config["site"]["name"] = "LIP Framework System";
$config["site"]["url"] = $config["system"]["base_url"];
$config["site"]["analyze"] = "MOD_REWRITE"; // PATH_INFO, MOD_REWRITE

/*
 * Database Settings
 */
$config["database"]["enable"] = TRUE;
$config["database"]["type"] = "mysql";
$config["database"]["host"] = "localhost";
$config["database"]["user"] = "user_name";
$config["database"]["pass"] = "password";
$config["database"]["dbname"] = "database";
$config["database"]["charset"] = "utf8";

/*
 * Session Settings
 */
$config["session"]["sess_cookie_name"] = "ex_session";

/*
 * Use Plugin Programs
 */
$config["plugin"]["dir"] = $config["system"]["app_dir"]."/plugin";
$config["plugin"]["use"] = array();

/*
 * Use Library Programs
 */
$config["library"]["use"] = array( "session" );

/*
 * Import Files
 */
require_once( $config["system"]["app_dir"]."/LIP/import.php" );
