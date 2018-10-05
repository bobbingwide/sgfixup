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
	 * Report fixups in the content
	 * 
	 */
	
	function report_fixups( $ID, $post ) {
		echo "Reporting fixups for $ID"; 
		echo PHP_EOL;
		$content = $post->post_content;
		$excerpt = $post->post_excerpt;
		//$content = $this->fixup_box_shortcode( $content );
		//$content = $this->fixup_dashes( $content );
		//$content = $this->fixup_p_styles( $content );
		//$content = $this->report_p_styles( $content );
		//$this->report_missing_image( $ID, $post );
		//$this->update_post( $post, $content, $excerpt );
		$this->report_p_see_below( $ID, $post );
		echo PHP_EOL;
  }
	
	/**
	 * Apply fixups to the content
	 * 
	 */
	//$fixups = array( $this
	
	function apply_fixups( $ID, $post ) {
		echo "Applying fixups for $ID"; 
		echo PHP_EOL;
		$content = $post->post_content;
		//$content = $this->fixup_box_shortcode( $content );
		//$content = $this->fixup_dashes( $content );
		//$content = $this->fixup_p_styles( $content );
		//$content = $this->report_p_styles( $content );
		//$content = $this->fixup_span_styles( $content );
		$excerpt = $post->post_excerpt;
		if ( empty( $excerpt ) ) {
			$excerpt = $this->extract_h2( $content );
			//$content = $this->remove_h2( $content );
		} else {
			echo "Already set: " ;
			echo $excerpt;
			echo PHP_EOL;
		}
				
		$this->update_post( $post, $content, $excerpt );
		echo PHP_EOL;
  }
	
	function apply_term_fixups( $term ) {
		echo "Applying fixups for term: " . $term->term_id . PHP_EOL;
		$description = $this->fixup_box_shortcode( $term->description );
		if ( $description != $term->description ) {
			//echo $description;
			//echo PHP_EOL;
      wp_update_term( $term->term_id, $term->taxonomy, array( "description" => $description, "filter" => "raw" ) );
			
			// exit();
		}
	
	}
	
	/**
	 * Updates the post, if the content or excerpt has changed
	 * 
	 * @param object $post The post object
	 * @param string $content the updated post_content
	 * @param string $excerpt the updated post_excerpt
   */
	function update_post( $post, $content, $excerpt ) {
		if ( ( $content != $post->post_content ) || ( $excerpt != $post->post_excerpt ) ) {
			$post->post_content = $content;
			$post->post_excerpt = $excerpt;
			wp_update_post( $post );
			echo "Updated {$post->ID}" ;
		} 
	}
	
	/**
	 * Eliminate the box shortcode from the content
	 */
	function fixup_box_shortcode( $content ) {
		if ( false !== strpos( $content, "[box" ) ) {
			echo PHP_EOL;
			echo $content;
			//$content = str_replace( "[box sty
			$content = str_replace( '[box style="rounded"]', '', $content );
			$content = str_replace( "[box style='rounded']", '', $content );
			$content = str_replace( "[box style='rounded]", '', $content );
			$content = str_replace( '[box style="rounded]', '', $content );
			
			$content = str_replace( '[/box]', '', $content );
			$content = trim( $content );
			echo PHP_EOL;
			echo $content;
			echo PHP_EOL;
		 
		}	else { 
			echo " No box";
		}
		return $content;
	}
	
	/**
	 * Fixup dashes.
	 * 
	 * Some hyphens get converted to en-dash which can go wrong with UTF-8 imported as latin.
	 * The right way of dealing with the issue is to export and import with the correctly defined character set.
	 * using the `--default-character-set=utf8` parameter on the mysql import
	 * 
	 * This will prevent characters which have been exported as UTF-8 from being doubly converted. 
	 * Lord knows why MySQL dump files contain stuff like this.
	 * `/ *!40101 SET NAMES utf8 * /;`  - note spaces added between *'s and /'s for PHP benefit
	 * 
	 * So - becomes – which becomes â€“
	 
	 
	 
 * Character fixing

Non b

https://www.fileformat.info/info/unicode/char/00a0/charset_support.htm

https://www.ascii-code.com/

Started as             | Unicode | Export UTF-8 | After import displayed as | Which is
-------------          | ------- | ------------ | ------------------------- | ------------ 
No-break space         | U+00A0  | c2a0         |	¶                         | Latin capital letter A with circumflex, Non-breaking space
En dash                | U+2013  | e28093       | â € ™                     | Latin small letter a with circumflex, Euro symbol, Left double quotation mark
Left single quotation  | U+								      | â € ˜											| 
Right single quotation |  
Left double quotation  | U+201C  | e2809c       | â € œ                     |  ", ", Latin small ligature oe
    



Originally | Which is | Converted to | Which is  | 	Like
---        | -----    |  ----------------  | --------- | -------           
 -         | Em dash  |   
				 Â   	 | Lower case a acute | C3 A1

160 A0 <span style="color: #ff0000;"><strong> </strong></span>

 

 


Hex 92  
’ right single quotation â€™   â€
	 */
	
	function fixup_dashes( $content ) {
		$content = str_replace( " – ", " - ", $content );
		$content = str_replace( "â€“", "-", $content );
		return $content;
  }
	
	
	/** 
	 * Removes unwanted classes and styles from p tags
	 *
	 * `<p class="western" style="margin-bottom: 5.95pt">Hello World!</p>`
	 * becomes
	 * `<p>Hello World!</p>`
	 * 
	 * `
	 * <p style="padding-left: 30px">Hello World</p>
	 * `
	 * 
	 * The result of the find is an array of elements
	 * `
	 *	Array  (
	 *		[0] => simple_html_dom_node Object
   *     (
   *         [nodetype] => 1
   *         [tag] => p
   *         [attr] => Array
   *             (
   *                 [class] => western
                    [style] => margin-bottom: 5.95pt
                )

            [children] => Array
	 *	`
	 * 
	 * @param string $content
	 * @return string content with class and style attributes removed from each p tag
   */
	function fixup_p_styles( $content ) {
	
		//echo $content;
		//echo PHP_EOL;
		$html = str_get_html( $content );
		
		foreach ( $html->find('p[class]') as $e) {
			$e->removeAttribute( "class" );
		}
		
		foreach ( $html->find('p[style]') as $e) {
			$e->removeAttribute( "style" );
		}
		
		$str = $html->save();
		//echo $str;
		//echo PHP_EOL;
		return $str;
	}
	
	/**
	 * Fixes up unnecessary span tags in content
	 * 
	 * Question: Do we still want stuff highlighted red?
	 * 
	 * `
	 * <span style="color: #ff0000">Hello World!</span>
	 * <span style="font-family: Arial;">Hello World!</span>
	 * `
	 
	 * ### | property: value      	    | Action
	 * --- | ---------------------      | -------
	 * 625 | color: #f00 ( red )        | Replace by <em> ?
	 *  32 | font-size: x-large         |
	 *  32 | font-size: xx-large        |
	 *  10 | font-family: Arial         | 
	 *   8 | color: #333 ( grey )       | 
	 *   8 | text-decoration: underline | 
	 *   2 | color: #f60 ( orange )     | 
	 *   2 | font-size: large           |
	 *   2 | font-size: medium          |
	 *   1 | color: #0f0 ( green )      | 
	 */
	function fixup_span_styles( $content ) {
	
		
		foreach ($html->find('span[style]') as $e) {
			echo $e->getAttribute( "style" );
			echo PHP_EOL;
		}
		return $content;
	}
	
	/**
	 * Extracts the contents of all h2's
	 * 
	 * @param string $content
	 * @return string concatentated innertext for all h2's
	 */
	function extract_h2( $content ) {
		$html = str_get_html( $content );
		$post_excerpt = null;
		foreach ( $html->find( 'h2' ) as $e ) {
			$post_excerpt .= $e->innertext;
			echo $post_excerpt;
			echo PHP_EOL;
		}
		
		return $post_excerpt;
	}
		
	
	/**
	 * Reports the styles being used inline in p tags
	 *
	 
	 * ### | property: value      	    | Action ?
	 * --- | ---------------------      | -------
	 * 154 | padding-left: 30px;        | remove attr
	 *  11 | text-align: left;          | remove attr
	 */ 
	function report_p_styles( $content ) {
		$html = str_get_html( $content );
		
		foreach ($html->find('p[style]') as $e) {
			echo $e->getAttribute( "style" );
			echo PHP_EOL;
		}
		return $content;
	}
	
	
	/** 
	 * Reports and missing images
	 * 
	 * Regenerate thumbnails reports missing images but only tells you the title and the 
	 * ID and not the file it couldn't find.
	 * We'll try to be a bit more helpful.
	 * get_post_thumbnail_id is a wrapper to get post meta _thumbnail_id
	 */ 
	function report_missing_image( $ID, $post ) {
		echo $ID .  $post->title . PHP_EOL;
		$attachment_id = get_post_thumbnail_id( $post );
		if ( $attachment_id ) {
			echo $attachment_id;
			echo PHP_EOL; 
			$attachment = get_post( $attachment_id );
			//print_r( $attachment );
			$file = get_attached_file( $attachment_id, true );
			echo $file;
			echo PHP_EOL;
			
			$exists = file_exists( $file );
			if ( !$exists ) {
			gob();
			
			}	else {
				$size = filesize( $file );
				echo $size; 
			}
			
		}
	}
	
	
	/**
	 * Here we're trying to find the "See below" that comes after the h2
	 * The "text" node contains the innertext of each tag.
	 * How do we identify a parentless one?
	 */
	
	
	function report_p_see_below( $ID, $post ) {
		echo $ID .  $post->title . PHP_EOL;
		$lines = explode( "\r\n", $post->post_content );
		$olines = array();
		$found = false;
		foreach ( $lines as $line ) {
      $sb = stripos( $line, "See below " );
			if ( false === $sb ) {
				$olines[] = $line;
			}	else {
				$found = true;
			}
		}
		
		if ( $found ) {
			$post->post_content = implode( "\r\n", $olines );
		} else {
      echo $post->post_content;
		}
		
		//	gob();
	}
		
	function find_text( $ID, $post ) {
		
		echo $post->post_content;
		echo PHP_EOL;
		$html = str_get_html( $post->post_content );
		//print_r( $html );
		//gob();
		foreach ( $html->find( 'text' ) as $e ) {
			echo $e->innertext;
			echo PHP_EOL;
			continue;
		}
			gob();
	
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
