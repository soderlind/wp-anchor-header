/**
* @desc WP Anchorific
* @author Per Soderlind - http://soderlind.no
*/
jQuery(document).ready(function($){
	$('.entry-content').anchorific({
	    navigation: '.anchorific', // position of navigation
	    speed: 200, // speed of sliding back to top
	    anchorClass: 'anchor', // class of anchor links
	    anchorText: '#', // prepended or appended to anchor headings
	    top: '.top', // back to top button or link class
	    spy: true, // scroll spy
	    position: 'prepend', // position of anchor text
	    spyOffset: 0 // specify heading offset for spy scrolling
	});
});