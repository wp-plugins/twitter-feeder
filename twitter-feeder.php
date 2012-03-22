<?php
/**
 * Plugin Name: Twitter Feeder
 
 * Description: Twitter Feeder allows you to add your twitter account to your sidebar of your blog, showing tweets to your visitors.
 
 * Author: Podz
 
 * Version: 1.1
 
 * Author Email: 
 
 * License: GPLv2 or later 
 */


/*  Copyright 2012 
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
	
    **********************************************************************
	
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    **********************************************************************

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



define('MY_VERSION', '1.0');
define('MY_PLUGINBASENAME', dirname(plugin_basename(__FILE__)));
define('MY_PLUGINPATH', PLUGINDIR . '/' . MY_PLUGINBASENAME);

class twitterfeeder_widget extends WP_Widget {

	function twitterfeeder_widget() {
	
		if(function_exists('load_plugin_textdomain')) {
			load_plugin_textdomain('tw_wp', tw_wp_PLUGINPATH . '/languages', MY_PLUGINBASENAME . '/languages');
		}

		$widget_ops = array(
			'classname' => 'twitterfeeder_widget',
			'description' => __('List your last tweet by displaying content, date, and link to follow you', 'tw_wp')
		);

		$control_ops = array();

		$this->WP_Widget('twitterfeeder_widget', __('Twitter WP', 'tw_wp'), $widget_ops, $control_ops);
	}

	function form($instance) {
	
		$instance = wp_parse_args((array) $instance, array(
			'twitter_wp_title' => '',
			'twitter_wp_username' => '',
			'twitter_wp_no_tweets' => '1',
			'twitter_wp_show_avatar' => false,
			'twitter_wp_cache_duration' => 0,
			'twitter_wp_default_css' => false
		));
		
		$default_css_checked = ' checked="checked"';
		if ( $instance['twitter_wp_default_css'] == false )
			$default_css_checked = '';
			
		$show_avatar_checked = ' checked="checked"';
		if ( $instance['twitter_wp_show_avatar'] == false )
			$show_avatar_checked = '';
			

		// Version of the plugin (hidden field)
		$jzoutput  = '<input id="' . $this->get_field_id('plugin-version') . '" name="' . $this->get_field_name('plugin-version') . '" type="hidden" value="' . tw_wp_VERSION . '" />';

		// Title
		$jzoutput .= '
			<p style="border-bottom: 1px solid #DFDFDF;">
				<label for="' . $this->get_field_id('twitter_wp_title') . '"><strong>' . __('Title', 'tw_wp') . '</strong></label>
			</p>
			<p>
				<input id="' . $this->get_field_id('twitter_wp_title') . '" name="' . $this->get_field_name('twitter_wp_title') . '" type="text" value="' . $instance['twitter_wp_title'] . '" />
			</p>
		';

		// Settings
		$jzoutput .= '
			<p style="border-bottom: 1px solid #DFDFDF;"><strong>' . __('Preferences', 'tw_wp') . '</strong></p>
	
			<p>
				<label>' . __('Username', 'tw_wp') . '<br />
				<span style="color:#999;">@</span><input id="' . $this->get_field_id('twitter_wp_username') . '" name="' . $this->get_field_name('twitter_wp_username') . '" type="text" value="' . $instance['twitter_wp_username'] . '" /> <abbr title="' . __('No @, just your username', 'tw_wp') . '">(?)</abbr></label>
			</p>
			<p>
				<label>' . __('Number of tweets to show', 'tw_wp') . '<br />
				<input style="margin-left: 1em;" id="' . $this->get_field_id('twitter_wp_no_tweets') . '" name="' . $this->get_field_name('twitter_wp_no_tweets') . '" type="text" value="' . $instance['twitter_wp_no_tweets'] . '" /> <abbr title="' . __('Just a number, between 1 and 5 for example', 'tw_wp') . '">(?)</abbr></label>
			</p>
			<p>
				<label>' . __('Duration of cache', 'tw_wp') . '<br />
				<input style="margin-left: 1em; text-align:right;" id="' . $this->get_field_id('twitter_wp_cache_duration') . '" name="' . $this->get_field_name('twitter_wp_cache_duration') . '" type="text" size="10" value="' . $instance['twitter_wp_cache_duration'] . '" /> '.__('Seconds', 'tw_wp').' <abbr title="' . __('A big number save your page speed. Try to use the delay between each tweet you make. (e.g. 1800 s = 30 min)', 'tw_wp') . '">(?)</abbr></label>
			</p>
			<p>
				<label>' . __('Show your avatar?', 'tw_wp') . ' 
				<input type="checkbox" name="' . $this->get_field_name('twitter_wp_show_avatar') . '" id="' . $this->get_field_id('twitter_wp_show_avatar') . '"'.$show_avatar_checked.' /> <abbr title="' . __("If it's possible, display your avatar at the top of twitter list", 'tw_wp') . '">(?)</abbr></label>
			</p>
		';
		
		// Default & Own CSS
		$jzoutput .= '
			<p style="border-bottom: 1px solid #DFDFDF;"><strong>' . __('Manage CSS', 'tw_wp') . '</strong></p>
			
			<p>
				<label>' . __('Use the default CSS?', 'tw_wp') . ' 
				<input type="checkbox" name="' . $this->get_field_name('twitter_wp_default_css') . '" id="' . $this->get_field_id('twitter_wp_default_css') . '"'.$default_css_checked.' /> <abbr title="' . __('Load a little CSS file with default styles for the widget', 'tw_wp') . '">(?)</abbr></label>
			</p>
			<p>
				<label for="' . $this->get_field_id('my-tw-own-css') . '" style="display:inline-block;">' . __('Your own CSS', 'tw_wp') . ':  <abbr title="' . __('Write your CSS here to replace or overwrite the default CSS', 'tw_wp') . '">(?)</abbr></label>
				<textarea id="' . $this->get_field_id('my-tw-own-css') . '" rows="7" cols="30" name="' . $this->get_field_name('my-tw-own-css') . '">' . $instance['my-tw-own-css'] . '</textarea>
			</p>
		';
		
		echo $jzoutput;
	}

	function update($new_instance, $old_instance) {
		
		$instance = $old_instance;

		$new_instance = wp_parse_args((array) $new_instance, array(
			'twitter_wp_title' => '',
			'twitter_wp_username' => '',
			'twitter_wp_no_tweets' => '1',
			'twitter_wp_show_avatar' => false,
			'twitter_wp_cache_duration' => 0,
			'twitter_wp_default_css' => false
		));

		$instance['plugin-version'] = strip_tags($new_instance['twitter_wp-version']);
		$instance['twitter_wp_title'] = strip_tags($new_instance['twitter_wp_title']);
		$instance['twitter_wp_username'] = strip_tags($new_instance['twitter_wp_username']);
		$instance['twitter_wp_no_tweets'] = strip_tags($new_instance['twitter_wp_no_tweets']);
		$instance['twitter_wp_show_avatar'] = strip_tags($new_instance['twitter_wp_show_avatar']);
		$instance['twitter_wp_cache_duration'] = $new_instance['my_cache_duration'];
		$instance['twitter_wp_default_css'] = $new_instance['twitter_wp_default_css'];
		$instance['my-tw-own-css'] = $new_instance['my-tw-own-css'];

		return $instance;
	}

	function widget($args, $instance) {
		extract($args);

		echo $before_widget;

		$title = (empty($instance['twitter_wp_title'])) ? '' : apply_filters('widget_title', $instance['twitter_wp_title']);

		if(!empty($title)) {
			echo $before_title . $title . $after_title;
		}

		echo $this->twitter_wp_output($instance, 'widget');
		echo $after_widget;
			}

	function twitter_wp_output($args = array(), $position) {
		
		$the_username = $args['twitter_wp_username'];
		$the_username = preg_replace('#^@(.+)#', '$1', $the_username);
		$the_nb_tweet = $args['twitter_wp_no_tweets'];
		$need_cache = ($args['twitter_wp_cache_duration']!='0') ? true : false;
		$show_avatar = ($args['twitter_wp_show_avatar']) ? true : false;




		if ( !function_exists ('tw_wp_filter_handler') ) {
			function tw_wp_filter_handler ( $seconds ) {
				// change the default feed cache recreation period to 2 hours
				return intval($args['twitter_wp_cache_duration']); //seconds
			}
		}
		add_filter( 'wp_feed_cache_transient_lifetime' , 'tw_wp_filter_handler' ); 
		 
		
			function jltw_format_since($date){
				
				$timestamp = strtotime($date);
				
				$the_date = '';
				$now = time();
				$diff = $now - $timestamp;
				
				if($diff < 60 ) {
					$the_date .= $diff.' ';
					$the_date .= ($diff > 1) ?  __('Seconds', 'tw_wp') :  __('Second', 'tw_wp');
				}
				elseif($diff < 3600 ) {
					$the_date .= round($diff/60).' ';
					$the_date .= (round($diff/60) > 1) ?  __('Minutes', 'tw_wp') :  __('Minute', 'tw_wp');
				}
				elseif($diff < 86400 ) {
					$the_date .=  round($diff/3600).' ';
					$the_date .= (round($diff/3600) > 1) ?  __('Hours', 'tw_wp') :  __('Hour', 'tw_wp');
				}
				else {
					$the_date .=  round($diff/86400).' ';
					$the_date .= (round($diff/86400) > 1) ?  __('Days', 'tw_wp') :  __('Day', 'tw_wp');
				}
			
				return $the_date;
			}
			
			function jltw_format_tweettext($raw_tweet, $username) {

				$i_text = htmlspecialchars_decode($raw_tweet);
				/* $i_text = preg_replace('#(([a-zA-Z0-9_-]{1,130})\.([a-z]{2,4})(/[a-zA-Z0-9_-]+)?((\#)([a-zA-Z0-9_-]+))?)#','<a href="//$1">$1</a>',$i_text); */
				$i_text = preg_replace('#(((https?|ftp)://(w{3}\.)?)(?<!www)(\w+-?)*\.([a-z]{2,4})(/[a-zA-Z0-9_-]+)?)#',' <a href="$1" rel="nofollow" class="twitter_wp_url">$5.$6$7</a>',$i_text);
				$i_text = preg_replace('#@([a-zA-z0-9_]+)#i','<a href="http://twitter.com/$1" class="twitter_wp_tweetos" rel="nofollow">@$1</a>',$i_text);
				$i_text = preg_replace('#[^&]\#([a-zA-z0-9_]+)#i',' <a href="http://twitter.com/#!/search/%23$1" class="twitter_wp_hastag" rel="nofollow">#$1</a>',$i_text);
				$i_text = preg_replace( '#^'.$username.': #i', '', $i_text );
				
				return $i_text;
			
			}
			
			function jltw_format_tweetsource($raw_source) {
			
				$i_source = htmlspecialchars_decode($raw_source);
				$i_source = preg_replace('#^web$#','<a href="http://twitter.com">Twitter</a>', $i_source);
				
				return $i_source;
			
			}
			
			
			function jltw_get_the_user_timeline($username, $nb_tweets, $show_avatar) {
				
				$username = (empty($username)) ? 'wordpress' : $username;
				$nb_tweets = (empty($nb_tweets) OR $nb_tweets == 0) ? 1 : $nb_tweets;
				$xml_result = $the_best_feed = '';
				
				// include of WP's feed functions
				include_once(ABSPATH . WPINC . '/feed.php');
				
				// some RSS feed with timeline user
				$search_feed1 = "http://api.twitter.com/1/statuses/user_timeline.rss?screen_name=".$username."&count=".intval($nb_tweets);
				$search_feed2 = "http://search.twitter.com/search.rss?q=from%3A".$username."&rpp=".intval($nb_tweets);

				
				// get the better feed
				// try with the first one
				
				$sf_rss = fetch_feed ( $search_feed1 );
				if ( is_wp_error($sf_rss) ) {
					// if first one is not ok, try with the second one
					$sf_rss = fetch_feed ( $search_feed2 );
					
					if ( is_wp_error($sf_rss) ) $the_best_feed = false;
					else $the_best_feed = '2';
				}
				else $the_best_feed = '1';
				
				// if one of the rss is readable
				if ( $the_best_feed ) {
					$max_i = $sf_rss -> get_item_quantity($nb_tweets);
					$rss_i = $sf_rss -> get_items(0, $max_i);
					$i = 0;
					foreach ( $rss_i as $tweet ) {
						$i++;
						$i_title = jltw_format_tweettext($tweet -> get_title() , $username);
						$i_creat = jltw_format_since( $tweet -> get_date() );
						
						$i_guid = $tweet->get_link();
						
						$author_tag = $tweet->get_item_tags('','author');
						$author_a = $author_tag[0]['data'];
						$author = substr($author_a, 0, stripos($author_a, "@") );
						
						$source_tag = $tweet->get_item_tags('http://api.twitter.com','source');
						$i_source = $source_tag[0]['data'];
						$i_source = jltw_format_tweetsource($i_source);
						$i_source = ($i_source) ? '<span class="my_source">via ' . $i_source : '</span>';
						
						if ( $the_best_feed == '1' && $show_avatar) {
							$avatar = "http://api.twitter.com/1/users/profile_image/". $username .".xml?size=normal"; // or bigger
						}
						elseif ($the_best_feed == '2' && $show_avatar) {
							$avatar_tag = $tweet->get_item_tags('http://base.google.com/ns/1.0','image_link');
							$avatar = $avatar_tag[0]['data'];
						}
						
						$html_avatar = ($i==1 && $show_avatar && $avatar) ? '<span class="user_avatar"><a href="http://twitter.com/' . $username . '" title="' . __('Follow', 'tw_wp') . ' @'.$author.' ' . __('on twitter.', 'tw_wp') . '"><img src="'.$avatar.'" alt="'.$author.'" width="48" height="48" /></a></span>' : '';
						//echo $i_title.'<br />'.$i_creat.'<br />'.$link_tag.'<br />'.$author.'('.$avatar.')<br /><br />';
						$xml_result .= '
							<li>
								'.$html_avatar.'
								<span class="my_lt_content">' . $i_title . '</span>
								<em class="twitter_wp_inner">' . __('Time ago', 'tw_wp') . '
									<a href="'.$i_guid .'" target="_blank">' . $i_creat . '</a>
									'. $i_source .'
								</em>
							</li>
						';
					}
				}
				// if any feed is readable
				else 
					$xml_result = '<li><em>'.__('The RSS feed for this twitter account is not loadable for the moment.', 'tw_wp').'</em></li>';

				return $xml_result;
			}
			
			// display the widget front content (but not immediatly because of cache system)
			echo '
				<div class="twitter_wp_inside">
					<ul id="twitter_wp_tweetlist">
						'. jltw_get_the_user_timeline($the_username, $the_nb_tweet, $show_avatar) .'
						
				
				</div>					
					</ul>
				<p class="twitter_wp_follow_us" style="margin: 10px 0;"> 
						<span class="tw_wp_follow">' . __('Follow', 'tw_wp') . '</span>
						<a class="tw_wp_username" href="http://twitter.com/' . $the_username . '">@' . $the_username . '</a>
						<span class="tw_wp_ontwitter">' . __('on twitter.', 'tw_wp') . '</span>
					</p>
			';
	}
}

