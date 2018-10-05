<?php 


/** 
 * @copyright (C) Copyright Bobbing Wide 2018
 * @package sgorders
 * 
 *
 * Syntax: 
 * `
 * cd [path]/wp-content/plugins/sgfixup
 * oikwp sgorders.php input.xml order-no > export-order-no.xml
 * `
 * 
 * e.g. 
  oikwp sgorders.php C:\backups-qw\sgmotorsport.biz\sgmotorsport.wordpress.2018-10-03-orders.xml 833
 */
 
 

if ( PHP_SAPI !== "cli" ) { 
	die();
}




add_action( "run_sgorders.php", "sgfixup_orders" );


function sgfixup_orders() {
	ini_set('memory_limit','2048M');

	

	$xml_file = oik_batch_query_value_from_argv( 1, null );
	$order_no = oik_batch_query_value_from_argv( 2, null );

	$contents = sgfixup_get_contents( $xml_file );
	
	$orders = sgfixup_list_orders( $contents );
	$order_info = sgfixup_find_order( $orders, $order_no );
	if ( $order_info ) {
		$order = sgfixup_extract_order( $contents, $order_info );
		sgfixup_write_order_xml( $order_no, $order, $contents );
	}
	
 	


}

/**
 * Using PHP simplexml_load_string didn't work 
 * since it didn't like the colons in the fields
 * nor funny characters in CDATA
 */

function sgfixup_load_xml( $xml_file ) {
	$xml = null;
	
	if ( !file_exists( $xml_file ) ) {
		echo "File $xml_file does not exist" . PHP_EOL;
  }
	$contents = file_get_contents( $xml_file );
	$contents = str_replace( "wp:", "wp-", $contents );
	$contents = str_replace( chr( 26 ), "", $contents );
	$xml = simplexml_load_string( $contents );
	if ( $xml === FALSE ) {
		echo "Bugger";
	}
	return $xml;
}


/**
 * 
 
		<wp:postmeta>
			<wp:meta_key><![CDATA[_order_number]]></wp:meta_key>
			<wp:meta_value><![CDATA[23]]></wp:meta_value>
		</wp:postmeta>


 */
 
function sgfixup_get_contents( $xml_file ) {
	if ( !file_exists( $xml_file ) ) {
		echo "File $xml_file does not exist" . PHP_EOL;
  }

	$contents = file( $xml_file );
	return $contents;
}

/**
 * Lists the orders with _order_number in the file
 * 
 * Doesn't work for orders that don't have _order_number before </item>
 *
 */

function sgfixup_list_orders( $contents ) {
	

	$items = array( );
	$start = null;
	$end = null;
	$order = null;
	
	echo "XML orders" . PHP_EOL;
	foreach ( $contents as $index => $line ) {
		$line = trim( $line );
		//echo $line;
			
		if ( false !== strpos( $line, "<item>" ) ) {
			$start = $index;
		}
		if ( false !== strpos( $line, "</item>" ) ) {
			$end = $index;
		}
		if ( false !== strpos( $line, "<wp:meta_key><![CDATA[_order_number]]></wp:meta_key>" ) ) {
			
			$order = $index+1;
			$order_number = sgfixup_get_order_number( $contents[ $order ] );
		}
		
		if ( $start && $end && $order && $order_number ) {
			$items[ $order_number ] = array( $order, $start, $end );
			$start = null;
			$end = null;
			$order = null;
			$order_number = null;
		}
	
	}
	
	//ksort( $items );
	//print_r( $items );
	return $items;


}

/**

			<wp:meta_value><![CDATA[23]]></wp:meta_value>
 */																						 
function sgfixup_get_order_number( $line ) {
	$order_number = str_replace( "<wp:meta_value><![CDATA[", "", $line );
	$order_number = str_replace( "]]></wp:meta_value>", "", $order_number );
	$order_number = trim( $order_number );
	return $order_number;
}


function sgfixup_find_order( $orders, $order_no ) {
	$order_found = isset( $orders[ $order_no ] );
	if ( $order_found ) {
		echo $order_no;
		//print_r( $orders[ $order_no ] );
	} else {
		return $order_found;
	}
	return( $orders[ $order_no ] );
}

/**
 *
 * @param array $contents - the original XML file
 * @param array $order_info contains ( $order, $start, $end )
 */
function sgfixup_extract_order( $contents, $order_info ) {
	$order = array();
	
	//print_r( $order_
	for ( $index = $order_info[1];  $index <= $order_info[ 2 ]; $index++ ) {
		//echo $index;
		$order[] = $contents[ $index ];
	}
	$order_body = implode( "", $order );
	return $order_body;
}

function sgfixup_write_order_xml( $order_no, $order, $contents ) {

	$filename = sgfixup_create_order_file( $order_no );
	$prefix = sgfixup_get_prefix( $contents );
	sgfixup_write_file( $filename, $prefix );
	sgfixup_write_file( $filename, $order );
	$suffix = sgfixup_get_suffix();
	sgfixup_write_file( $filename, $suffix );

} 
	

function sgfixup_write_file( $filename, $text ) {
	$handle = fopen( $filename, "a" );
	fwrite( $handle, $text );
	fclose( $handle );
}

function sgfixup_create_order_file( $order_no ) {
	$filename = "order-$order_no.xml" ;
	if ( file_exists( $filename ) ) {
		echo "File $filename already exists. Deleting" . PHP_EOL;
		unlink( $filename );
	}
  echo "Creating file: $filename" . PHP_EOL;
	return $filename;
}

function sgfixup_get_prefix( $contents ) {
	$prefix = array();
	foreach ( $contents as $line ) {
		if ( false === strpos( $line, "<item>" ) ) {
			$prefix[] = $line;
		} else {
			break;
		}
	} 
	$prefix = implode( "", $prefix );
	//echo $prefix;
	return $prefix;

}

function sgfixup_get_suffix() {
	$suffix = "</channel>\n";
	$suffix .= "</rss>\n";
	return $suffix;
}
