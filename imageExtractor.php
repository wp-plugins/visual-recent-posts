<?php
/*
Information for the Image Extractor plugin is here:
Plugin URI: http://www.dynamick.it/image-extractor-765.html
Description: This WordPress mod extract the image from the post.
Version: 1.1
Author: Michele Gobbi
Author URI: http://www.dynamick.it

	Copyright (c) 2004, 2007 Michele Gobbi (http://www.dynamick.it)
	Image Extractor is released under the GNU General Public
	License: http://www.gnu.org/licenses/gpl.txt

	This is a WordPress plugin (http://wordpress.org). WordPress is
	free software; you can redistribute it and/or modify it under the
	terms of the GNU General Public License as published by the Free
	Software Foundation; either version 2 of the License, or (at your
	option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
	General Public License for more details.

	For a copy of the GNU General Public License, write to:

	Free Software Foundation, Inc.
	59 Temple Place, Suite 330
	Boston, MA  02111-1307
	USA

	You can also view a copy of the HTML version of the GNU General
	Public License at http://www.gnu.org/copyleft/gpl.html

~Changelog:

07_04_2007
class.ImageToolbox.php has lost his tail. I've added a php closing tag "?>"...
Thanks to Sergio

06_04_2007
Updated some bugs in the interface methods

*/

// Configure this variable and set the cache folder for the resized images

$destinationDir="/wp-content/thumb-cache/";

// do not touch this lines...

include_once ("htmlParser.php");
include_once ("class.ImageToolbox.php");


function wp_image_extractor($args='') {
	parse_str($args);
	if(!isset($width))  $width = '';
	if(!isset($height)) $height = '';
	if(!isset($resize)) $resize = false;
	if(!isset($resize_type)) $resize_type = 1;
	if(!isset($class))  $class = '';
	if(!isset($id))     $id = '';
	if(!isset($prefix)) $prefix = '';
	if(!isset($suffix)) $suffix = '';

	return image_extractor($resize, $resize_type, $width, $height, $class, $id, $prefix, $suffix);
}

function image_extractor($resize=false, $resize_type=1, $width='', $height='', $class='', $id='', $prefix='', $suffix='', $post = '') {
	echo get_image_extractor($resize, $resize_type, $width, $height, $class, $id, $prefix, $suffix, $post);
}

function get_image_extractor($resize, $resize_type, $width, $height, $class, $id, $prefix, $suffix, $post) {
	global $destinationDir;

	if (!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_'.COOKIEHASH] != $post->post_password) { // and it doesn't match cookie
			if(is_feed()) { // if this runs in a feed
				$output = __('There is no excerpt because this is a protected post.');
			} else {
	            $output = get_the_password_form();
			}
		}
		return $output;
	}

	$text = $post->post_content;
	$the_post_title = $post->the_title;

  // Create the parser
  $parser = new htmlparser_class;
  
  // Set the html code
  $ret=$parser->InsertHTML($text);
  if ($ret===false) return;
  $parser->Parse();
  $result=$parser->GetElements($htmlCode);
  $attribArr=$parser->getTagResource("img");    
  if ($attribArr==false) return '<!--noimage-->';

  if ($resize===true) {
    $src=$parser->linkAnalyzer($attribArr[0]["src"]);
    if (!file_exists(getenv("DOCUMENT_ROOT").$src["path"])) return;
    $dest=$destinationDir.$width."x".$height."-".basename($src["path"]);
    $dest=preg_replace ('/\.(gif|jpg|png)/', '', $dest).".png";
    
    if (!file_exists(getenv("DOCUMENT_ROOT").$dest)) {
    
      list($w, $h, $t, $a) = getimagesize(getenv("DOCUMENT_ROOT").$src["path"]);

      $thumbnail=new Image_Toolbox(getenv("DOCUMENT_ROOT").$src["path"]);
      //$thumbnail->setResizeMethod('workaround');
      $thumbnail->setResizeMethod('resize');
      $thumbnail->newOutputSize($width,$height,$resize_type,false,'#FFFFFF');
      // $thumbnail->addImage('./img/logo.png');
      // $thumbnail->blend('right','bottom');
      $thumbnail->save(getenv("DOCUMENT_ROOT").$dest,"png24");
      
    }
    $attribArr[0]["src"]=$dest;
  }
  
  $ret="";
  
  if (is_array($attribArr[0]))
  foreach ($attribArr[0] as $k=>$v) {
    if ($k=="width" and $width!="") continue;
    if ($k=="height" and $height!="") continue;
    if ($k=="class" and $class!="") continue;
    if ($k=="id" and $id!="") continue;
    $ret.=" ".$k."=\"".$v."\"";
  }

  if ($width!="") $ret.=" width=\"".$width."\"";
  if ($height!="") $ret.=" height=\"".$height."\"";
  if ($class!="") $ret.=" class=\"".$class."\"";
  if ($id!="") $ret.=" id=\"".$id."\"";

  $output=$prefix.'<img '.$ret.'/>'.$suffix;
  
  
	$output = apply_filters($filter_type, $output);

	return $output;
}
?>
