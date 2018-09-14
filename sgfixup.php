<?php 

/** 
 * @copyright (C) Copyright Bobbing Wide 2018
 * @package sgfixup
 * 
 * oik batch routine to fix up the HTML in Ascentor's posts
 *
 * Syntax: 
 * `
 * cd [path]/wp-content/plugins/sgfixup
 * oikwp sgfixup.php 
 * `
 *
 * Processing
 * 1. Sets featured image for all posts
 * 2. Tidies up heading levels
 * 3. Removes unexpected style tags
 
 

53 broken images
111 shortcodes  - [box] can be removed.
xxx Allow reviews

3 Customer services wrong
49 ¶ converted to Â 

youtube links 
h1
h2


 * 
 * 
 * - Uses simple_html_dom to parse the post_content for each post
 * - Depends on oik, since it runs under oikwp
 * 
 * post_types	| count
 * ---------- | -----
 * product		| 140
 * page 			| 142

 
 */


oik_require( "class-sgfixup.php", "sgfixup" );

ini_set('memory_limit','2048M');

count_fixups();


//echo "Pages: " . count( $posts ) . PHP_EOL;
function count_fixups() {

	echo "Type,ID,Title,thumbnail,#img,#h1,#h2,#style,youtube" . PHP_EOL;
	$post_types = array( "page", "product" );

	foreach ( $post_types as $type ) {
		do_post_type( $type );
	}
}


/**
 * Process a
 */	

	
	

function do_post_type( $type ) {
	$fixup = new sgfixup();
	$posts = $fixup->get_posts( $type );

	foreach ( $posts as $post ) {
		//echo $post->ID . $post->post_title;
		//echo PHP_EOL;
		//echo $post->post_content;
		//echo PHP_EOL;
		$html = $fixup->get_post_html( $post->ID, $post);
		//echo $html->plaintext;
		if ( !$html ) {
			//echo "No HTML.";
			$images = array();
			$h1 = array();
			$style = array();
		}	else {
		
			$images = $html->find( "img" );
			$h1 = $html->find( "h1" );
			$h2 = $html->find( "h2" );
			$style = $html->find( "*[style]" );
			//$apple = $html->find( ".Apple-style-span" );
			//$mbr = $html->find( "mbr" );
			$bad_email = false !== strpos( $post->post_content, "ascentor.dev" );
					}
		$csv = array();
		$csv[] = $post->post_type;
		$csv[] = $post->ID;
		$csv[] = '"' . $post->post_title . '"';
		$csv[] = get_post_thumbnail_id( $post->ID );
		$csv[] = count( $images );
		$csv[] = count( $h1 );
		$csv[] = count( $h2 );
		$csv[] = count( $style );
		//$csv[] = count( $apple );
		//$csv[] = count( $mbr );
		$csv[] = $bad_email;
		echo implode( ",", $csv );
		echo PHP_EOL;
	}	
}	

function convert_html_to_text() {

	$str = $html->save();
	echo $str;
}



