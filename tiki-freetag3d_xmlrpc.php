<?php 
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-freetag3d_xmlrpc.php 40234 2012-03-17 19:17:41Z changi67 $

include_once('tiki-setup.php');
require_once("XML/Server.php");
require_once("lib/freetag/freetaglib.php");

$access->check_feature('feature_freetags', 'freetags_feature_3d');

$map = array ("getSubGraph" => array( "function" => "getSubGraph" ) );

$server = new XML_RPC_Server($map);

function getSubGraph($params) 
{
    global $freetaglib, $dbTiki, $base_url, $prefs;

    $nodeName = $params->getParam(0); $nodeName = $nodeName->scalarVal();
    $depth = $params->getParam(1); $depth = $depth->scalarVal();

    $nodes = array();

    $passed = array($nodeName => true);
    $queue = array($nodeName);
    $i = 0;

    $tikilib = new TikiLib;
    $color = $prefs['freetags_3d_existing_page_color'];

    while ($i <= $depth && count($queue) > 0) {
	$nextQueue = array();
	foreach ($queue as $nodeName) {

	    $similar = $freetaglib->similar_tags($nodeName, 5);
	    $neighbours = array();
	    foreach ($similar as $tag) {
		$neighbours[] = $tag['tag'];
	    }
	    
	    $temp_max = count($neighbours);
	    for ($j = 0; $j < $temp_max; $j++) {
		if (!isset($passed[$neighbours[$j]])) {
		    $nextQueue[] = $neighbours[$j];
		    $passed[$neighbours[$j]] = true;
		}
		$neighbours[$j] = new XML_RPC_Value($neighbours[$j]);
	    }

	    $node = array();

	    $actionUrl = $base_url.'tiki-browse_freetags.php?tag='.$nodeName;

	    $node['neighbours'] = new XML_RPC_Value($neighbours, "array");
	    if (!empty($color)) {
		$node['color'] = new XML_RPC_Value($color, "string");
	    }
	    $node['actionUrl'] = new XML_RPC_Value($actionUrl, "string");

	    $nodes[$nodeName] = new XML_RPC_Value($node, "struct");

	}
	$i++;
	$queue = $nextQueue;
    }

    $response = array("graph" => new XML_RPC_Value($nodes, "struct"));
    
    return new XML_RPC_Response(new XML_RPC_Value($response, "struct"));
}
