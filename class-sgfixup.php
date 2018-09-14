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
	
	/**
	 * Apply fixups to the content
	 * 
	 */
	//$fixups = array( $this
	
	function apply_fixups( $ID, $post ) {
		echo "Applying fixups for $ID"; 
		$content = $post->post_content;
		$content = $this->fixup_box_shortcode( $content );
		$content = $this->fixup_dashes( $content );
		$this->update_post( $post, $content );
		echo PHP_EOL;
  }
	
	function fixup_box_shortcode( $content ) {
		if ( false !== strpos( $content, "[box" ) ) {
			echo PHP_EOL;
			echo $content;
			//$content = str_replace( "[box sty
			$content = str_replace( '[box style="rounded"]', '', $content );
			$content = str_replace( '[/box]', '', $content );
			echo $content;
		 
		}	else { 
			echo " No box";
		}
		return $content;
	}
	
	/**
	 * Fixup dashes.
	 * 
	 * Some hyphens get converted to mdash or ndash which can go wrong with UTF-8 imported as latin.
	 * The right way of dealing with the issue is to export and import with the correctly defined character set.
	 * using the `--default-character-set=utf8` parameter on the mysql import
	 * 
	 * This will prevent characters which have been exported as UTF-8 from being doubly converted. 
	 * Lord knows why MySQL dump files contain stuff like this.
	 * `/ *!40101 SET NAMES utf8 * /;`  - note spaces added between *'s and /'s for PHP benefit
	 * 
	 * So - becomes – which becomes â€“
	 */
	
	function fixup_dashes( $content ) {
		$content = str_replace( " – ", " - ", $content );
		$content = str_replace( "â€“", "-", $content );
		return $content;
  }
	
	function update_post( $post, $content ) {
		if ( $content != $post->post_content ) {
			$post->post_content = $content;
			wp_update_post( $post );
			echo "Updated {$post->ID}" ;
		}
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
