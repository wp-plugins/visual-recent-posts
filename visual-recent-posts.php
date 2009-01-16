<?php
/*
Plugin name: Visual Recent Posts
Version: 1.2.3
Plugin URI: http://oktober5.com/visual-recent-posts-plugin/
Description: Visually represents your most recent posts by extracting the first image from each post and displaying it along with the post title and excerpt.
Author: Ryan Scott
Author URI: http://oktober5.com/
*/

/*  Copyright 2008  Ryan Scott  (email : oktoberfive [a t] gmail [d o t] com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

include_once("imageExtractor.php");

if (!class_exists("VisualRecentPostsPlugin")) {
	class VisualRecentPostsPlugin {
		var $adminOptionsName = "VisualRecentPostsPluginAdminOptions";
		function VisualRecentPostsPlugin() {
			
		}
		function init() {
			$this->getAdminOptions();
		}

		function getDefaultOptions() {
			$vrp_AdminOptions = array('number_of_posts' => '5', 'offset' => '0', 'custom_heading_code' => 'THE LATEST',
				'include_post_title' => 'true', 'image_height' => '72', 'image_width' => '280', 'location_hook' => '',
				'include_post_excerpt' => 'false', 'float_left' => 'false', 'top_margin' => '0', 'title_caption_font_size' => '12',
				'excerpt_font_size' => '12', 'header_text_font_size' => '12', 'box_height' => '', 'right_margin' => '0',
				'bottom_margin' => '0', 'left_margin' => '0', 'only_front_page' => 'false', 'layout_option' => 'horizontal',
				'box_width' => '', 'include_featured' => 'false', 'featured_is_most_recent' => 'true', 'featured_layout' => 'horizontal',
				'featured_box_width' => '', 'featured_post_id' => '', 'featured_image_width' => '400', 'featured_image_height' => '200',
				'featured_include_excerpt' => 'true', 'featured_include_title' => 'true', 'featured_title_font_size' => '18',
				'featured_excerpt_font_size' => '14', 'featured_tag' => '', 'only_posts_with_images' => 'false', 'category' => '',
				'featured_tag_font_size' => '12', 'box_background_color' => 'efefef', 'display_popups' => 'false',
				
				'image_box_ptop' => '', 'image_box_pright' => '', 'image_box_pbottom' => '', 'image_box_pleft' => '',
				'post_title_ptop' => '', 'post_title_pright' => '', 'post_title_pbottom' => '', 'post_title_pleft' => '',
				'excerpt_ptop' => '', 'excerpt_pright' => '', 'excerpt_pbottom' => '', 'excerpt_pleft' => '',
				
				'fimage_box_ptop' => '', 'fimage_box_pright' => '', 'fimage_box_pbottom' => '', 'fimage_box_pleft' => '',
				'fpost_title_ptop' => '', 'fpost_title_pright' => '', 'fpost_title_pbottom' => '', 'fpost_title_pleft' => '',
				'fexcerpt_ptop' => '', 'fexcerpt_pright' => '', 'fexcerpt_pbottom' => '', 'fexcerpt_pleft' => '',
				'post_title_after_image' => 'false'
				);
			return $vrp_AdminOptions;
		}
		
		//Returns an array of admin options
		function getAdminOptions() {
			$vrp_AdminOptions = $this->getDefaultOptions();
			$vrpOptions = get_option($this->adminOptionsName);
			if (!empty($vrpOptions)) {
				foreach ($vrpOptions as $key => $option)
					$vrp_AdminOptions[$key] = $option;
			}				
			update_option($this->adminOptionsName, $vrp_AdminOptions);
		
			return $vrp_AdminOptions;
		}
		
		function setAdminOptions($newAdminOptions) {
			update_option($this->adminOptionsName, $newAdminOptions);
		}

		function getVrpLocationHook() {
			$vrpOptions = $this->getAdminOptions();			
			return $vrpOptions['location_hook'];
		}
		
		function uploadVRPCSS() {
			echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/visual-recent-posts/vrp_styles.css" />' . "\n";
			return "cheese";
		}

		function addVRP($category = '', $include_featured_post = '', $number_of_posts = '') {
			$vrpOptions = $this->getAdminOptions();
			
			if($category != '') $vrpOptions['category'] = $category;
			if($include_featured_post != '') $vrpOptions['include_featured'] = $include_featured_post;
			if($number_of_posts != '') $vrpOptions['number_of_posts'] = $number_of_posts;

			if($vrpOptions['only_front_page'] == 'true' && is_front_page()) {
				if($vrpOptions['layout_option'] == 'vertical') $this->drawLayout_vertical($vrpOptions);
				if($vrpOptions['layout_option'] == 'horizontal') $this->drawLayout_horizontal($vrpOptions);
			} elseif($vrpOptions['only_front_page'] == 'false') {
				if($vrpOptions['layout_option'] == 'vertical') $this->drawLayout_vertical($vrpOptions);
				if($vrpOptions['layout_option'] == 'horizontal') $this->drawLayout_horizontal($vrpOptions);
			}
		}
		
		function drawFeatured_vertical($vrpOptions, $featuredPost) {
			echo '<div id="vrp_image_box" style="width:';
				if(intval($vrpOptions['featured_box_width']) > (intval($vrpOptions['featured_image_width'])+17)) {
					echo intval($vrpOptions['featured_box_width']).'px;';
				} else {
					echo (intval($vrpOptions['featured_image_width']) + 17).'px;';
				}
				if($vrpOptions['float_left'] == 'true') {
					echo ' float:left;';
				}
				if($vrpOptions['box_background_color'] != '') {
					echo 'background:#'.$vrpOptions['box_background_color'].';';
				}
				
				if($vrpOptions['fimage_box_ptop'] != '') {echo 'padding-top:'.$vrpOptions['fimage_box_ptop'].'px;';}
				if($vrpOptions['fimage_box_pright'] != '') {echo 'padding-right:'.$vrpOptions['fimage_box_pright'].'px;';}
				if($vrpOptions['fimage_box_pbottom'] != '') {echo 'padding-bottom:'.$vrpOptions['fimage_box_pbottom'].'px;';}
				if($vrpOptions['fimage_box_pleft'] != '') {echo 'padding-left:'.$vrpOptions['fimage_box_pleft'].'px;';}
				
				echo '">';
				
				if($vrpOptions['featured_include_title'] == 'true') {
					echo '<div id="vrp_title_caption" style="';
					
					if($vrpOptions['fpost_title_ptop'] != '') echo 'padding-top:'.$vrpOptions['fpost_title_ptop'].'px;';
					if($vrpOptions['fpost_title_pright'] != '') echo 'padding-right:'.$vrpOptions['fpost_title_pright'].'px;';
					if($vrpOptions['fpost_title_pbottom'] != '') echo 'padding-bottom:'.$vrpOptions['fpost_title_pbottom'].'px;';
					if($vrpOptions['fpost_title_pleft'] != '') echo 'padding-left:'.$vrpOptions['fpost_title_pleft'].'px;';
					
					echo '"><h3><a style=" font-size:'.$vrpOptions['featured_title_font_size'].'px;" href="';
					echo get_permalink($featuredPost->ID);//the_permalink();
					echo '">';
					echo $featuredPost->post_title;
					echo '</a></h3></div>';
				} else {
					echo '<div style="padding-top:10px;"></div>';
				}
				
				echo '<a href="';
				echo get_permalink($featuredPost->ID);//the_permalink();
				echo '" class="info">';
				echo '<span>';
				the_excerpt();
				echo '</span>';
			 	echo image_extractor($resize = true, $resize_type = 1, $width = intval($vrpOptions['featured_image_width']), $height = intval($vrpOptions['featured_image_height']), $class = 'vrp_img', $id = '', $prefix='', $suffix='', $post=$featuredPost);
				echo '</a>';
				
				if($vrpOptions['featured_include_excerpt'] == 'true') {
					echo '<div id="vrp_excerpt" style="padding-top:0px; font-size:'.$vrpOptions['featured_excerpt_font_size'].'px;';
					
					if($vrpOptions['fexcerpt_ptop'] != '') echo 'padding-top:'.$vrpOptions['fexcerpt_ptop'].'px;';
					if($vrpOptions['fexcerpt_pright'] != '') echo 'padding-right:'.$vrpOptions['fexcerpt_pright'].'px;';
					if($vrpOptions['fexcerpt_pbottom'] != '') echo 'padding-bottom:'.$vrpOptions['fexcerpt_pbottom'].'px;';
					if($vrpOptions['fexcerpt_pleft'] != '') echo 'padding-left:'.$vrpOptions['fexcerpt_pleft'].'px;';
					
					echo '">';
					echo '<a style="text-decoration:none;" href="';
					echo get_permalink($featuredPost->ID);//the_permalink();
					echo '"><p>';
					echo $featuredPost->post_excerpt;
					echo '</p></a>';
					echo '</div>';
				} else {
					echo '<div style="padding-top:7px;"></div>';
				}
				
				echo '</div>';
		}

		function drawFeatured_horizontal($vrpOptions, $featuredPost) {
			echo '<div id="vrp_image_box" style="width:';		
			if(intval($vrpOptions['featured_box_width']) > (intval($vrpOptions['featured_image_width'])+17)) {
				echo intval($vrpOptions['featured_box_width']).'px;';
			} else {
				echo (intval($vrpOptions['featured_image_width']) + 17).'px;';
			}
			if($vrpOptions['float_left'] == 'true') {
				echo ' float:left;';
			}
			echo 'height:';
			if($vrpOptions['featured_tag'] != '') $temp_height = intval($vrpOptions['featured_image_height']) + 37;
			else $temp_height = intval($vrpOptions['featured_image_height']) + 17;
			echo $temp_height.'px;';
			if($vrpOptions['box_background_color'] != '') {
				echo 'background:#'.$vrpOptions['box_background_color'].';';
			}
			
			if($vrpOptions['fimage_box_ptop'] != '') {echo 'padding-top:'.$vrpOptions['fimage_box_ptop'].'px;';}
			if($vrpOptions['fimage_box_pright'] != '') {echo 'padding-right:'.$vrpOptions['fimage_box_pright'].'px;';}
			if($vrpOptions['fimage_box_pbottom'] != '') {echo 'padding-bottom:'.$vrpOptions['fimage_box_pbottom'].'px;';}
			if($vrpOptions['fimage_box_pleft'] != '') {echo 'padding-left:'.$vrpOptions['fimage_box_pleft'].'px;';}
			
			echo '">';
			
			
			if($vrpOptions['featured_tag'] != '') {
				echo '<div id="featured_tag" style="padding:5px 5px 0px 10px;';
				if($vrpOptions['featured_tag_font_size'] != '') {
					echo 'font-size:'.$vrpOptions['featured_tag_font_size'].'px;';
				}
				echo '">';
				echo '<p>'.$vrpOptions['featured_tag'].'</p>';
				echo '</div>';
			}
			
			echo '<a href="';
			echo get_permalink($featuredPost->ID);//the_permalink();
			echo '" class="info">';
			echo '<span>';
			the_excerpt();
			echo '</span>';
		 	echo image_extractor($resize = true, $resize_type = 1, $width = intval($vrpOptions['featured_image_width']), $height = intval($vrpOptions['featured_image_height']), $class = 'vrp_img_mag', $id = '', $prefix='', $suffix='', $post=$featuredPost);
			echo '</a>';
			
			if($vrpOptions['featured_include_title'] == 'true') {
				echo '<div id="vrp_title_caption_mag" style="';
				
				if($vrpOptions['fpost_title_ptop'] != '') echo 'padding-top:'.$vrpOptions['fpost_title_ptop'].'px;';
				if($vrpOptions['fpost_title_pright'] != '') echo 'padding-right:'.$vrpOptions['fpost_title_pright'].'px;';
				if($vrpOptions['fpost_title_pbottom'] != '') echo 'padding-bottom:'.$vrpOptions['fpost_title_pbottom'].'px;';
				if($vrpOptions['fpost_title_pleft'] != '') echo 'padding-left:'.$vrpOptions['fpost_title_pleft'].'px;';
				
				echo '"><h3><a style="';
				echo ' font-size:'.$vrpOptions['featured_title_font_size'].'px;" href="';
				echo get_permalink($featuredPost->ID);
				echo '">';
				echo $featuredPost->post_title;//the_title();
				echo '</a></h3></div>';
			} else {
				echo '<div style="padding-top:10px;"></div>';
			}
			
			if($vrpOptions['featured_include_excerpt'] == 'true') {
				echo '<div id="vrp_excerpt_mag" style="padding-top:0px; font-size:'.$vrpOptions['featured_excerpt_font_size'].'px;';
				
				if($vrpOptions['fexcerpt_ptop'] != '') echo 'padding-top:'.$vrpOptions['fexcerpt_ptop'].'px;';
				if($vrpOptions['fexcerpt_pright'] != '') echo 'padding-right:'.$vrpOptions['fexcerpt_pright'].'px;';
				if($vrpOptions['fexcerpt_pbottom'] != '') echo 'padding-bottom:'.$vrpOptions['fexcerpt_pbottom'].'px;';
				if($vrpOptions['fexcerpt_pleft'] != '') echo 'padding-left:'.$vrpOptions['fexcerpt_pleft'].'px;';
				
				echo '">';
				echo '<a style="text-decoration:none;" href="';
				echo get_permalink($featuredPost->ID);
				echo '">';
				echo $featuredPost->post_excerpt;//the_excerpt();
				echo '</a>';
				echo '</div>';
			} else {
				echo '<div></div>';
			}
			
			echo '</div>';
		}

		function drawLayout_vertical($vrpOptions) {
			if(!$vrpOptions['custom_heading_code'] == '') {
				$vrp_h3_first_letter = $vrpOptions['custom_heading_code'][0];
				$vrp_h3_the_rest = substr($vrpOptions['custom_heading_code'], 1);;
			}
			echo '<div id="vrp_box" class="custom" style="';
			if($vrpOptions['top_margin'] != '') echo 'margin-top:'.$vrpOptions['top_margin'].'px;';
			if($vrpOptions['right_margin'] != '') echo 'margin-right:'.$vrpOptions['right_margin'].'px;';
			if($vrpOptions['bottom_margin'] != '') echo 'margin-bottom:'.$vrpOptions['bottom_margin'].'px;';
			if($vrpOptions['left_margin'] != '') echo 'margin-left:'.$vrpOptions['left_margin'].'px;';
			echo '">';
			if(!$vrpOptions['custom_heading_code'] == '') {
				echo '<h3 id="vrp_h3" style="font-size:'.$vrpOptions['header_text_font_size'].'px;"><span class="h3_drop_cap">'.$vrp_h3_first_letter.'</span>'.$vrp_h3_the_rest.'</h3>';
			}
			
			$vrp_counter = 0;
			global $post;
			//$myposts = get_posts('numberposts='.$vrpOptions['number_of_posts'].'&offset='.$vrpOptions['offset'].'&category='.$vrpOptions['category']);
			$myposts = $this->vrp_get_posts($vrpOptions);
			if($vrpOptions['number_of_posts'] == '0') {
				$vrp_no_posts = 'true';
			} else $vrp_no_posts = 'false';
			foreach($myposts as $post) :
				$vrp_counter = $vrp_counter + 1;
				$go_on = 'false';
				if($vrp_counter > 1 || $vrpOptions['include_featured'] == 'false') $go_on = 'true';
				if($vrpOptions['include_featured'] == 'true' && $vrp_counter == 1) {
					if($vrpOptions['featured_is_most_recent'] == 'false' && $vrpOptions['featured_post_id'] != '') {
						$featuredPost = get_post($vrpOptions['featured_post_id']);					
						if($vrpOptions['featured_layout'] == 'vertical') $this->drawFeatured_vertical($vrpOptions, $featuredPost);
						if($vrpOptions['featured_layout'] == 'horizontal') $this->drawFeatured_horizontal($vrpOptions, $featuredPost);
						$go_on = 'true';
					} else {
						if($vrpOptions['featured_layout'] == 'vertical') $this->drawFeatured_vertical($vrpOptions, $post);
						if($vrpOptions['featured_layout'] == 'horizontal') $this->drawFeatured_horizontal($vrpOptions, $post);
					}
				}
				
				if($go_on == 'true' && $vrp_no_posts == 'false') {
					echo '<div id="vrp_image_box" style="width:';
					if(intval($vrpOptions['box_width']) > (intval($vrpOptions['image_width']))) {
						echo intval($vrpOptions['box_width']).'px;';
					} else {
						echo (intval($vrpOptions['image_width'])).'px;';
					}
					if(!$vrpOptions['box_height'] == '') {
						echo 'height:'.intval($vrpOptions['box_height']).'px;';
					}
					if($vrpOptions['float_left'] == 'true') {
						echo ' float:left;';
					}
					if($vrpOptions['box_background_color'] != '') {
						echo 'background:#'.$vrpOptions['box_background_color'].';';
					}
					if($vrpOptions['image_box_ptop'] != '') {echo 'padding-top:'.$vrpOptions['image_box_ptop'].'px;';}
					if($vrpOptions['image_box_pright'] != '') {echo 'padding-right:'.$vrpOptions['image_box_pright'].'px;';}
					if($vrpOptions['image_box_pbottom'] != '') {echo 'padding-bottom:'.$vrpOptions['image_box_pbottom'].'px;';}
					if($vrpOptions['image_box_pleft'] != '') {echo 'padding-left:'.$vrpOptions['image_box_pleft'].'px;';}
					
					echo '">';
				
				  if($vrpOptions['post_title_after_image'] == 'false') {
					  if($vrpOptions['include_post_title'] == 'true') {
						  echo '<div id="vrp_title_caption" style="';
					
						  if($vrpOptions['post_title_ptop'] != '') echo 'padding-top:'.$vrpOptions['post_title_ptop'].'px;';
						  if($vrpOptions['post_title_pright'] != '') echo 'padding-right:'.$vrpOptions['post_title_pright'].'px;';
						  if($vrpOptions['post_title_pbottom'] != '') echo 'padding-bottom:'.$vrpOptions['post_title_pbottom'].'px;';
						  if($vrpOptions['post_title_pleft'] != '') echo 'padding-left:'.$vrpOptions['post_title_pleft'].'px;';
						
						  echo '"><h3><a style=" font-size:'.$vrpOptions['title_caption_font_size'].'px;';
						  echo '" href="';
						  echo the_permalink();
						  echo '">';
						  echo the_title();
						  echo '</a></h3></div>';
					  } else {
						  //echo '<div></div>';
					  }
				
					  echo '<a href="';
					  echo the_permalink();
					  echo '" class="info">';
					  if($vrpOptions['display_popups'] == 'true') {
						  echo '<span><p class="pop_title">';
						  echo $post->post_title;
						  echo '</p>';
						  echo $post->post_excerpt;
						  echo '</span>';
					  }
				   	echo image_extractor($resize = true, $resize_type = 1, $width = intval($vrpOptions['image_width']), $height = intval($vrpOptions['image_height']), $class = 'vrp_img', $id = '', $prefix='', $suffix='', $post=$post);
					  echo '</a>';
					} else {
					  echo '<a href="';
					  echo the_permalink();
					  echo '" class="info">';
					  if($vrpOptions['display_popups'] == 'true') {
						  echo '<span><p class="pop_title">';
						  echo $post->post_title;
						  echo '</p>';
						  echo $post->post_excerpt;
						  echo '</span>';
					  }
				   	echo image_extractor($resize = true, $resize_type = 1, $width = intval($vrpOptions['image_width']), $height = intval($vrpOptions['image_height']), $class = 'vrp_img', $id = '', $prefix='', $suffix='', $post=$post);
					  echo '</a>';
					
					  if($vrpOptions['include_post_title'] == 'true') {
						  echo '<div id="vrp_title_caption" style="';
					
						  if($vrpOptions['post_title_ptop'] != '') echo 'padding-top:'.$vrpOptions['post_title_ptop'].'px;';
						  if($vrpOptions['post_title_pright'] != '') echo 'padding-right:'.$vrpOptions['post_title_pright'].'px;';
						  if($vrpOptions['post_title_pbottom'] != '') echo 'padding-bottom:'.$vrpOptions['post_title_pbottom'].'px;';
						  if($vrpOptions['post_title_pleft'] != '') echo 'padding-left:'.$vrpOptions['post_title_pleft'].'px;';
						
						  echo '"><h3><a style=" font-size:'.$vrpOptions['title_caption_font_size'].'px;';
						  echo '" href="';
						  echo the_permalink();
						  echo '">';
						  echo the_title();
						  echo '</a></h3></div>';
					  } else {
						  //echo '<div></div>';
					  }
					}
				
					if($vrpOptions['include_post_excerpt'] == 'true') {
						echo '<div id="vrp_excerpt" style="font-size:'.$vrpOptions['excerpt_font_size'].'px;';
						
						if($vrpOptions['excerpt_ptop'] != '') echo 'padding-top:'.$vrpOptions['excerpt_ptop'].'px;';
						if($vrpOptions['excerpt_pright'] != '') echo 'padding-right:'.$vrpOptions['excerpt_pright'].'px;';
						if($vrpOptions['excerpt_pbottom'] != '') echo 'padding-bottom:'.$vrpOptions['excerpt_pbottom'].'px;';
						if($vrpOptions['excerpt_pleft'] != '') echo 'padding-left:'.$vrpOptions['excerpt_pleft'].'px;';
						
						echo '">';
						echo '<a href="';
						echo the_permalink();
						echo '">';
						the_excerpt();
						echo '</a>';
						echo '</div>';
					} else {
						//echo '<div style="padding-top:7px;"></div>';
					}
				
					echo '</div>';
				}
 			endforeach;
			echo '</div>';
		}

		function drawLayout_horizontal($vrpOptions) {
			if(!$vrpOptions['custom_heading_code'] == '') {
				$vrp_h3_first_letter = $vrpOptions['custom_heading_code'][0];
				$vrp_h3_the_rest = substr($vrpOptions['custom_heading_code'], 1);;
			}
			echo '<div id="vrp_box" class="custom" style="';
			if($vrpOptions['top_margin'] != '') echo 'margin-top:'.$vrpOptions['top_margin'].'px;';
			if($vrpOptions['right_margin'] != '') echo 'margin-right:'.$vrpOptions['right_margin'].'px;';
			if($vrpOptions['bottom_margin'] != '') echo 'margin-bottom:'.$vrpOptions['bottom_margin'].'px;';
			if($vrpOptions['left_margin'] != '') echo 'margin-left:'.$vrpOptions['left_margin'].'px;';
			echo '">';
			
			if(!$vrpOptions['custom_heading_code'] == '') {
				echo '<h3 id="vrp_h3" style="font-size:'.$vrpOptions['header_text_font_size'].'px;"><span class="h3_drop_cap">'.$vrp_h3_first_letter.'</span>'.$vrp_h3_the_rest.'</h3>';
			}
			

			$vrp_counter = 0;
			global $post;
			//$myposts = get_posts('numberposts='.$vrpOptions['number_of_posts'].'&offset='.$vrpOptions['offset'].'&category='.$vrpOptions['category']);
			$myposts = $this->vrp_get_posts($vrpOptions);
			if($vrpOptions['number_of_posts'] == '0') {
				$vrp_no_posts = 'true';
			} else $vrp_no_posts = 'false';
			foreach($myposts as $post) :
				$vrp_counter = $vrp_counter + 1;
				$go_on = 'false';
				if($vrp_counter > 1 || $vrpOptions['include_featured'] == 'false') $go_on = 'true';
				if($vrpOptions['include_featured'] == 'true' && $vrp_counter == 1) {
					if($vrpOptions['featured_is_most_recent'] == 'false' && $vrpOptions['featured_post_id'] != '') {
						$featuredPost = get_post($vrpOptions['featured_post_id']);					
						if($vrpOptions['featured_layout'] == 'vertical') $this->drawFeatured_vertical($vrpOptions, $featuredPost);
						if($vrpOptions['featured_layout'] == 'horizontal') $this->drawFeatured_horizontal($vrpOptions, $featuredPost);
						$go_on = 'true';
					} else {
						if($vrpOptions['featured_layout'] == 'vertical') $this->drawFeatured_vertical($vrpOptions, $post);
						if($vrpOptions['featured_layout'] == 'horizontal') $this->drawFeatured_horizontal($vrpOptions, $post);
					}
				}
				
				if($go_on == 'true' && $vrp_no_posts == 'false') {
					echo '<div id="vrp_image_box" style="width:';
					if(intval($vrpOptions['box_width']) > (intval($vrpOptions['image_width']))) {
						echo intval($vrpOptions['box_width']).'px;';
					} else {
						echo (intval($vrpOptions['image_width'])).'px;';
					}
					if($vrpOptions['float_left'] == 'true') {
						echo ' float:left;';
					}
					echo 'height:';
					if(intval($vrpOptions['box_height']) > (intval($vrpOptions['image_height']))) {
						echo intval($vrpOptions['box_height']).'px;';
					} else {
						echo (intval($vrpOptions['image_height'])).'px;';
					}
					if($vrpOptions['box_background_color'] != '') {
						echo 'background:#'.$vrpOptions['box_background_color'].';';
					}
					if($vrpOptions['image_box_ptop'] != '') echo 'padding-top:'.$vrpOptions['image_box_ptop'].'px;';
					if($vrpOptions['image_box_pright'] != '') echo 'padding-right:'.$vrpOptions['image_box_pright'].'px;';
					if($vrpOptions['image_box_pbottom'] != '') echo 'padding-bottom:'.$vrpOptions['image_box_pbottom'].'px;';
					if($vrpOptions['image_box_pleft'] != '') echo 'padding-left:'.$vrpOptions['image_box_pleft'].'px;';
					echo '">';
				
					echo '<a href="';
					echo the_permalink();
					echo '" class="info">';
					if($vrpOptions['display_popups'] == 'true') {
						echo '<span><p class="pop_title">';
						echo $post->post_title;
						echo '</p>';
						echo $post->post_excerpt;
						echo '</span>';
					}
				 	echo image_extractor($resize = true, $resize_type = 1, $width = intval($vrpOptions['image_width']), $height = intval($vrpOptions['image_height']), $class = 'vrp_img_mag', $id = '', $prefix='', $suffix='', $post=$post);
					echo '</a>';
				
					if($vrpOptions['include_post_title'] == 'true') {
						echo '<div id="vrp_title_caption_mag" style="';
						
						if($vrpOptions['post_title_ptop'] != '') echo 'padding-top:'.$vrpOptions['post_title_ptop'].'px;';
						if($vrpOptions['post_title_pright'] != '') echo 'padding-right:'.$vrpOptions['post_title_pright'].'px;';
						if($vrpOptions['post_title_pbottom'] != '') echo 'padding-bottom:'.$vrpOptions['post_title_pbottom'].'px;';
						if($vrpOptions['post_title_pleft'] != '') echo 'padding-left:'.$vrpOptions['post_title_pleft'].'px;';
						
						echo'"><h3><a style="';
						echo ' font-size:'.$vrpOptions['title_caption_font_size'].'px;" href="';
						echo the_permalink();
						echo '">';
						echo the_title();
						echo '</a></h3></div>';
					} else {
						//echo '<div style="padding-top:10px;"></div>';
					}
				
					if($vrpOptions['include_post_excerpt'] == 'true') {
						echo '<div id="vrp_excerpt_mag" style="font-size:'.$vrpOptions['excerpt_font_size'].'px; ';
						
						if($vrpOptions['excerpt_ptop'] != '') echo 'padding-top:'.$vrpOptions['excerpt_ptop'].'px;';
						if($vrpOptions['excerpt_pright'] != '') echo 'padding-right:'.$vrpOptions['excerpt_pright'].'px;';
						if($vrpOptions['excerpt_pbottom'] != '') echo 'padding-bottom:'.$vrpOptions['excerpt_pbottom'].'px;';
						if($vrpOptions['excerpt_pleft'] != '') echo 'padding-left:'.$vrpOptions['excerpt_pleft'].'px;';
						
						echo '">';
						echo '<a style="text-decoration:none;" href="';
						echo the_permalink();
						echo '">';
						the_excerpt();
						echo '</a>';
						echo '</div>';
					} else {
						echo '<div></div>';
					}
				
					echo '</div>';
				}
 			endforeach;
			echo '</div>';
		}

		function vrp_get_posts($vrpOptions) {
			$temp_offset = $vrpOptions['offset'];
			$temp_num_posts = 0;
			$return_posts = array();
			while($temp_num_posts < $vrpOptions['number_of_posts']) {
				$temp_post = get_posts('numberposts=1&offset='.$temp_offset.'&category='.$vrpOptions['category']);
				if($temp_post[0] == '') return $return_posts;
				if($vrpOptions['only_posts_with_images'] == 'true') {
					if($this->vrp_has_image($temp_post[0])) {
						array_push($return_posts, $temp_post[0]);
						$temp_offset = $temp_offset + 1;
						$temp_num_posts = $temp_num_posts + 1;
					} else {
						$temp_offset = $temp_offset + 1;
					}
				} else {
					array_push($return_posts, $temp_post[0]);
					$temp_offset = $temp_offset + 1;
					$temp_num_posts = $temp_num_posts + 1;
				}
			}
			
			return $return_posts;
		}

		function vrp_has_image($vrp_post) { 
			$text = $vrp_post->post_content;

		  	// Create the parser
		  	$parser = new htmlparser_class;
		  
		  	// Set the html code
		  	$ret=$parser->InsertHTML($text);
		  	if ($ret===false) return;
		  	$parser->Parse();
		  	$result=$parser->GetElements($htmlCode);
		  	$attribArr=$parser->getTagResource("img");    
		  	
		  	if ($attribArr==false) return false;
		  	return true;
		}

		//Prints out the admin page
		function printAdminPage() {
					$vrpOptions = $this->getAdminOptions();
										
					if (isset($_POST['update_VisualRecentPostsPluginSettings'])) { 
						if (isset($_POST['vrp_number_of_posts'])) {
							$vrpOptions['number_of_posts'] = $_POST['vrp_number_of_posts'];
						}
						if (isset($_POST['vrp_offset'])) {
							$vrpOptions['offset'] = $_POST['vrp_offset'];
						}
						if (isset($_POST['vrp_custom_heading_code'])) {
							$vrpOptions['custom_heading_code'] = $_POST['vrp_custom_heading_code'];
						}
						if (isset($_POST['vrp_include_post_title'])) {
							$vrpOptions['include_post_title'] = $_POST['vrp_include_post_title'];
						}
						if (isset($_POST['vrp_image_height'])) {
							$vrpOptions['image_height'] = $_POST['vrp_image_height'];
						}
						if (isset($_POST['vrp_image_width'])) {
							$vrpOptions['image_width'] = $_POST['vrp_image_width'];
						}
						if (isset($_POST['vrp_location_hook'])) {
							$vrpOptions['location_hook'] = $_POST['vrp_location_hook'];
						}
						if (isset($_POST['vrp_include_post_excerpt'])) {
							$vrpOptions['include_post_excerpt'] = $_POST['vrp_include_post_excerpt'];
						}
						if (isset($_POST['vrp_float_left'])) {
							$vrpOptions['float_left'] = $_POST['vrp_float_left'];
						}
						if (isset($_POST['vrp_top_margin'])) {
							$vrpOptions['top_margin'] = $_POST['vrp_top_margin'];
						}
						if (isset($_POST['vrp_title_caption_font_size'])) {
							$vrpOptions['title_caption_font_size'] = $_POST['vrp_title_caption_font_size'];
						}
						if (isset($_POST['vrp_excerpt_font_size'])) {
							$vrpOptions['excerpt_font_size'] = $_POST['vrp_excerpt_font_size'];
						}
						if (isset($_POST['vrp_header_text_font_size'])) {
							$vrpOptions['header_text_font_size'] = $_POST['vrp_header_text_font_size'];
						}
						if (isset($_POST['vrp_box_height'])) {
							$vrpOptions['box_height'] = $_POST['vrp_box_height'];
						}
						if (isset($_POST['vrp_right_margin'])) {
							$vrpOptions['right_margin'] = $_POST['vrp_right_margin'];
						}
						if (isset($_POST['vrp_bottom_margin'])) {
							$vrpOptions['bottom_margin'] = $_POST['vrp_bottom_margin'];
						}
						if (isset($_POST['vrp_left_margin'])) {
							$vrpOptions['left_margin'] = $_POST['vrp_left_margin'];
						}
						if (isset($_POST['vrp_only_front_page'])) {
							$vrpOptions['only_front_page'] = $_POST['vrp_only_front_page'];
						}
						if (isset($_POST['vrp_layout_option'])) {
							$vrpOptions['layout_option'] = $_POST['vrp_layout_option'];
						}
						if (isset($_POST['vrp_box_width'])) {
							$vrpOptions['box_width'] = $_POST['vrp_box_width'];
						}
						if (isset($_POST['vrp_include_featured'])) {
							$vrpOptions['include_featured'] = $_POST['vrp_include_featured'];
						}
						if (isset($_POST['vrp_featured_is_most_recent'])) {
							$vrpOptions['featured_is_most_recent'] = $_POST['vrp_featured_is_most_recent'];
						}
						if (isset($_POST['vrp_featured_layout'])) {
							$vrpOptions['featured_layout'] = $_POST['vrp_featured_layout'];
						}
						if (isset($_POST['vrp_featured_box_width'])) {
							$vrpOptions['featured_box_width'] = $_POST['vrp_featured_box_width'];
						}
						if (isset($_POST['vrp_featured_post_id'])) {
							$vrpOptions['featured_post_id'] = $_POST['vrp_featured_post_id'];
						}
						if (isset($_POST['vrp_featured_image_width'])) {
							$vrpOptions['featured_image_width'] = $_POST['vrp_featured_image_width'];
						}
						if (isset($_POST['vrp_featured_image_height'])) {
							$vrpOptions['featured_image_height'] = $_POST['vrp_featured_image_height'];
						}
						if (isset($_POST['vrp_featured_include_title'])) {
							$vrpOptions['featured_include_title'] = $_POST['vrp_featured_include_title'];
						}
						if (isset($_POST['vrp_featured_include_excerpt'])) {
							$vrpOptions['featured_include_excerpt'] = $_POST['vrp_featured_include_excerpt'];
						}
						if (isset($_POST['vrp_featured_title_font_size'])) {
							$vrpOptions['featured_title_font_size'] = $_POST['vrp_featured_title_font_size'];
						}
						if (isset($_POST['vrp_featured_excerpt_font_size'])) {
							$vrpOptions['featured_excerpt_font_size'] = $_POST['vrp_featured_excerpt_font_size'];
						}
						if (isset($_POST['vrp_featured_tag'])) {
							$vrpOptions['featured_tag'] = $_POST['vrp_featured_tag'];
						}
						if (isset($_POST['vrp_only_posts_with_images'])) {
							$vrpOptions['only_posts_with_images'] = $_POST['vrp_only_posts_with_images'];
						}
						if (isset($_POST['vrp_category'])) {
							$vrpOptions['category'] = $_POST['vrp_category'];
						}
						if (isset($_POST['vrp_featured_tag_font_size'])) {
							$vrpOptions['featured_tag_font_size'] = $_POST['vrp_featured_tag_font_size'];
						}
						if (isset($_POST['vrp_box_background_color'])) {
							$vrpOptions['box_background_color'] = $_POST['vrp_box_background_color'];
						}
						if (isset($_POST['vrp_display_popups'])) {
							$vrpOptions['display_popups'] = $_POST['vrp_display_popups'];
						}
						
						/*VRP Image Box padding stuff*/
						if (isset($_POST['vrp_image_box_ptop'])) {
							$vrpOptions['image_box_ptop'] = $_POST['vrp_image_box_ptop'];
						}
						if (isset($_POST['vrp_image_box_pright'])) {
							$vrpOptions['image_box_pright'] = $_POST['vrp_image_box_pright'];
						}
						if (isset($_POST['vrp_image_box_pbottom'])) {
							$vrpOptions['image_box_pbottom'] = $_POST['vrp_image_box_pbottom'];
						}
						if (isset($_POST['vrp_image_box_pleft'])) {
							$vrpOptions['image_box_pleft'] = $_POST['vrp_image_box_pleft'];
						}
						
						if (isset($_POST['vrp_post_title_ptop'])) {
							$vrpOptions['post_title_ptop'] = $_POST['vrp_post_title_ptop'];
						}
						if (isset($_POST['vrp_post_title_pright'])) {
							$vrpOptions['post_title_pright'] = $_POST['vrp_post_title_pright'];
						}
						if (isset($_POST['vrp_post_title_pbottom'])) {
							$vrpOptions['post_title_pbottom'] = $_POST['vrp_post_title_pbottom'];
						}
						if (isset($_POST['vrp_post_title_pleft'])) {
							$vrpOptions['post_title_pleft'] = $_POST['vrp_post_title_pleft'];
						}
						
						if (isset($_POST['vrp_excerpt_ptop'])) {
							$vrpOptions['excerpt_ptop'] = $_POST['vrp_excerpt_ptop'];
						}
						if (isset($_POST['vrp_excerpt_pright'])) {
							$vrpOptions['excerpt_pright'] = $_POST['vrp_excerpt_pright'];
						}
						if (isset($_POST['vrp_excerpt_pbottom'])) {
							$vrpOptions['excerpt_pbottom'] = $_POST['vrp_excerpt_pbottom'];
						}
						if (isset($_POST['vrp_excerpt_pleft'])) {
							$vrpOptions['excerpt_pleft'] = $_POST['vrp_excerpt_pleft'];
						}
					
						/*Featured box padding stuff*/
						if (isset($_POST['vrp_fimage_box_ptop'])) {
							$vrpOptions['fimage_box_ptop'] = $_POST['vrp_fimage_box_ptop'];
						}
						if (isset($_POST['vrp_fimage_box_pright'])) {
							$vrpOptions['fimage_box_pright'] = $_POST['vrp_fimage_box_pright'];
						}
						if (isset($_POST['vrp_fimage_box_pbottom'])) {
							$vrpOptions['fimage_box_pbottom'] = $_POST['vrp_fimage_box_pbottom'];
						}
						if (isset($_POST['vrp_fimage_box_pleft'])) {
							$vrpOptions['fimage_box_pleft'] = $_POST['vrp_fimage_box_pleft'];
						}
						
						if (isset($_POST['vrp_fpost_title_ptop'])) {
							$vrpOptions['fpost_title_ptop'] = $_POST['vrp_fpost_title_ptop'];
						}
						if (isset($_POST['vrp_fpost_title_pright'])) {
							$vrpOptions['fpost_title_pright'] = $_POST['vrp_fpost_title_pright'];
						}
						if (isset($_POST['vrp_fpost_title_pbottom'])) {
							$vrpOptions['fpost_title_pbottom'] = $_POST['vrp_fpost_title_pbottom'];
						}
						if (isset($_POST['vrp_fpost_title_pleft'])) {
							$vrpOptions['fpost_title_pleft'] = $_POST['vrp_fpost_title_pleft'];
						}
						
						if (isset($_POST['vrp_fexcerpt_ptop'])) {
							$vrpOptions['fexcerpt_ptop'] = $_POST['vrp_fexcerpt_ptop'];
						}
						if (isset($_POST['vrp_fexcerpt_pright'])) {
							$vrpOptions['fexcerpt_pright'] = $_POST['vrp_fexcerpt_pright'];
						}
						if (isset($_POST['vrp_fexcerpt_pbottom'])) {
							$vrpOptions['fexcerpt_pbottom'] = $_POST['vrp_fexcerpt_pbottom'];
						}
						if (isset($_POST['vrp_fexcerpt_pleft'])) {
							$vrpOptions['fexcerpt_pleft'] = $_POST['vrp_fexcerpt_pleft'];
						}
						
						if (isset($_POST['vrp_post_title_after_image'])) {
							$vrpOptions['post_title_after_image'] = $_POST['vrp_post_title_after_image'];
						}
					
						update_option($this->adminOptionsName, $vrpOptions);
						
						?>
