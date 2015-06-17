<?php
/*
Plugin Name: WP Anchor Header
Plugin URI: https://github.com/soderlind/wp-anchor-header
Description: Generates anchored headings.
Author: Per Soderlind
Version: 0.1.8
Author URI: http://soderlind.no
*/

if ( defined( 'ABSPATH' ) ) {
	Anchor_Header::instance();
}

define( 'ANCHORHEADER_URL',   plugin_dir_url( __FILE__ ) );
define( 'ANCHORHEADER_VERSION', '0.1.8' );


class Anchor_Header {

	/**
	 * Refers to a single instance of this class
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since 0.1.8
	 * @return object Anchor_Header, a single instance of this class.
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Load style and attach $this->the_content to the the_content filter
	 *
	 * @since 0.1.0
	 */
	function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'add_script_style' ) );
		add_filter( 'the_content', array( $this, 'the_content' ) );
	}

	/**
	 * Using DOMDocument, parse the content and add anchors to headers (H1-H6)
	 *
	 * @since 0.1.0
	 *
	 * @param string  $content The content
	 * @return string          the content, updated if the content has H1-H6
	 */
	function the_content( $content ) {

		if ( ! is_singular() ) {
			return $content;
		}
		$anchors = array();
		$doc = new DOMDocument();
		@$doc->loadHTML( mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8') );
		foreach ( array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) as $h ) {
			$headings = $doc->getElementsByTagName( $h );
			foreach ( $headings as $heading ) {
				$a = $doc->createElement( "a" );
				$newnode = $heading->appendChild( $a );
				$newnode->setAttribute( "class", "anchorlink dashicons-before" );
				$slug = $tmpslug = sanitize_title( $heading->nodeValue );
				$i = 2;
				while ( false !== in_array( $slug, $anchors ) ) {
					$slug = sprintf( "%s-%d", $tmpslug, $i++ );
				}
				$anchors[] = $slug;
				$heading->setAttribute( "id", $slug );
				$newnode->setAttribute( "href", '#' . $slug );
			}
		}
		return $doc->saveHTML();
	}

	/**
	 * Enable dashicons on the front-end
	 * Load style
	 *
	 * @since 0.1.0
	 */
	function add_script_style() {
		if ( is_singular() ) {
			wp_enqueue_style( 'dashicons' );
			wp_enqueue_style( 'achored-header',  ANCHORHEADER_URL . 'css/achored-header.css', array( 'dashicons' ), ANCHORHEADER_VERSION );
		}
	}
} // class Anchor_Header
