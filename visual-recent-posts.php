<?php
/*
Plugin name: Visual Recent Posts
Version: 1.1.1
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
				'featured_excerpt_font_size' => '14', 'featured_tag' => '');
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

		function addVRP() {
			$vrpOptions = $this->getAdminOptions();

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
				/*if(!$vrpOptions['box_height'] == '') {
					echo 'height:'.intval($vrpOptions['box_height']).'px;';
				}*/
				if($vrpOptions['float_left'] == 'true') {
					echo ' float:left;';
				}
				echo '">';
				
				if($vrpOptions['featured_include_title'] == 'true') {
					echo '<div id="vrp_title_caption"><h2><a style=" font-size:'.$vrpOptions['featured_title_font_size'].'px;" href="';
					echo get_permalink($featuredPost->ID);//the_permalink();
					echo '">';
					echo $featuredPost->post_title;
					echo '</a></h2></div>';
				} else {
					echo '<div style="padding-top:10px;"></div>';
				}
				
				echo '<a href="';
				echo get_permalink($featuredPost->ID);//the_permalink();
				echo '">';
			 	echo image_extractor($resize = true, $resize_type = 1, $width = intval($vrpOptions['featured_image_width']), $height = intval($vrpOptions['featured_image_height']), $class = 'vrp_img', $id = '', $prefix='', $suffix='', $post=$featuredPost);
				echo '</a>';
				
				if($vrpOptions['featured_include_excerpt'] == 'true') {
					echo '<div id="vrp_excerpt" style="padding-top:0px; font-size:'.$vrpOptions['featured_excerpt_font_size'].'px;">';
					echo '<a style="text-decoration:none; color:#111111;" href="';
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
			/*if(intval($vrpOptions['box_height']) > (intval($vrpOptions['image_height'])+17).'px;') {
				echo intval($vrpOptions['box_height']).'px;';
			} else {
				echo (intval($vrpOptions['image_height']) + 17).'px;';
			}*/
			echo '">';
			
			
			if($vrpOptions['featured_tag'] != '') {
				echo '<div id="featured_tag" style="padding:5px 5px 0px 10px;">';
				echo '<p>'.$vrpOptions['featured_tag'].'</p>';
				echo '</div>';
			}
			
			echo '<a href="';
			echo get_permalink($featuredPost->ID);//the_permalink();
			echo '">';
			
		 	echo image_extractor($resize = true, $resize_type = 1, $width = intval($vrpOptions['featured_image_width']), $height = intval($vrpOptions['featured_image_height']), $class = 'vrp_img_mag', $id = '', $prefix='', $suffix='', $post=$featuredPost);
			echo '</a>';
			
			if($vrpOptions['featured_include_title'] == 'true') {
				echo '<div id="vrp_title_caption_mag"><h2><a style="';
				echo ' font-size:'.$vrpOptions['featured_title_font_size'].'px;" href="';
				echo get_permalink($featuredPost->ID);
				echo '">';
				echo $featuredPost->post_title;//the_title();
				echo '</a></h2></div>';
			} else {
				echo '<div style="padding-top:10px;"></div>';
			}
			
			if($vrpOptions['featured_include_excerpt'] == 'true') {
				echo '<div id="vrp_excerpt_mag" style="padding-top:0px; font-size:'.$vrpOptions['featured_excerpt_font_size'].'px;">';
				echo '<a style="text-decoration:none; color:#111111;" href="';
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
			echo '<div id="vrp_box" style="margin-top:'.$vrpOptions['top_margin'].'px;margin-right:'.$vrpOptions['right_margin'].'px;
			margin-bottom:'.$vrpOptions['bottom_margin'].'px;margin-left:'.$vrpOptions['left_margin'].'px;">';
			if(!$vrpOptions['custom_heading_code'] == '') {
				echo '<h3 id="vrp_h3" style="margin-top:0px; font-size:'.$vrpOptions['header_text_font_size'].'px;"><span class="h3_drop_cap">'.$vrp_h3_first_letter.'</span>'.$vrp_h3_the_rest.'</h3>';
			}
			
			$vrp_counter = 0;
			global $post;
			$myposts = get_posts('numberposts='.$vrpOptions['number_of_posts'].'&offset='.$vrpOptions['offset']);
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
					if(intval($vrpOptions['box_width']) > (intval($vrpOptions['image_width'])+17)) {
						echo intval($vrpOptions['box_width']).'px;';
					} else {
						echo (intval($vrpOptions['image_width']) + 17).'px;';
					}
					if(!$vrpOptions['box_height'] == '') {
						echo 'height:'.intval($vrpOptions['box_height']).'px;';
					}
					if($vrpOptions['float_left'] == 'true') {
						echo ' float:left;';
					}
					echo '">';
				
					if($vrpOptions['include_post_title'] == 'true') {
						echo '<div id="vrp_title_caption"><h3><a style=" font-size:'.$vrpOptions['title_caption_font_size'].'px;" href="';
						echo the_permalink();
						echo '">';
						echo the_title();
						echo '</a></h3></div>';
					} else {
						echo '<div style="padding-top:10px;"></div>';
					}
				
					echo '<a href="';
					echo the_permalink();
					echo '">';
				 	echo image_extractor($resize = true, $resize_type = 1, $width = intval($vrpOptions['image_width']), $height = intval($vrpOptions['image_height']), $class = 'vrp_img', $id = '', $prefix='', $suffix='', $post=$post);
					echo '</a>';
				
					if($vrpOptions['include_post_excerpt'] == 'true') {
						echo '<div id="vrp_excerpt" style="padding-top:0px; font-size:'.$vrpOptions['excerpt_font_size'].'px;">';
						echo '<a style="text-decoration:none; color:#111111;" href="';
						echo the_permalink();
						echo '">';
						the_excerpt();
						echo '</a>';
						echo '</div>';
					} else {
						echo '<div style="padding-top:7px;"></div>';
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
			echo '<div id="vrp_box" style="margin-top:'.$vrpOptions['top_margin'].'px;margin-right:'.$vrpOptions['right_margin'].'px;
			margin-bottom:'.$vrpOptions['bottom_margin'].'px;margin-left:'.$vrpOptions['left_margin'].'px;">';
			if(!$vrpOptions['custom_heading_code'] == '') {
				echo '<h3 id="vrp_h3" style="margin-top:0px; font-size:'.$vrpOptions['header_text_font_size'].'px;"><span class="h3_drop_cap">'.$vrp_h3_first_letter.'</span>'.$vrp_h3_the_rest.'</h3>';
			}
			

			$vrp_counter = 0;
			global $post;
			$myposts = get_posts('numberposts='.$vrpOptions['number_of_posts'].'&offset='.$vrpOptions['offset']);
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
					if(intval($vrpOptions['box_width']) > (intval($vrpOptions['image_width'])+17)) {
						echo intval($vrpOptions['box_width']).'px;';
					} else {
						echo (intval($vrpOptions['image_width']) + 17).'px;';
					}
					if($vrpOptions['float_left'] == 'true') {
						echo ' float:left;';
					}
					echo 'height:';
					if(intval($vrpOptions['box_height']) > (intval($vrpOptions['image_height'])+17)) {
						echo intval($vrpOptions['box_height']).'px;';
					} else {
						echo (intval($vrpOptions['image_height']) + 17).'px;';
					}
					//if($vrpOptions['include_post_excerpt'] == 'false') {
					//echo 'height:'.(intval($vrpOptions['image_height']) + 17).'px;';
					//}
					echo '">';
				
					echo '<a href="';
					echo the_permalink();
					echo '">';
				 	echo image_extractor($resize = true, $resize_type = 1, $width = intval($vrpOptions['image_width']), $height = intval($vrpOptions['image_height']), $class = 'vrp_img_mag', $id = '', $prefix='', $suffix='', $post=$post);
					echo '</a>';
				
					if($vrpOptions['include_post_title'] == 'true') {
						echo '<div id="vrp_title_caption_mag"><h3><a style="';
						echo ' font-size:'.$vrpOptions['title_caption_font_size'].'px;" href="';
						echo the_permalink();
						echo '">';
						echo the_title();
						echo '</a></h3></div>';
					} else {
						echo '<div style="padding-top:10px;"></div>';
					}
				
					if($vrpOptions['include_post_excerpt'] == 'true') {
						echo '<div id="vrp_excerpt_mag" style="padding-top:0px; font-size:'.$vrpOptions['excerpt_font_size'].'px;">';
						echo '<a style="text-decoration:none; color:#111111;" href="';
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
					
						update_option($this->adminOptionsName, $vrpOptions);
						
						?>
<div class="updated"><p><strong><?php _e("Settings Updated.", "VisualRecentPostsPlugin");?></strong></p></div>
					<?php
					} ?>
<div class=wrap>
<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<h2>Visual Recent Posts Plugin</h2>

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
<li>thesis_hook_before_post</li>
<li>thesis_hook_before_sidebar_1</li>
<li>thesis_hook_after_multimedia_box</li>
</ul>
<p>For a complete reference of Thesis hooks, see <a href="http://diythemes.com/thesis/rtfm/hooks/">diythemes.com/thesis/rtfm/hooks/</a></p>
<p>If you don't have hooks, simply insert `<?php insertVisualRecentPosts(); ?>` wherever you want things to show up.</p>

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

<h3>Include Post Excerpt</h3>
<p>This only works if you use the actual excerpt field when you write your post, otherwise you'll get the excerpt of whatever page you're viewing. Stupid, I know, but the geeks at Wordpress assure me that if I understood The Loop things like this could be avoided. The Loop must be some drug they are on. I wonder where they get it . . . .</p>
<label for="include_post_excerpt_yes"><input type="radio" id="include_post_excerpt_yes" name="vrp_include_post_excerpt" value="true" <?php if ($vrpOptions['include_post_excerpt'] == "true") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?> /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label for="include_post_excerpt_no"><input type="radio" id="include_post_excerpt_no" name="vrp_include_post_excerpt" value="false" <?php if ($vrpOptions['include_post_excerpt'] == "false") { _e('checked="checked"', "VisualRecentPostsPlugin"); }?>/> No</label>

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
		//add_action('wp_head', array(&$dl_pluginVRP, 'uploadVRPCSS'));
		add_action($dl_pluginVRP->getVrpLocationHook(), array(&$dl_pluginVRP,'addVRP'), 1);
	}
	
	//Filters
	//add_filter('the_content', array(&$dl_pluginVRP, 'addContent'),1); 
	//add_filter('get_comment_author', array(&$dl_pluginVRP, 'authorUpperCase'));
}

function insertVisualRecentPosts() {
	$vrp_class = new VisualRecentPostsPlugin();
	$vrp_class->addVRP();
}

?>
