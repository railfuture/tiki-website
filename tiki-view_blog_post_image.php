<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-view_blog_post_image.php 39467 2012-01-12 19:47:28Z changi67 $

require_once ('tiki-setup.php');

$access->check_feature('feature_blogs');

include_once ('lib/blogs/bloglib.php');
if (!isset($_REQUEST['imgId'])) {
	die;
}
$info = $bloglib->get_post_image($_REQUEST['imgId']);
$type = & $info['filetype'];
$file = & $info['filename'];
$content = & $info['data'];
header("Content-type: $type");
header("Content-Disposition: inline; filename=$file");
echo $content;
