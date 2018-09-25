<?php

/**
 * Trying to respond to "init" won't work since this file is not loaded as a plugin
 */
add_action( "init", "fiddle_is_admin", 1 );

/**
 * Fails to do any init.
 *
 * 
 * Remove hooks that will mess with output?
 * 
 * OR mu plugin
 * deactivate plugins that will affect our output?
 */
function fiddle_is_admin() {
	gob();
}

add_action( "run_sgob.php", "oh_bugger_me" );

//oh_bugger_me();




function oh_bugger_me() {
	print_r( ob_list_handlers() );
	echo "Level: ";
	echo ob_get_level(); 
	echo PHP_EOL;
	//gob();
}
