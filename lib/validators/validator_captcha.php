<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: validator_captcha.php 39469 2012-01-12 21:13:48Z changi67 $

function validator_captcha($input, $parameter = '', $message = '')
{
	global $prefs, $captchalib;
	$_REQUEST['captcha'] = array('input' => $input, 'id' => $parameter);
	if (!$captchalib->validate()) {
		// the following needed to keep session active for ajax checking 
		$session = $captchalib->captcha->getSession();
		$session->setExpirationHops(2, null, true);
		$captchalib->captcha->setSession($session);
		$captchalib->captcha->setKeepSession(false);
		// now return errors
		return $captchalib->getErrors();
	}
	// the following needed to keep session active for ajax checking 
	$session = $captchalib->captcha->getSession();
	$session->setExpirationHops(2, null, true);
	$captchalib->captcha->setSession($session);
	$captchalib->captcha->setKeepSession(false);
	// now return ok
	return true;
}
