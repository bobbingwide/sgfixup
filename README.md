# sgfixup 
* Contributors: bobbingwide
* Donate link: https://www.oik-plugins.com/oik/oik-donate/
* Tags: content, correction
* Requires at least: 4.9
* Tested up to: 4.9.8
* Stable tag: 0.0.2
* License: GPLv3
* License URI: http://www.gnu.org/licenses/gpl-3.0.html

## Description 
Fixes up products and categories for SG Motorsport during migration from Canvas to Storefront.
See also https://github.com/bobbingwide/sg-motorsport.


## Installation 
1. Upload the contents of the sgfixup plugin to the `/wp-content/plugins/sgfixup' directory
2. Upload oik-batch
3. Run oik-batch sgfixup.php from the command line

## Frequently Asked Questions 


## Screenshots 
1. sgfixup in action

## Upgrade Notice 
# 0.0.2 
Now includes SG-mu.php - for safe keeping

# 0.0.1 
Edit as required to get the output needed.

# 0.0.0 
New plugin. Only available from github

## Changelog 
# 0.0.2 
* Added: Save first version of the SG-mu.php MU plugin used during local CLI development
* Added: Add logic to check existence of product_cat thumbnail images

# 0.0.1 
* Added: Search for p[style] and span[style] tags.
* Added: Start fixing up products and pages; remove [box] shortcodes
* Added: sgtest.php - temporarily until converted to PHPUnit test
* Changed: Implement as oik-batch action 'run_sgfixup.php'
* Commented: Draft notes about UTF-8 character problems
* Documented: Summarise in comments the possible fixups for span and p tags

# 0.0.0 
* Added: New plugin, copied / cobbled from another QAD oik-batch routine.

## Further reading 
Depends upon oik-batch.