<div class="updated"><p><strong><?php _e("Settings Updated.", "VisualRecentPostsPlugin");?></strong></p></div>
					<?php
					} ?>
<div class=wrap>
<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<h2>Visual Recent Posts Plugin</h2>
<p>Note: Any style settings you set in these options will override any settings set in your own stylesheet (CSS) file. To stop that from happening, leave blank those styles that you want to define in your CSS.</p>
<h3>Custom Header Text</h3>
<p>If you want to include a heading to your Visual Recent Posts section, do so here.</p>
<input type="text" name="vrp_custom_heading_code" value="<?php _e($vrpOptions['custom_heading_code'], 'VisualRecentPostsPlugin') ?>"></input>

<h2>Thesis Theme Location Hook</h2>
<p>Thesis includes several "hooks" in order to put custom code in specified locations. The default location hook here is 'thesis_hook_before_sidebars'.</p>
<input type="text" style="width:300px;" name="vrp_location_hook" value="<?php _e($vrpOptions['location_hook'], 'VisualRecentPostsPlugin') ?>"></input>
<p>Possible options include (you may want to copy/paste for accuracy):</p>
<ul>
<li>thesis_hook_before_sidebars</li>
<li>thesis_hook_after_sidebars</li>
<li>thesis_hook_before_content</li>
<li>thesis_hook_after_multimedia_box</li>
</ul>
<p>For a complete reference of Thesis hooks, see <a href="http://diythemes.com/thesis/rtfm/hooks/">diythemes.com/thesis/rtfm/hooks/</a></p>
<h3>If you don't have hooks, simply insert <code>insertVisualRecentPosts();</code> wherever you want things to show up (but make sure it's within a php block).</h3>
<p>The function insertVisualRecentPosts() can also be passed the following optional parameters:</p>
<p><strong>category</strong> - Want to define a specific category to display in this VRP box? Enter the Category ID here. To find the category ID, go to your Wordpress admin page, edit categories, click on the desired category, and in the URL box you should see something like 'cat_ID=9'. That '9' is your category ID. Also, you can insert multiple ideas seperated by a comma (ex. '9,5,3'). Also, if you want to exclude a category, enter the negative of it (ex. '-9' would display all categories but 9). Note: This option won't effect the featured post.</p>
<p><strong>include_featured_post</strong> - Maybe you don't want to include the featured post in this instance? Specify either 'true' or 'false' (and it's case sensitive).</p>
<p><em>Example: </em><code>insertVisualRecentPosts($category = '9', $include_featured_post = 'false')</code><em> would display only posts from category with ID 9 and the featured post would not be displayed.</em></p>
<p>Note: If you specify a category in the function insertVisualRecentPosts(), it will override the category selection below.</p>

