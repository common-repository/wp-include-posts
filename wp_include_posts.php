<?php
/*
Plugin Name: WP-Include-Posts
Plugin URI: http://www.thusjanthan.com/WP-Include-Posts
Description: This allows you to put code into your posts such as [include id=post_id]. This will fetch the contents of the post_id and replace it with the include tag. 
Version: 1
Author: Thusjanthan Kubendranathan
Author URI: http://www.thusjanthan.com
*/

function wp_include_posts(){
}

function active_wp_include_posts(){
        add_option('wp_include_posts','1','active the plugin');
}

function deactive_wp_include_posts(){
    delete_option('wp_include_posts');
}

function render_notes($text) {
	global $wpdb;
    $note_tag_elements = array(
        '/\[include\s+id=\d+\s*\]/'
    );

   
    
    foreach ($note_tag_elements as $tags) {
    	preg_match_all($tags, $text, $matches); 
    	 
    	if(isset($matches[0]) && is_array($matches[0])){
	    	foreach($matches[0] as $match){
	    		preg_match("/id=([0-9]+)/", $match, $ids);
	    		if(is_array($ids) && isset($ids[1])){
	    			$post_id = $ids[1]; 
	    			$post_content = $wpdb->get_row("SELECT * FROM wp_posts WHERE id = " . $post_id);
	    			if(isset($post_content) && isset($post_content->post_content)){
	    				$html_content = html_entity_decode($post_content->post_content); 
	    				$replace_tag = '/(<p>)*\[include\s+id=' . $post_id . '\s*\](<\/p>)*/';
	    				$text = preg_replace($replace_tag, $html_content, $text);
	    			}
	    		} 
	    	}

    	}	
    }
    return $text;
}

add_filter('the_content', 'render_notes', 10);
add_filter('the_excerpt', 'render_notes', 10);
add_action('wp_head', 'wp_include_posts');

register_activation_hook(__FILE__,'active_wp_include_posts');
register_deactivation_hook(__FILE__,'deactive_wp_include_posts');

?>
