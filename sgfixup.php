<?php 

/** 
 * @copyright (C) Copyright Bobbing Wide 2018
 * @package sgfixup
 * 
 * oik batch routine to fix up the HTML in SG Motorsport's posts and categories
 *
 * Syntax: 
 * `
 * cd [path]/wp-content/plugins/sgfixup
 * oikwp sgfixup.php 
 * `
 *
 * Processing:
 * 
 * Number | Problem | Solutions
 * ------ | ------- | -------------
 * 53     | broken images
 * 11     | shortcodes  - [box] can be removed 
 * xxx    | Allow reviews
 * 3      | Customer services wrong - en dash character problem 
 * xxx    | Character fixing - for characters pasted from Word and elsewhere
 *        | youtube links
 *        | h1's in products
 *        | h2's in products
 *        | links with target="_blank" rel="noopener"
 *        | telephone number, email and address hard coded
 *        | Style in tags: p, span 
 
 * 
 * - Uses simple_html_dom to parse the post_content for each post
 * - Depends on oik, since it runs under oikwp
 * 
 * content type | count
 * ------------ | -----
 * product		  | 1261
 * page 			  | 18
 * product_cat  | 126

 
 */
 

if ( PHP_SAPI !== "cli" ) { 
	die();
}


oik_require( "class-sgfixup.php", "sgfixup" );

ini_set('memory_limit','2048M');

// temporarily prevent fixups
//apply_fixups();
report_fixups();

//count_fixups();


exit();


//echo "Pages: " . count( $posts ) . PHP_EOL;

/**
 * Counts the number of fixups needed
 * 
 * For Products we'd expect this to be reduced to 0 across the board
 * For Pages it's a bit different
 */
function count_fixups() {

	echo "Type,ID,Title,thumbnail,#img,#h1,#h2,#style,#pstyle,#sstyle,box,badchars,services" . PHP_EOL;
	$post_types = array( "page", "product" );

	foreach ( $post_types as $type ) {
		do_post_type( $type );
	}
}


/**
 * Applies the fixups needed
 * 
 */
function apply_fixups() {

	$post_types = array( "product" );

	foreach ( $post_types as $type ) {
	
		$fixup = new sgfixup();
		$posts = $fixup->get_posts( $type );
    foreach ( $posts as $post ) {
			echo $post->ID . $post->post_title;
			echo PHP_EOL;
			//echo $post->post_content;
			//echo PHP_EOL;
			$fixup->apply_fixups( $post->ID, $post );
		}
	}
}


/**
 * Applies the fixups needed
 * 
 */
function report_fixups() {

	$post_types = array( "product" );

	foreach ( $post_types as $type ) {
	
		$fixup = new sgfixup();
		$posts = $fixup->get_posts( $type );
    foreach ( $posts as $post ) {
			echo $post->ID . $post->post_title;
			echo PHP_EOL;
			//echo $post->post_content;
			//echo PHP_EOL;
			$fixup->report_fixups( $post->ID, $post );
		}
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
			$pstyle = $html->find( "p[style]" );
			
			//print_r( $pstyle );
			
			$sstyle = $html->find( "span[style]" );
			//$apple = $html->find( ".Apple-style-span" );
			//$mbr = $html->find( "mbr" );
			//$bad_email = false !== strpos( $post->post_content, "ascentor.dev" );
			$box = strpos( $post->post_content, "[/box]" );
			$badchars = strpos( $post->post_content, " " );
			$services = strpos( $post->post_content, "Customer services" );
			
			
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
		$csv[] = count( $pstyle );
		$csv[] = count( $sstyle );
		$csv[] = $box; 
		$csv[] = $badchars;
		$csv[] = $services;
		//$csv[] = count( $apple );
		//$csv[] = count( $mbr );
		//$csv[] = $bad_email;
		echo implode( ",", $csv );
		echo PHP_EOL;
	}	
}	

function convert_html_to_text() {

	$str = $html->save();
	echo $str;
}



