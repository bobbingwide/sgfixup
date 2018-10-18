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
 * Number | Problem                                                             | Solutions
 * ------ | -------                                                             | -------------
 * 53     | broken images																												|
 * 11     | shortcodes not supported                                            | Remove  [box] shortcode
 * xxx    | Allow reviews	                                                      |
 * 3      | Customer services wrong - en dash character problem 
 * xxx    | Character fixing - for characters pasted from Word and elsewhere
 *        | h1's in products                                                    | hide using CSS
 *        | h2's in products																										| Copy to post_excerpt and hide in CSS
 *        | Product tags:                                                       | Remove in theme
 *        | links with target="_blank" rel="noopener"
 *        | telephone number, email and address hard coded
 *        | Style in tags: p, span 
 *        | See below for more detail...
 *        | youtube links
 
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

function oh_bugger_me() {
	print_r( ob_list_handlers() );
	echo "Level: ";
	echo ob_get_level(); 
	echo PHP_EOL;
	gob();
}

function run_count_fixups() {
	oik_require( "class-sgfixup.php", "sgfixup" );
	ini_set('memory_limit','2048M');
	// temporarily enable / disable the logic you want
	//apply_fixups();
	//report_fixups();
	//count_fixups();
	//apply_taxonomy_fixups();
	count_taxonomy_fixups();
	
	// count_missing_images();
}

add_action( "run_sgfixup.php", "run_count_fixups" );


//exit();


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
 * Counts the number of fixups needed for taxonomies
 */
function count_taxonomy_fixups() {
	echo "Type,ID,Title,box,image" . PHP_EOL;
	$taxonomies = array( "product_cat" );
	foreach ( $taxonomies as $taxonomy ) {
		do_taxonomy( $taxonomy );
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
			if ( false === $box ) {
				$box = strpos( $post->post_content, "[box" );
			}
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


/**
 * Applies fixups to all terms in a taxonomy
 *
     [124] => WP_Term Object
        (
            [term_id] => 55
            [name] => Suzuki Wing Mirrors
            [slug] => suzuki-wing-mirrors
            [term_group] => 0
            [term_taxonomy_id] => 55
            [taxonomy] => product_cat
            [description] => [box style="rounded"]
<h2>Suzuki wing mirrors by Bikeit.</h2>
Theses quality direct replacement Bikeit Suzuki wing mirrors are great value for money.

Easy to install, no modification needed.

[/box]
 * 
 * @param string $taxonomy the taxonomy name
 */
function do_taxonomy( $taxonomy ) {
	//$fixup = new sgfixup();
	$terms = get_terms( $taxonomy );
	//print_r( $terms );
	//gob();
	foreach ( $terms as $term ) {
		//$html = $fixup->get_post_html( $post->ID, $post);
		$box = strpos( $term->description, "[/box]" );
    if ( false === $box ) {
			$box = strpos( $term->description, "[box" );
		}
		$csv = array();
		$csv[] = $term->taxonomy;
		$csv[] = $term->term_id;
		$csv[] = '"' . $term->name . '"';
		$csv[] = $box; 
		$csv[] = get_term_image( $term );
		//$csv[] = count( $apple );
		//$csv[] = count( $mbr );
		//$csv[] = $bad_email;
		echo implode( ",", $csv );
		echo PHP_EOL;
	}	
}

	
/**
 * Applies the fixups needed
 * 
 */
function apply_taxonomy_fixups() {

	remove_filter( "pre_term_description", "wp_filter_kses", 10 );

	$fixup = new sgfixup();
	$taxonomies = array( "product_cat" );

	foreach ( $taxonomies as $taxonomy ) {
	
		$terms = get_terms( $taxonomy );
    foreach ( $terms as $term ) {
			$fixup->apply_term_fixups( $term );
			
		}
	}
}

function convert_html_to_text() {

	$str = $html->save();
	echo $str;
}


function get_term_image( $term ) {
	$term_image = array();
	$thumbnail_id = get_term_meta( $term->term_id, "thumbnail_id", true );
	//echo $thumbnail_id . PHP_EOL;
	
	$term_image[] = $thumbnail_id;
	if ( $thumbnail_id ) {
		$thumbnail = get_attached_file( $thumbnail_id, true );
		//echo $thumbnail . PHP_EOL;
		$term_image[] = $thumbnail;
		$term_image[] = check_file_exists( $thumbnail );
	}
	
	//gob();
	return implode( ",", $term_image );
}

function check_file_exists( $file ) {
	$size = null;
	//echo PHP_EOL;
			
	$exists = file_exists( $file );
	if ( !$exists ) {
		echo "File does not exist" . PHP_EOL;
		//gob();
	
	}	else {
		$size = filesize( $file );
		echo $size; 
	}
	return $size;
}



