<?php
/*
 * インポートファイル
 * /app/include/import.php
 */
do {
	$dir = dirname( __FILE__ );
	require_once( $dir."/LIP.php" );

	/* Includes */
	require_once( $dir."/include/object.php" );
	require_once( $dir."/include/boot.php" );
	require_once( $dir."/include/load.php" );
	require_once( $dir."/include/url.php" );
	require_once( $dir."/include/hook.php" );
	require_once( $dir."/include/config.php" );
	require_once( $dir."/include/controller.php" );
	require_once( $dir."/include/model.php" );

	/* Libraries */
	require_once( $dir."/library/session.php" );
	require_once( $dir."/library/file.php" );
	require_once( $dir."/library/pager.php" );

	/* Functions */
	require_once( $dir."/function/common.php" );
	require_once( $dir."/function/html.php" );
	require_once( $dir."/function/url.php" );
} while(0);