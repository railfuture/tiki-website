<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_galleriffic.php 39469 2012-01-12 21:13:48Z changi67 $

function wikiplugin_galleriffic_info()
{
	return array(
		'name' => tra('Galleriffic'),
		'documentation' => 'PluginGalleriffic',
		'description' => tra('Displays images in galleriffic'),
		'prefs' => array('wikiplugin_galleriffic', 'feature_file_galleries'),
		'params' => array(
			'fgalId' => array(
				'required' => true,
				'name' => tra('File Gallery ID'),
				'description' => tra('ID number of the file gallery that contains the images to be displayed'),
				'filter' => 'digits',
				'accepted' => 'ID',
				'default' => '',
			),
			'sort_mode' => array(
				'required' => false,
				'name' => tra('Sort Mode'),
				'description' => tra('Sort by database table field name, ascending or descending. Examples: fileId_asc or name_desc.'),
				'filter' => 'word',
				'accepted' => 'fieldname_asc or fieldname_desc with actual table field name in place of \'fieldname\'.',
				'default' => 'created_desc',
			),
			'thumbsWidth' => array(
				'required' => false,
				'name' => tra('Thumbs div width'),
				'description' => tra('Width in pixels or percentage.'),
				'filter' => 'striptags',
				'accepted' => 'Number of pixels followed by \'px\' or percent followed by % (e.g. "200px" or "100%").',
				'default' => '300px',
			),
			'imgWidth' => array(
				'required' => false,
				'name' => tra('Image slideshow width'),
				'description' => tra('Width in pixels or percentage of the largest image.'),
				'filter' => 'striptags',
				'accepted' => 'Number of pixels followed by \'px\' or percent followed by % (e.g. "200px" or "100%").',
				'default' => '550px',
			),
			'imgHeight' => array(
				'required' => false,
				'name' => tra('Image slideshow height'),
				'description' => tra('Height in pixels or percentage of the largest images.'),
				'filter' => 'striptags',
				'accepted' => 'Number of pixels followed by \'px\' or percent followed by % (e.g. "200px" or "100%").',
				'default' => '502px',
			),
		),
	);
}

function wikiplugin_galleriffic($data, $params)
{
	global $smarty;
	static $igalleriffic = 0;
	$smarty->assign('igalleriffic', $igalleriffic++);
	$plugininfo = wikiplugin_galleriffic_info();
	foreach ($plugininfo['params'] as $key => $param) {
		$default["$key"] = $param['default'];
	}
	$params = array_merge($default, $params);
	extract($params, EXTR_SKIP);

    $filegallib = TikiLib::lib('filegal');
	$files = $filegallib->get_files(0, -1, $params['sort_mode'], '', $params['fgalId']);
	if (empty($files['cant'])) {
		return '';
	}
	$headerlib = TikiLib::lib('header');
	$headerlib->add_cssfile('lib/jquery/galleriffic/css/galleriffic-2.css');
	$headerlib->add_jsfile('lib/jquery/galleriffic/js/jquery.galleriffic.js');
	$headerlib->add_jsfile('lib/jquery/galleriffic/js/jquery.opacityrollover.js');
	$playLinkText = tra('Play Slideshow');
	$pauseLinkText = tra('Pause SlideShow');
	$prevLinkText = '&lsaquo; '.tra('Previous Photo');
	$nextLinkText = tra('Next Photo').' &rsaquo;';
	$nextPageLinkText = tra('Next').' &rsaquo;';
	$prevPageLinkText = '&lsaquo; '.tra('Prev');
$jq = <<<JQ
	// We only want these styles applied when javascript is enabled
\$('div.navigation').css({'width' : '$thumbsWidth', 'float' : 'left'});
\$('div.gcontent').css('display', 'block');

	// Initially set opacity on thumbs and add
	// additional styling for hover effect on thumbs
	var onMouseOutOpacity = 0.67;
	\$('#thumbs ul.thumbs li').opacityrollover({
		mouseOutOpacity:   onMouseOutOpacity,
		mouseOverOpacity:  1.0,
		fadeSpeed:         'fast',
		exemptionSelector: '.selected'
	});
    var gallery = \$('#thumbs').galleriffic({
        delay:                     2500, // in milliseconds
        numThumbs:                 15, // The number of thumbnails to show page
        preloadAhead:              10, // Set to -1 to preload all images
        enableTopPager:            true,
        enableBottomPager:         true,
        maxPagesToShow:            7,  // The maximum number of pages to display in either the top or bottom pager
        imageContainerSel:         '#slideshow', // The CSS selector for the element within which the main slideshow image should be rendered
        controlsContainerSel:      '#controls', // The CSS selector for the element within which the slideshow controls should be rendered
        captionContainerSel:       '#caption', // The CSS selector for the element within which the captions should be rendered
        loadingContainerSel:       '#loading', // The CSS selector for the element within which should be shown when an image is loading
        renderSSControls:          true, // Specifies whether the slideshow's Play and Pause links should be rendered
        renderNavControls:         true, // Specifies whether the slideshow's Next and Previous links should be rendered
		playLinkText:              '$playLinkText',
		pauseLinkText:             '$pauseLinkText',
		prevLinkText:              '$prevLinkText',
		nextLinkText:              '$nextLinkText',
		nextPageLinkText:          '$nextPageLinkText',
		prevPageLinkText:          '$prevPageLinkText',
        enableHistory:             false, // Specifies whether the url's hash and the browser's history cache should update when the current slideshow image changes
        enableKeyboardNavigation:  true, // Specifies whether keyboard navigation is enabled
        autoStart:                 false, // Specifies whether the slideshow should be playing or paused when the page first loads
        syncTransitions:           false, // Specifies whether the out and in transitions occur simultaneously or distinctly
        defaultTransitionDuration: 1000, // If using the default transitions, specifies the duration of the transitions
    });
\$('div.gcontent').css({'width' : '$imgWidth'});
\$('div.loader').css({'width' : '$imgWidth', 'height' : '$imgHeight'});
\$('div.slideshow a.advance-link').css({'width' : '$imgWidth', 'height' : '$imgHeight', 'line-height' : '$imgHeight'});
\$('div.span.image-caption').css({'width' : '$imgWidth'});
\$('div.slideshow-container').css({'height' : '$imgHeight'});
JQ;

	$headerlib->add_jq_onready($jq);
	$smarty->assign('images', $files['data']);
	$smarty->assign('imgWidth', $imgWidth-50);// arbritary number to allow some padding
	return $smarty->fetch('wiki-plugins/wikiplugin_galleriffic.tpl');
}