<h3>Category</h3>
<p>To find the category ID, go to your Wordpress admin page, edit categories, click on the desired category, and in the URL box you should see something like `cat_ID=9`. That `9` is your category ID. Also, you can insert multiple ideas seperated by a comma (ex. `9,5,3`). Also, if you want to exclude a category, enter the negative of it (ex. `-9` would display all categories but 9). Leave blank to display all categories.</p>
<input type="text" name="vrp_category" value="<?php _e($vrpOptions['category'], 'VisualRecentPostsPlugin') ?>"></input>

<h3>Display Only On Front Page</h3>
<label for="only_front_page_yes"><input type="radio" id="only_front_page_yes" name="vrp_only_front_page" value="true" <?php if ($vrpOptions['only_front_page'] == "true") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?> /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label for="only_front_page_false"><input type="radio" id="only_front_page_false" name="vrp_only_front_page" value="false" <?php if ($vrpOptions['only_front_page'] == "false") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?>/> No</label>

<h2>VRP Box</h2>
<h3>Number Of Posts To Display</h3>
<input type="text" name="vrp_number_of_posts" value="<?php _e($vrpOptions['number_of_posts'], 'VisualRecentPostsPlugin') ?>"></input>

<h3>Offset</h3>
<p>This is how many recent posts to skip until they start showing up in the Visual Recent Posts. If, for example, you don't want to include the most recent post, set the offset to 1.</p>
<input type="text" name="vrp_offset" value="<?php _e($vrpOptions['offset'], 'VisualRecentPostsPlugin') ?>"></input>

