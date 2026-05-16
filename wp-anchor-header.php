<?php
/*
Plugin Name: WP Anchor Header
Plugin URI: https://github.com/soderlind/wp-anchor-header
Description: Generates anchored headings.
Author: Per Soderlind
Version: 0.3.0
Author URI: http://soderlind.no
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 7.2
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ANCHORHEADER_URL', plugin_dir_url( __FILE__ ) );
define( 'ANCHORHEADER_VERSION', '0.3.0' );


class Anchor_Header {

	/**
	 * Refers to a single instance of this class
	 *
	 * @var Anchor_Header|null
	 */
	private static $instance = null;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since 0.1.8
	 * @return object Anchor_Header, a single instance of this class.
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Load style and attach $this->the_content to the the_content filter
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
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
	public function the_content( $content ) {

		if ( ! is_singular() || '' === $content ) {
			return $content;
		}

		$anchors = array();
		$doc     = new DOMDocument();

		// START LibXML error management.
		$libxml_previous_state = libxml_use_internal_errors( true );
		$loaded                 = $doc->loadHTML( '<?xml encoding="UTF-8">' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
		libxml_clear_errors();
		libxml_use_internal_errors( $libxml_previous_state );
		// END LibXML error management.

		if ( ! $loaded ) {
			return $content;
		}

		$this->remove_encoding_processing_instruction( $doc );

		foreach ( array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) as $tag_name ) {
			$headings = $doc->getElementsByTagName( $tag_name );

			foreach ( $headings as $heading ) {
				if ( ! $heading instanceof DOMElement ) {
					continue;
				}

				$slug = $this->get_heading_slug( $heading, $anchors );
				if ( '' === $slug ) {
					continue;
				}

				$anchors[] = $slug;
				$heading->setAttribute( 'id', $slug );

				if ( $this->has_anchor_link( $heading ) ) {
					continue;
				}

				$anchor_link = $doc->createElement( 'a' );
				$anchor_link->setAttribute( 'class', 'anchorlink dashicons-before' );
				$anchor_link->setAttribute( 'href', '#' . $slug );
				$heading->appendChild( $anchor_link );
			}
		}

		return $doc->saveHTML();
	}

	/**
	 * Remove the XML processing instruction used to force UTF-8 handling.
	 *
	 * @since 0.3.0
	 *
	 * @param DOMDocument $doc Parsed content document.
	 */
	private function remove_encoding_processing_instruction( $doc ) {
		foreach ( $doc->childNodes as $node ) {
			if ( XML_PI_NODE === $node->nodeType ) {
				$doc->removeChild( $node );
				return;
			}
		}
	}

	/**
	 * Get a unique anchor slug for a heading.
	 *
	 * @since 0.3.0
	 *
	 * @param DOMElement $heading Heading element.
	 * @param array      $anchors Existing anchors in the current content.
	 * @return string Unique anchor slug.
	 */
	private function get_heading_slug( $heading, $anchors ) {
		$base_slug = $heading->hasAttribute( 'id' ) ? $heading->getAttribute( 'id' ) : sanitize_title( $heading->textContent );
		$base_slug = sanitize_title( $base_slug );

		if ( '' === $base_slug ) {
			$base_slug = 'heading';
		}

		$slug   = $base_slug;
		$suffix = 2;

		while ( in_array( $slug, $anchors, true ) ) {
			$slug = sprintf( '%s-%d', $base_slug, $suffix++ );
		}

		return $slug;
	}

	/**
	 * Determine whether a heading already contains an anchor link.
	 *
	 * @since 0.3.0
	 *
	 * @param DOMElement $heading Heading element.
	 * @return bool True when an anchor link exists.
	 */
	private function has_anchor_link( $heading ) {
		$links = $heading->getElementsByTagName( 'a' );

		foreach ( $links as $link ) {
			if ( ! $link instanceof DOMElement ) {
				continue;
			}

			$classes = preg_split( '/\s+/', $link->getAttribute( 'class' ) );

			if ( in_array( 'anchorlink', $classes, true ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Enable dashicons on the front-end
	 * Load style
	 *
	 * @since 0.1.0
	 */
	public function add_script_style() {
		if ( is_singular() ) {
			wp_enqueue_style( 'dashicons' );
			wp_enqueue_style( 'achored-header', ANCHORHEADER_URL . 'css/achored-header.css', array( 'dashicons' ), ANCHORHEADER_VERSION );
		}
	}
} // class Anchor_Header

Anchor_Header::instance();
