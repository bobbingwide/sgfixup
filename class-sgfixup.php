<?php 

/** 
 * @copyright (C) Copyright Bobbing Wide 2018
 * @package sgfixup
 * 
 */

class sgfixup {

	private $posts;
	
	function __construct() {
		if ( !function_exists( "str_get_html" ) ) {
			oik_require( "simple_html_dom.php", "sgfixup" );
		}
		if ( !function_exists( "bw_get_posts" ) ) {
			oik_require( "includes/bw_posts.php" );
		}
		
	}
	
	/**
	 * Only get published posts
	 * Get all posts, not just those with post_parent=0
	 *
	 */
	function get_posts( $post_type ) {
		$atts = array( "post_type" => $post_type
							 	, "numberposts" => -1
								, "post_parent" => '.'
								);
		$this->posts = bw_get_posts( $atts );
		return $this->posts;							
	}
	
	function get_post_html( $ID, $post ) {
	
		$html = str_get_html( $post->post_content );
		//print_r( $html );
		return $html;
	
	}
	
/*	
	
	
function process_gallery( $file ) {
  $html = file_get_html( $file );
  $links = $html->find( "a[href^=/gallery]" );
  $prev = null;
  foreach ( $links as $key => $link ) {
    $curr = $link->href;
    if ( $curr != $prev ) {
      echo $curr; 
      process_curr( $curr );
    echo PHP_EOL;
    }
    $prev = $curr;   
    
  }  

}
		
*/	
								

}