add_action('widgets_init', create_function('', 'return register_widget("twitterfeeder_widget");'));

/**
 * Custom styles et <del>JS</del>
 */
 if(!is_admin()) {

	function twitter_wp_head() {

		$twitter_wp_css = '';
		$$use_default_css = $var_sOwnCSS = '';
		
		$array_widgetOptions = get_option('widget_twitterfeeder_widget');
		
		foreach($array_widgetOptions as $key => $value) {
			if($value['my-tw-own-css'])
				$var_sOwnCSS = $value['my-tw-own-css'];
			elseif($value['twitter_wp_default_css']) {
				$use_default_css = $value['twitter_wp_default_css'];
			
			}
		}
		
		if ( $use_default_css )
			// wp_enqueue_style() add the style in the footer of document... why ? Oo
			$twitter_wp_css .= '<link type="text/css" media="all" rel="stylesheet" id="twitterfeeder_widget_styles" href="'. plugins_url(MY_PLUGINBASENAME."/css/twitter_wp.css") . '" />';

		if ( $var_sOwnCSS != '' ) {
			$twitter_wp_css .= '
				<style type="text/css">
					<!--
					'  . $var_sOwnCSS . '
					-->
				</style>
			';
		}
		
		echo $twitter_wp_css;
	}

	function twitter_wp_footer() {
		$var_custom_my_scripts = "\n\n".'<!-- No script for Twitter WP Widget :) -->'."\n\n";
		echo $var_custom_my_scripts;
	}

	// custom head and footer
	add_action('wp_head', 'twitter_wp_head');
	add_action('wp_footer', 'twitter_wp_footer');
}
?>