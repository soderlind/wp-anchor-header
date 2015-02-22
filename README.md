# WP Anchor Header

Generates anchored headings (H1-H6), creating a heading like this:

```html
<h2 id="the-heading">
	The Heading<a class="anchorlink dashicons-before" href="#the-heading"></a>
</h2>
```

The links are styled using the following CSS:

```css
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
```

## Installation

1. Download the plugin and extract the wp-anchor-header.zip
1. Upload the extracted `wp-anchor-header` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

## Credits

The plugin is inspired by Christian Weiske and his article [Usability: Clickable heading links](http://cweiske.de/tagebuch/html-heading-links.htm).

Latest Stable Release: [0.1.5](https://github.com/soderlind/wp-anchor-header/releases/tag/0.1.5)

~Current Version: 0.1.5~
