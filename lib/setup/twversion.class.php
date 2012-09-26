<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: twversion.class.php 42769 2012-08-27 10:29:48Z changi67 $

// Should generally be instantiated from tiki-setup.php

class TWVersion
{
	var $branch;		// Development cycle
	var $version;		// This version
	private $latestMinorRelease;		// Latest release in the same major version release series
	var $latestRelease;		// Latest release
	private $isLatestMajorVersion; // Whether or not the current major version is the latest
	var $releases;		// Array of all releases from website
	var $star;			// Star being used for this version tree
	var $svn;			// Is this a Subversion version or a package?

	function TWVersion()
	{
		// Set the development branch.  Valid are:
		//   stable   : Represents stable releases.
		//   unstable : Represents candidate and test/development releases.
		//   trunk     : Represents next generation development version.
		$this->branch 	= 'stable';

		// Set everything else, including defaults.
		$this->version 	= '9.1';	// needs to have no spaces for releases
		$this->star	= 'Herbig Haro';
		$this->releases	= array();

		// Check for Subversion or not
		$this->svn	= is_dir('.svn') ? 'y' : 'n';
	}

	// Returns the latest minor release in the same major version release series.
	function getLatestMinorRelease()
	{
		$this->pollVersion();
		return $this->latestMinorRelease;
	}

	function getBaseVersion()
	{
		return preg_replace("/^(\d+\.\d+).*$/", '$1', $this->version);
	}

	// Returns an array of all used Tiki stars.
	function tikiStars()
	{
		return array(
				1=>'Spica',			// 0.9
				2=>'Shaula',		// 0.95
				3=>'Ras Algheti',	// 1.0.x
				4=>'Capella',		// 1.1.x
				5=>'Antares',		// 1.2.x
				6=>'Pollux',		// 1.3.x
				7=>'Mira',			// 1.4.x
				8=>'Regulus',		// 1.5.x
				9=>'Tau Ceti',		// 1.6.x
				10=>'Era Carinae',	// 1.7.x
				11=>'Polaris',		// 1.8.x
				12=>'Sirius',		// 1.9.x
				13=>'Arcturus',		// 2.x
				14=>'Betelgeuse',	// 3.x
				15=>'Aldebaran',	// 4.x
				16=>'Vulpeculae',	// 5.x
				17=>'Rigel',		// 6.x
				18=>'Electra',		// 7.x
				19=>'Acubens',		// 8.x
				20=>'Herbig Haro'	// 9.x
		);
	}

	// Returns an array of all valid versions of Tiki.
	function tikiVersions()
	{
		// These are all the valid release versions of Tiki.
		// Newest version goes at the end.
		// Release Managers should update this array before
		// release.
		return array(
				1=>'1.9.1',
				'1.9.1.1',
				'1.9.2',
				'1.9.3.1',
				'1.9.3.2',
				'1.9.4',
				'1.9.5',
				'1.9.6',
				'1.9.7',
				'1.9.8',
				'1.9.8.1',
				'1.9.8.2',
				'1.9.8.3',
				'1.9.9',
				'1.9.10',
				'1.9.10.1',
				'1.9.11',
				'2.0',
				'2.1',
				'2.2',
				'2.3',
				'2.4',
				'3.0beta1',
				'3.0beta2',
				'3.0beta3',
				'3.0beta4',
				'3.0rc1',
				'3.0rc2',
				'3.0',
				'3.1',
				'3.2',
				'3.3',
				'3.4',
				'3.5',
				'3.6',
				'3.7',
				'3.8',
				'4.0beta1',
				'4.0RC1',
				'4.0',
				'4.1',
				'4.2',
				'4.3',
				'5.0alpha',
				'5.0beta1',
				'5.0beta2',
				'5.0RC1',
				'5.0RC2',
				'5.0',
				'5.1RC1',
				'5.1',
				'5.2',
				'5.3',
				'6.0beta1',
				'6.0beta2',
				'6.0beta3',
				'6.0RC1',
				'6.0',
				'6.1alpha1',
				'6.1beta1',
				'6.1beta2',
				'6.1RC1',
				'6.1',
				'6.2',
				'6.3',
				'6.4',
				'6.5',
				'6.6',
				'6.7',
				'7.0beta1',
				'7.0beta2',
				'7.0RC1',
				'7.0',
				'7.1RC1',
				'7.1RC2',
				'7.1',
				'7.2',
				'7.3',
				'8.0beta',
				'8.0RC1',
				'8.0',
				'8.1',
				'8.2',
				'8.3',
				'8.4',
				'8.5',
				'9.0alpha',
				'9.0beta',
				'9.0beta2',
				'9.0',
				'9.1',
			);
	}

	// Gets the latest star used by Tiki.
	function getStar()
	{
		$stars = $this->tikiStars();
		$star = $stars[count($stars)];

		return $star;
	}

	// Determines the currently-running version of Tikiwiki.
	function getVersion()
	{
		return $this->version;
	}

	// Pulls the list of releases in the current branch of Tikiwiki from
	// a central site.
	private function pollVersion()
	{
		static $done = false;
		if ($done) {
			return;
		}
		global $tikilib;
		$upgrade = 0;
		$major = 0;
		$velements = explode('.', $this->getBaseVersion());
		// .version contains an ordered list of release numbers, one per line. All minor releases from a same major release are grouped.
		$body = $tikilib->httprequest('tiki.org/' . $this->branch . '.version');
		$lines = explode("\n", $body);
		$this->isLatestMajorVersion = true;

		foreach ($lines as $line) {
			$relements = explode('.', $line);
			if (isset($relements[0]) && is_numeric($relements[0])) { // Avoid issues with empty lines
				$line = rtrim($line);
				$count = array_push($this->releases, $line);
				if ($relements[0] == $velements[0]) {
					$this->latestMinorRelease = $line;
				} elseif ($relements[0] > $velements[0]) {
					$this->isLatestMajorVersion = false;
				}
				$this->latestRelease = $line;
			}
		}
		$done = true;
	}

	// Returns true if the current major version is the latest, false otherwise.
	function isLatestMajorVersion()
	{
		$this->pollVersion();
		return $this->isLatestMajorVersion;
	}

	// Returns true if the current version is the latest in its major version release series, false otherwise.
	function isLatestMinorRelease()
	{
		$this->pollVersion();
		return $this->latestMinorRelease == $this->version || version_compare($this->version, $this->latestRelease) == 1;
	}
}
