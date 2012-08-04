<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tikisession-adodb.php 39469 2012-01-12 21:13:48Z changi67 $

include('db/local.php');
$ADODB_SESSION_DRIVER=$db_tiki;
$ADODB_SESSION_CONNECT=$host_tiki;
$ADODB_SESSION_USER=$user_tiki;
$ADODB_SESSION_PWD=$pass_tiki;
$ADODB_SESSION_DB=$dbs_tiki;
unset($db_tiki);
unset($host_tiki);
unset($user_tiki);
unset($pass_tiki);
unset($dbs_tiki);
ini_set('session.save_handler', 'user');
include_once('lib/adodb/session/adodb-session.php');
