<?php	

/**
 * Poor man's testing of simple html dom stuff
 */



oik_require( "class-sgfixup.php", "sgfixup" );

ini_set('memory_limit','2048M');


$fixup = new sgfixup();

$content = '<p class="western" style="margin-bottom: 5.95pt">Hello World!</p>';
$content .= '<p><span style="color: #ff0000">Hello World!</span></p>';


$content = $fixup->fixup_p_styles( $content );	 
echo $content . PHP_EOL;
$content = $fixup->fixup_span_styles( $content );

echo $content . PHP_EOL;