<h3>Include Post Title</h3>
<label for="include_post_title_yes"><input type="radio" id="include_post_title_yes" name="vrp_include_post_title" value="true" <?php if ($vrpOptions['include_post_title'] == "true") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?> /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label for="include_post_title_no"><input type="radio" id="include_post_title_no" name="vrp_include_post_title" value="false" <?php if ($vrpOptions['include_post_title'] == "false") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?>/> No</label>

<h3>Position Post Title After Image</h3>
<p>Mark yes if you want the title showing up after the image; mark no to have it show up before. Note: This only applies to the vertical layout option.</p>
<label for="post_title_after_image_yes"><input type="radio" id="post_title_after_image_yes" name="vrp_post_title_after_image" value="true" <?php if ($vrpOptions['post_title_after_image'] == "true") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?> /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label for="post_title_after_image_no"><input type="radio" id="post_title_after_image_no" name="vrp_post_title_after_image" value="false" <?php if ($vrpOptions['post_title_after_image'] == "false") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?>/> No</label>

<h3>Include Post Excerpt</h3>
<p>This only works if you use the actual excerpt field when you write your post, otherwise you'll get the excerpt of whatever page you're viewing. Stupid, I know, but the geeks at Wordpress assure me that if I understood The Loop things like this could be avoided. The Loop must be some drug they are on. I wonder where they get it....</p>
<label for="include_post_excerpt_yes"><input type="radio" id="include_post_excerpt_yes" name="vrp_include_post_excerpt" value="true" <?php if ($vrpOptions['include_post_excerpt'] == "true") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?> /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label for="include_post_excerpt_no"><input type="radio" id="include_post_excerpt_no" name="vrp_include_post_excerpt" value="false" <?php if ($vrpOptions['include_post_excerpt'] == "false") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?>/> No</label>

