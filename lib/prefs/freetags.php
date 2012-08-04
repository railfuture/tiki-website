<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: freetags.php 39469 2012-01-12 21:13:48Z changi67 $

function prefs_freetags_list()
{
	return array (
			'freetags_multilingual' => array(
			'name' => tra('Multilingual tags'),
			'description' => tra('Permits translation management of tags'),
			'help' => 'Tags',
			'type' => 'flag',
			'dependencies' => array(
				'feature_multilingual',
				'feature_freetags',
			),
			'default' => 'n',
		),
		'freetags_browse_show_cloud' => array(
			'name' => tra('Show tag cloud'),
			'type' => 'flag',
			'default' => 'y',
		),
		'freetags_browse_amount_tags_in_cloud' => array(
			'name' => tra('Maximum number of tags in cloud'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
			'default' => '100',
		),
		'freetags_show_middle' => array(
			'name' => tra('Show freetags in middle column'),
			'type' => 'flag',
			'default' => 'y',
		),
		'freetags_preload_random_search' => array(
			'name' => tra('Preload freetag random tag'),
			'type' => 'flag',
			'default' => 'y',
		),
		'freetags_browse_amount_tags_suggestion' => array(
			'name' => tra('Number of Tags to show in Tag Suggestions'),
			'type' => 'text',
			'size' => '4',
			'filter' => 'digits',
			'default' => '10',
		),
		'freetags_normalized_valid_chars' => array(
			'name' => tra('Valid characters pattern'),
			'type' => 'text',
			'size' => '30',
			'default' => '',
		),
		'freetags_lowercase_only' => array(
			'name' => tra('Lowercase tags only'),
			'type' => 'flag',
			'default' => 'y',
		),
		'freetags_feature_3d' => array(
			'name' => tra('Enable freetags 3D browser'),
			'type' => 'flag',
			'default' => 'n',
		),
		'freetags_3d_autoload' => array(
			'name' => tra('3D autoload'),
			'type' => 'flag',
			'default' => 'false',
		),
		'freetags_3d_width' => array(
			'name' => tra('Browser width'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
			'default' => 500,
		),
		'freetags_3d_height' => array(
			'name' => tra('Browser height'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
			'default' => 500,
		),
		'freetags_3d_navigation_depth' => array(
			'name' => tra('Navigation depth'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
			'default' => 1,
		),
		'freetags_3d_node_size' => array(
			'name' => tra('Node size'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
			'default' => '30',
		),
		'freetags_3d_text_size' => array(
			'name' => tra('Text size'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
			'default' => '40',
		),
		'freetags_3d_spring_size' => array(
			'name' => tra('Spring (connection) size'),
			'type' => 'text',
			'size' => '3',
			'filter' => 'digits',
			'default' => '100',
		),
		'freetags_3d_existing_page_color' => array(
			'name' => tra('Existing page node color'),
			'type' => 'text',
			'size' => '10',
			'default' => '#00CC55',
		),
		'freetags_3d_missing_page_color' => array(
			'name' => tra('Missing page node color'),
			'type' => 'text',
			'size' => '10',
			'default' => '#FF5555',
		),
		'freetags_3d_adjust_camera' => array(
			'name' => tra('Camera distance adjusted relative to nearest node'),
			'type' => 'flag',
			'default' => 'false',
		),
		'freetags_3d_camera_distance' => array(
			'name' => tra('Camera distance'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
			'default' => '200',
		),
		'freetags_3d_fov' => array(
			'name' => tra('Field of view'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
			'default' => '250',
		),
		'freetags_3d_feed_animation_interval' => array(
			'name' => tra('Feed animation interval (milisecs)'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
			'default' => 500,
		),
		'freetags_3d_friction_constant' => array(
			'name' => tra('Friction constant'),
			'type' => 'text',
			'size' => '5',
			'default' => '0.4f',
		),
		'freetags_3d_elastic_constant' => array(
			'name' => tra('Elastic constant'),
			'type' => 'text',
			'size' => '5',
			'default' => '0.5f',
		),
		'freetags_3d_eletrostatic_constant' => array(
			'name' => tra('Electrostatic constant'),
			'type' => 'text',
			'size' => '5',
			'default' => '1000f',
		),
		'freetags_3d_node_mass' => array(
			'name' => tra('Node mass'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
			'default' => '5',
		),
		'freetags_3d_node_charge' => array(
			'name' => tra('Node charge'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
			'default' => '1',
		),
	);
}
