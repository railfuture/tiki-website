<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: shell.php 41306 2012-05-03 18:13:07Z jonnybradley $

if ( isset($_SERVER['REQUEST_METHOD']) ) die;

if ( !isset( $_SERVER['argv'][1] ) || !in_array($_SERVER['argv'][1], array('install')))
	die( 'Usage: php lib/profilelib/shell.php command [option]
	Where command [option] can be:
		install profile_name [repository=profiles.tiki.org]
' );

if ( ! file_exists('db/local.php') )
	die( "Tiki is not installed yet.\n" );

require_once('tiki-setup.php');

include_once 'lib/core/Zend/Log/Writer/Syslog.php';
$log_level = Zend_Log::INFO;
$writer = new Zend_Log_Writer_Stream('php://output');
$writer->addFilter((int) $log_level);
$logger = new Zend_Log($writer);

$logger->debug('Running search shell utility');

require_once 'lib/profilelib/profilelib.php';
require_once 'lib/profilelib/installlib.php';

if ( $_SERVER['argv'][1] === 'install' ) {
	$args = $_SERVER['argv'];
	$script = array_shift($args);
	$command = array_shift($args);
	$profile = array_shift($args);
	$repository = array_shift($args);

	if (! $repository) {
		$repository = 'profiles.tiki.org';
	}

	if (! $profile) {
		$logger->err('Profile not specified.');
		exit(1);
	}

	$profile = Tiki_Profile::fromNames($repository, $profile);

	if (! $profile) {
		$logger->err('Profile not found');
		exit(1);
	}

	$transaction = $tikilib->begin();
	$installer = new Tiki_Profile_Installer;
	$installer->install($profile);
	$transaction->commit();
}
