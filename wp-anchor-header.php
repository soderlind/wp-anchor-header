<?php
/*
Plugin Name: WP Anchor Header
Plugin URI: https://github.com/soderlind/wp-anchor-header
Description: Generates anchored headings.
Author: Per Soderlind
Version: 0.1.7
Author URI: http://soderlind.no
*/

defined( 'ABSPATH' ) or die();
define( 'ANCHORHEADER_URL',   plugin_dir_url( __FILE__ ));
define( 'ANCHORHEADER_VERSION', '0.1.7' );

class Anchor_Header {

	function __construct() {
		add_action('wp_enqueue_scripts', array($this,'add_script_style'));
		add_filter( 'the_content', array($this, 'the_content' ));
	}

	function the_content($content) {
		$anchors = array();
		$doc = new DOMDocument();
		@$doc->loadHTML($content);
		foreach (array('h1','h2','h3','h4','h5','h6') as $h) {
			$headings = $doc->getElementsByTagName($h);
			foreach ($headings as $heading) {
				$a = $doc->createElement("a");
				$newnode = $heading->appendChild($a);
				$newnode->setAttribute("class", "anchorlink dashicons-before");
				$slug = $tmpslug = sanitize_title($heading->nodeValue);
				$i = 2;
				while (false !== in_array($slug, $anchors)) {
					$slug = sprintf("%s-%d",$tmpslug,$i++);
				}
				$anchors[] = $slug;
				$heading->setAttribute("id", $slug);
				$newnode->setAttribute("href", '#' . $slug);
			}
		}
		return $doc->saveHTML();
	}

	function add_script_style() {
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'achored-header',  ANCHORHEADER_URL . 'css/achored-header.css', array('dashicons'), ANCHORHEADER_VERSION );
	}
} // class Anchor_Header
$n = new Anchor_Header();