<h3>Only Include Posts With Images</h3>
<p>This does not apply to the featured post.</p>
<label for="only_posts_with_images_yes"><input type="radio" id="only_posts_with_images_yes" name="vrp_only_posts_with_images" value="true" <?php if ($vrpOptions['only_posts_with_images'] == "true") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?> /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label for="only_posts_with_images_no"><input type="radio" id="only_posts_with_images_no" name="vrp_only_posts_with_images" value="false" <?php if ($vrpOptions['only_posts_with_images'] == "false") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?>/> No</label>

<h3>Include CSS Popups</h3>
<p>When the mouse hovers over an image, a box pops up that includes the post title and excerpt that belong to that image. Note: This does not apply to the featured post. Sorry. Also, as noted in the excerpt option above, you must explicitly define an excerpt in the post edit screen in the 'Excerpt' section--this excerpt does not apply to using the more tag.</p>
<label for="display_popups_yes"><input type="radio" id="display_popups_yes" name="vrp_display_popups" value="true" <?php if ($vrpOptions['display_popups'] == "true") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?> /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label for="display_popups_no"><input type="radio" id="display_popups_no" name="vrp_display_popups" value="false" <?php if ($vrpOptions['display_popups'] == "false") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?>/> No</label>

<h3>Layout Option</h3>
<p>Currently, there are two layout options: horizontal and vertical. Vertical puts the title above the image and the excerpt below it. The horizontal layout puts the title and excerpt to the right of the image. (Hint: You'll probably want to set the 'Box Width' below if you choose to do a horizontal layout.)</p>
<label for="layout_option_vertical"><input type="radio" id="layout_option_vertical" name="vrp_layout_option" value="vertical" <?php if ($vrpOptions['layout_option'] == "vertical") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?> /> Vertical</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label for="layout_option_horizontal"><input type="radio" id="layout_option_horizontal" name="vrp_layout_option" value="horizontal" <?php if ($vrpOptions['layout_option'] == "horizontal") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?>/> Horizontal</label>

<h3>Float Left</h3>
<p>Floating left refers to the behavior of each post box. If it floats left, then the boxes will tend to line up in a row horizontally until they run out of space and then wrap back down and start another row. If this is not selected, then the boxes are kept in a one-column layout. This option can be used to "fake" a multi-column layout.</p>
<label for="float_left_yes"><input type="radio" id="float_left_yes" name="vrp_float_left" value="true" <?php if ($vrpOptions['float_left'] == "true") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?> /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label for="float_left_false"><input type="radio" id="float_left_false" name="vrp_float_left" value="false" <?php if ($vrpOptions['float_left'] == "false") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?>/> No</label>

<h2>Image Dimensions</h2>
<p>Important note: Your image height will determine the height of the post box. This has certain implications, the foremost being that if you have a long excerpt and a short image, the text is going to run out of the box and it's going to look ugly.</p>
<h3>Thumbnail Image Width</h3>
<p>You image will be scaled and cut to this width.</p>
<input type="text" name="vrp_image_width" value="<?php _e($vrpOptions['image_width'], 'VisualRecentPostsPlugin') ?>"></input>

<h3>Thumbnail Image Height</h3>
<p>You image will be scaled to this height.</p>
<input type="text" name="vrp_image_height" value="<?php _e($vrpOptions['image_height'], 'VisualRecentPostsPlugin') ?>"></input>

<h2>Styles</h2>

<h3>Top Margin</h3>
<p>If you need to define a special margin width before the post boxes appear, this is where you do it. I found this helpful when putting my Visual Recent Posts in the sidebar where they were crammed up against the top menu line.</p>
<input type="text" name="vrp_top_margin" value="<?php _e($vrpOptions['top_margin'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Right Margin</h3>
<input type="text" name="vrp_right_margin" value="<?php _e($vrpOptions['right_margin'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Bottom Margin</h3>
<input type="text" name="vrp_bottom_margin" value="<?php _e($vrpOptions['bottom_margin'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Left Margin</h3>
<input type="text" name="vrp_left_margin" value="<?php _e($vrpOptions['left_margin'], 'VisualRecentPostsPlugin') ?>"></input>

<h3>Header Text Font Size</h3>
<p>This is the size of the font for the header of your Visual Recent Posts section, if you have one.</p>
<input type="text" name="vrp_header_text_font_size" value="<?php _e($vrpOptions['header_text_font_size'], 'VisualRecentPostsPlugin') ?>"></input>

<h3>Title Caption Font Size</h3>
<p>This is the size of the font for the title of the post, if displayed.</p>
<input type="text" name="vrp_title_caption_font_size" value="<?php _e($vrpOptions['title_caption_font_size'], 'VisualRecentPostsPlugin') ?>"></input>

<h3>Excerpt Font Size</h3>
<p>This is the size of the font for the post excerpt, if displayed.</p>
<input type="text" name="vrp_excerpt_font_size" value="<?php _e($vrpOptions['excerpt_font_size'], 'VisualRecentPostsPlugin') ?>"></input>

<h3>Box Height</h3>
<p>This controls the height of each individual VRP box. It's recommended to leave this blank, which will set the height equal to the height of the image thumbnail.</p>
<input type="text" name="vrp_box_height" value="<?php _e($vrpOptions['box_height'], 'VisualRecentPostsPlugin') ?>"></input>

<h3>Box Width</h3>
<p>This controls the width of each individual VRP box. If left blank, the width of your post box height will be set to the width of your image. If you're using a horizontal layout, you'll want to set this value for sure.</p>
<input type="text" name="vrp_box_width" value="<?php _e($vrpOptions['box_width'], 'VisualRecentPostsPlugin') ?>"></input>

<h3>Box Background Color</h3>
<p>This controls the background color of your VRP image box thingy... Anyway, it's a hex value, like ffffff for white or 000000 for black.</p>
<input type="text" name="vrp_box_background_color" value="<?php _e($vrpOptions['box_background_color'], 'VisualRecentPostsPlugin') ?>"></input>

<h2>VRP Image Box Padding</h2>
<p>This is the box where the image thumbnail, title, and excerpt reside.</p>
<h3>Padding Top</h3>
<input type="text" name="vrp_image_box_ptop" value="<?php _e($vrpOptions['image_box_ptop'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Padding Right</h3>
<input type="text" name="vrp_image_box_pright" value="<?php _e($vrpOptions['image_box_pright'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Padding Bottom</h3>
<input type="text" name="vrp_image_box_pbottom" value="<?php _e($vrpOptions['image_box_pbottom'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Padding Left</h3>
<input type="text" name="vrp_image_box_pleft" value="<?php _e($vrpOptions['image_box_pleft'], 'VisualRecentPostsPlugin') ?>"></input>

<h2>Post Title Padding</h2>
<p>Specify padding around the post title.</p>
<h3>Padding Top</h3>
<input type="text" name="vrp_post_title_ptop" value="<?php _e($vrpOptions['post_title_ptop'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Padding Right</h3>
<input type="text" name="vrp_post_title_pright" value="<?php _e($vrpOptions['post_title_pright'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Padding Bottom</h3>
<input type="text" name="vrp_post_title_pbottom" value="<?php _e($vrpOptions['post_title_pbottom'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Padding Left</h3>
<input type="text" name="vrp_post_title_pleft" value="<?php _e($vrpOptions['post_title_pleft'], 'VisualRecentPostsPlugin') ?>"></input>

<h2>Post Excerpt Padding</h2>
<p>Specify padding around the post excerpt.</p>
<h3>Padding Top</h3>
<input type="text" name="vrp_excerpt_ptop" value="<?php _e($vrpOptions['excerpt_ptop'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Padding Right</h3>
<input type="text" name="vrp_excerpt_pright" value="<?php _e($vrpOptions['excerpt_pright'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Padding Bottom</h3>
<input type="text" name="vrp_excerpt_pbottom" value="<?php _e($vrpOptions['excerpt_pbottom'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Padding Left</h3>
<input type="text" name="vrp_excerpt_pleft" value="<?php _e($vrpOptions['excerpt_pleft'], 'VisualRecentPostsPlugin') ?>"></input>

<h2>Featured Post</h2>
<h3>Include Featured Post</h3>
<label for="include_featured_yes"><input type="radio" id="include_featured_yes" name="vrp_include_featured" value="true" <?php if ($vrpOptions['include_featured'] == "true") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?> /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label for="include_featured_no"><input type="radio" id="include_featured_no" name="vrp_include_featured" value="false" <?php if ($vrpOptions['include_featured'] == "false") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?>/> No</label>

<h3>Featured Post Is Most Recent Post</h3>
<p>If you've specified an offset, "Most Recent Post" here will take into account the offset.</p>
<label for="featured_is_most_recent_yes"><input type="radio" id="featured_is_most_recent_yes" name="vrp_featured_is_most_recent" value="true" <?php if ($vrpOptions['featured_is_most_recent'] == "true") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?> /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label for="featured_is_most_recent_no"><input type="radio" id="featured_is_most_recent_no" name="vrp_featured_is_most_recent" value="false" <?php if ($vrpOptions['featured_is_most_recent'] == "false") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?>/> No</label>

<h3>Featured Post Layout Option</h3>
<p>Currently, there are two layout options: horizontal and vertical. Vertical puts the title above the image and the excerpt below it. The horizontal layout puts the title and excerpt to the right of the image.</p>
<label for="featured_layout_vertical"><input type="radio" id="featured_layout_vertical" name="vrp_featured_layout" value="vertical" <?php if ($vrpOptions['featured_layout'] == "vertical") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?> /> Vertical</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label for="featured_layout_horizontal"><input type="radio" id="featured_layout_horizontal" name="vrp_featured_layout" value="horizontal" <?php if ($vrpOptions['featured_layout'] == "horizontal") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?>/> Horizontal</label>

<h3>Featured Box Width</h3>
<p>This controls the width of the featured VRP box. If left blank, the width of your post box height will be set to the width of your image. If you're using a horizontal layout, you'll want to set this value for sure.</p>
<input type="text" name="vrp_featured_box_width" value="<?php _e($vrpOptions['featured_box_width'], 'VisualRecentPostsPlugin') ?>"></input>

<h3>Featured Post ID</h3>
<p>If you didn't specify that you wanted the featured post to be the most recent, then you can enter a post id of a particular post that you would like to be the featured. You can find this number by editing this post in the admin panel and looking in the URL field. You should see something like <em>post=238</em>. That '238' is your post id.</p>
<input type="text" name="vrp_featured_post_id" value="<?php _e($vrpOptions['featured_post_id'], 'VisualRecentPostsPlugin') ?>"></input>

<h3>Featured Image Width</h3>
<input type="text" name="vrp_featured_image_width" value="<?php _e($vrpOptions['featured_image_width'], 'VisualRecentPostsPlugin') ?>"></input>

<h3>Featured Image Height</h3>
<input type="text" name="vrp_featured_image_height" value="<?php _e($vrpOptions['featured_image_height'], 'VisualRecentPostsPlugin') ?>"></input>

<h3>Include Featured Post Title</h3>
<label for="featured_include_title_yes"><input type="radio" id="featured_include_title_yes" name="vrp_featured_include_title" value="true" <?php if ($vrpOptions['featured_include_title'] == "true") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?> /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label for="featured_include_title_no"><input type="radio" id="featured_include_title_no" name="vrp_featured_include_title" value="false" <?php if ($vrpOptions['featured_include_title'] == "false") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?>/> No</label>

<h3>Include Featured Post Excerpt</h3>
<label for="featured_include_excerpt_yes"><input type="radio" id="featured_include_excerpt_yes" name="vrp_featured_include_excerpt" value="true" <?php if ($vrpOptions['featured_include_excerpt'] == "true") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?> /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label for="featured_include_excerpt_no"><input type="radio" id="featured_include_excerpt_no" name="vrp_featured_include_excerpt" value="false" <?php if ($vrpOptions['featured_include_excerpt'] == "false") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?>/> No</label>

<h3>Featured Title Font Size</h3>
<input type="text" name="vrp_featured_title_font_size" value="<?php _e($vrpOptions['featured_title_font_size'], 'VisualRecentPostsPlugin') ?>"></input>

<h3>Featured Excerpt Font Size</h3>
<input type="text" name="vrp_featured_excerpt_font_size" value="<?php _e($vrpOptions['featured_excerpt_font_size'], 'VisualRecentPostsPlugin') ?>"></input>

<h3>Featured Tag</h3>
<p>This is a little bit of text displayed at the top left corner of the featured post box. It could be something original like 'Featured Post!'</p>
<input type="text" name="vrp_featured_tag" value="<?php _e($vrpOptions['featured_tag'], 'VisualRecentPostsPlugin') ?>"></input>

<h3>Featured Tag Font Size</h3>
<input type="text" name="vrp_featured_tag_font_size" value="<?php _e($vrpOptions['featured_tag_font_size'], 'VisualRecentPostsPlugin') ?>"></input>

<!-- FEATURED BOX PADDING STUFF -->

<h2>Featured Image Box Padding</h2>
<p>This is the box where the featured image thumbnail, title, and excerpt reside.</p>
<h3>Padding Top</h3>
<input type="text" name="vrp_fimage_box_ptop" value="<?php _e($vrpOptions['fimage_box_ptop'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Padding Right</h3>
<input type="text" name="vrp_fimage_box_pright" value="<?php _e($vrpOptions['fimage_box_pright'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Padding Bottom</h3>
<input type="text" name="vrp_fimage_box_pbottom" value="<?php _e($vrpOptions['fimage_box_pbottom'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Padding Left</h3>
<input type="text" name="vrp_fimage_box_pleft" value="<?php _e($vrpOptions['fimage_box_pleft'], 'VisualRecentPostsPlugin') ?>"></input>

<h2>Featured Post Title Padding</h2>
<p>Specify padding around the featured post title.</p>
<h3>Padding Top</h3>
<input type="text" name="vrp_fpost_title_ptop" value="<?php _e($vrpOptions['fpost_title_ptop'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Padding Right</h3>
<input type="text" name="vrp_fpost_title_pright" value="<?php _e($vrpOptions['fpost_title_pright'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Padding Bottom</h3>
<input type="text" name="vrp_fpost_title_pbottom" value="<?php _e($vrpOptions['fpost_title_pbottom'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Padding Left</h3>
<input type="text" name="vrp_fpost_title_pleft" value="<?php _e($vrpOptions['fpost_title_pleft'], 'VisualRecentPostsPlugin') ?>"></input>

<h2>Featured Post Excerpt Padding</h2>
<p>Specify padding around the featured post excerpt.</p>
<h3>Padding Top</h3>
<input type="text" name="vrp_fexcerpt_ptop" value="<?php _e($vrpOptions['fexcerpt_ptop'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Padding Right</h3>
<input type="text" name="vrp_fexcerpt_pright" value="<?php _e($vrpOptions['fexcerpt_pright'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Padding Bottom</h3>
<input type="text" name="vrp_fexcerpt_pbottom" value="<?php _e($vrpOptions['fexcerpt_pbottom'], 'VisualRecentPostsPlugin') ?>"></input>
<h3>Padding Left</h3>
<input type="text" name="vrp_fexcerpt_pleft" value="<?php _e($vrpOptions['fexcerpt_pleft'], 'VisualRecentPostsPlugin') ?>"></input>



<div class="submit">
<input type="submit" name="update_VisualRecentPostsPluginSettings" value="<?php _e('Update Settings', 'VisualRecentPostsPlugin') ?>" /></div>
</form>
<p>For support, go to the <a href="http://oktober5.com/visual-recent-posts-plugin/">plugin homepage at Oktober5</a>.
 </div>
					<?php
				}//End function printAdminPage()
	
	}

} //End Class VisualRecentPostsPlugin

