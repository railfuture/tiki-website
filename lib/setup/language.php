<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: language.php 40057 2012-03-06 21:51:17Z pkdille $

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER['SCRIPT_NAME'], basename(__FILE__));

// Indicates whether a locale identifier is valid
// $localeIdentifier: A locale identifier, such as "en"
// Returns a boolean, true if and only if the given locale identifier is valid and allowed. 
function isValidLocale($localeIdentifier = '')
{
	global $prefs;
	return preg_match("/[a-zA-Z-_]+$/", $localeIdentifier) && file_exists('lang/'. $localeIdentifier .'/language.php')
		&& (empty($prefs['available_languages']) || in_array($localeIdentifier, $prefs['available_languages']));
}

// Sets the language
// $localeIdentifier: the identifier of the locale to set
// Returns true on success, false on failure (if $localeIdentifier is not a valid and allowed locale identifier) 
function setLanguage($localeIdentifier = '')
{
	global $prefs, $tikilib, $user;
	if (isValidLocale($localeIdentifier)) {
		return $tikilib->set_user_preference($user, 'language', $localeIdentifier);
	} else {
		return false;
	}	
}

if ($prefs['feature_multilingual'] != 'y') { // change_language depends on feature_multilingual.
	$prefs['change_language'] = 'n';
}

if ( $prefs['change_language'] == 'y') {
	// $noSwitchLang = true; // Uncomment to disable switchLang
	if (isset($_GET['switchLang']) && !isset($noSwitchLang)) {
		// Special feature to allow creating Tiki links that also permanently switch the language of the user following the link.
		// Tiki does not create such links. See http://doc.tiki.org/i18n+Admin#Goodies 
		setLanguage($_GET['switchLang']);
	} elseif ($prefs['feature_detect_language'] == 'y' and !$tikilib->userHasPreference('language')) {
		// Detect browser language
		$browser_language = detect_browser_language();
		if ( isValidLocale($browser_language) ) {
			$prefs['language'] = $browser_language;
		}
	}
}

if (!isValidLocale($prefs['language'])) {
	// Override broken user locales
	setLanguage($prefs['site_language']);
}

// Some languages need BiDi support. Add their code names here ...
$prefs['feature_bidi'] = in_array($prefs['language'], array('ar', 'he', 'fa')) ? 'y' : 'n';
