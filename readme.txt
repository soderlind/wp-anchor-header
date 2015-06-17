=== WP Anchor Header ===
Contributors: PerS
Donate link: http://soderlind.no/donate/
Tags: header, link
Requires at least: 4.0
Tested up to: 4.3
Stable tag: 0.1.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WP Anchor Header generates anchored headings.

== Description ==

WP Anchor Header generates anchored headings (H1-H6), creating a heading like this:

`
<h2 id="the-heading">
   The Heading<a class="anchorlink dashicons-before" href="#the-heading"></a>
</h2>
`

The links are styled using the following CSS:

`
/* show IDs for anchors */
h1[id]:hover a.anchorlink:before,
h2[id]:hover a.anchorlink:before,
h3[id]:hover a.anchorlink:before,
h4[id]:hover a.anchorlink:before,
h5[id]:hover a.anchorlink:before,
h6[id]:hover a.anchorlink:before {
    content: "\f103"; /*dashicons-admin-links*/
    color: #888;
    font-size: smaller;
    text-decoration: none;
    vertical-align: baseline;
}
a.anchorlink {
    text-decoration: none;
    margin-left: 0.5em;
    font-size: smaller;
}
`

=== Credits ===

The plugin is inspired by Christian Weiske and his article [Usability: Clickable heading links](http://cweiske.de/tagebuch/html-heading-links.htm).

== Installation ==

1. Download the plugin and extract the read-online.zip
1. Upload the extracted `read-online` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress



== Changelog ==
= 0.1.8 =
* Fixed UTF-8 parsing bug, added `mb_convert_encoding` to loadHTML(): `@$doc->loadHTML( mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8') );`
* Added singleton
* Added comments to code
= 0.1.5 =
* Initial release