if (class_exists("VisualRecentPostsPlugin")) {
	$dl_pluginVRP = new VisualRecentPostsPlugin();
}

//Initialize the admin panel
if (!function_exists("VisualRecentPostsPlugin_ap")) {
	function VisualRecentPostsPlugin_ap() {
		global $dl_pluginVRP;
		if (!isset($dl_pluginVRP)) {
			return;
		}
		if (function_exists('add_options_page')) {
			add_options_page('Visual Recent Posts', 'Visual Recent Posts', 9, basename(__FILE__), array(&$dl_pluginVRP, 'printAdminPage'));
		}
	}	
}

//Actions and Filters	
if (isset($dl_pluginVRP)) {
	//Actions
	add_action('admin_menu', 'VisualRecentPostsPlugin_ap');
	add_action('visual-recent-posts/visual-recent-posts.php',  array(&$dl_pluginVRP, 'init'));
	add_action('wp_head', array(&$dl_pluginVRP, 'uploadVRPCSS'));
	
	if (!$dl_pluginVRP->getVrpLocationHook() == '') {
		add_action($dl_pluginVRP->getVrpLocationHook(), array(&$dl_pluginVRP,'addVRP'), 1);
	}
}

function insertVisualRecentPosts($category = '', $include_featured_post = '', $number_of_posts = '') {
	$vrp_class = new VisualRecentPostsPlugin();
	$vrp_class->addVRP($category, $include_featured_post, $number_of_posts);
}

?>
