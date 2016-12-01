<?php
/*
Plugin Name: Carrington Sniffer
Description: Sniff out Carrington Build pages
Version: 0.1
Author: Boise State University OIT/EAS WP Support Team
*/

add_action('admin_menu', 'init');
	
/**
 * Setup plugin by adding a WordPress Media Page
 */
function init() {
	$hook = add_options_page('Carrington Sniffer', 'Carrington Sniffer', 'administrator', 'cs', 'csAdminMain');
	add_action('load-' . $hook, 'csLoad');
}

function csLoad() {
	wp_enqueue_style('csStyles', plugins_url() . '/carrington-sniffer/csStyles.css');
}

function csAdminMain() {
	global $wpdb;
	echo '<div class="wrap">';
	
	echo csGatherData($wpdb);
	
	echo '</div>';
}

function csGatherData($wpdb) {
	$html = '<h2>Carrington Sniffer</h2>';
	$prefix = $wpdb->base_prefix;
	$counter = array(
		'cb' => 0,
		'ngg' => 0,
		'faq' => 0);
	
	$blogs = $wpdb->get_results("SELECT `blog_id`, `domain`, `path` FROM `{$prefix}blogs`");
	
	foreach ($blogs as $blog) {
		$table = '';
		
		if ($blog->blog_id == 1)
			$table = $prefix;
		else
			$table = $prefix . $blog->blog_id . '_';
		
		$html .= '<table><tr"><th class="large site-title" colspan="2">' . $blog->path . '</th></tr><tr><th>Post ID</th><th>Post Title</th></tr><tr class="red"><td class="large" colspan="2">Carrington Build Pages</td></tr>';
		
		$cbPages = $wpdb->get_results("SELECT `ID`, `post_title` FROM `{$table}posts` WHERE `post_content` LIKE '%CFCT-BD%' AND `post_type` = 'page'");
		
		foreach ($cbPages as $page) {
			$counter['cb']++;
			$html .= '<tr class="red"><td>' . $page->ID . '</td><td><a target="_blank" href="http://' . $blog->domain . $blog->path . 'wp-admin/post.php?post=' . $page->ID . '&action=edit">' . $page->post_title . '</a></td></tr>';
		}

		$html .= '<tr class="yellow"><td class="large" colspan="2">NGG Galleries</td></tr>';
		$galleries = $wpdb->get_results("SELECT `title` FROM `{$table}ngg_gallery`");
		
		foreach ($galleries as $gallery) {
			$counter['ngg']++;
			$html .= '<tr class="yellow"><td></td><td>' . $gallery->title . '</td></tr>';
		}
		
		$html .= '<tr class="red"><td class="large" colspan="2">FAQ Posts</td></tr>';
		$faqs = $wpdb->get_results("SELECT `ID`, `post_title` FROM `{$table}posts` WHERE `post_type` = 'question'");
		
		foreach ($faqs as $faq) {
			$counter['faq']++;
			$html .= '<tr class="red"><td>' . $faq->ID . '</td><td><a target="_blank" href="http://' . $blog->domain . $blog->path . 'wp-admin/post.php?post=' . $faq->ID . '&action=edit">' . $faq->post_title . '</a></td></tr>';
		}
		
		$html .= '<tr class="yellow"><td class="large" colspan="2">Scripts n Styles Snippets</td></tr>';
		$scripts = $wpdb->get_results("SELECT `ID`, `post_title` FROM `{$table}posts` RIGHT JOIN `{$table}postmeta` ON `{$table}postmeta`.`post_id` = `{$table}posts`.`ID` WHERE `{$table}postmeta`.`meta_key` = '_SNS'");
	
		foreach ($scripts as $script) {
			$html .= '<tr class="yellow"><td>' . $script->ID . '</td><td><a target="_blank" href="http://' . $blog->domain . $blog->path . 'wp-admin/post.php?post=' . $script->ID . '&action=edit">' . $script->post_title . '</a></td></tr>';
		}
		
		$html .= '</table>';
	}

	/*$result = mysql_query("SELECT `option_value` FROM `wp_options` WHERE `option_name` = 'active_plugins'");
	$data = mysql_fetch_assoc($result);
	print_r(unserialize($data['option_value']));*/

	$html .= '<table style="width: 25%; border: none;"><tr><td><b>Total CB Pages: </b></td><td>' . $counter['cb'] . '</td></tr>';
	$html .= '<tr><td><b>Total NGGs: </b></td><td>' . $counter['ngg'] . '</td></tr>';
	$html .= '<tr><td><b>Total FAQ Posts: </b></td><td>' . $counter['faq'] . '</td></tr></table>';
	
	return $html;
}