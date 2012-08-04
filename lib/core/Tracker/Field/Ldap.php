<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Ldap.php 39469 2012-01-12 21:13:48Z changi67 $

/**
 * Handler class for LDAP. Was not extensively tested after migration.
 * 
 * Letter key: ~P~
 *
 */
class Tracker_Field_Ldap extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return array(
			'P' => array(
				'name' => tr('LDAP'),
				'description' => tr('Display a field value from a specific user in LDAP'),
				'readonly' => true,
				'help' => 'LDAP Tracker Field',					
				'prefs' => array('trackerfield_ldap'),
				'tags' => array('advanced'),
				'default' => 'n',
				'params' => array(
					'filter' => array(
						'name' => tr('Filter'),
						'description' => tr('LDAP filter, can contain the %field_name% placeholder to be replaced with the current field\'s name'),
						'example' => '(&(mail=%field_name%)(objectclass=posixaccount))',
						'filter' => 'none',
					),
					'field' => array(
						'name' => tr('Field'),
						'description' => tr('Field name returned by LDAP'),
						'filter' => 'text',
					),
					'dsn' => array(
						'name' => tr('DSN'),
						'description' => tr('Data source name registered in Tiki'),
						'filter' => 'text',
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		if ($this->getOption(2)) {
			$adminlib = TikiLib::lib('admin');
			$ldaplib = TikiLib::lib('ldap');

			// Retrieve DSN
			$info_ldap = $adminlib->get_dsn_from_name($this->getOption(2));

			if ($info_ldap) {
				$ldap_filter = $this->getOption(0);

				// Replace %field_name% by real value
				preg_match('/%([^%]+)%/', $ldap_filter, $ldap_filter_field_names);

				if (isset($ldap_filter_field_names[1])) {
					$field = $this->getTrackerDefinition()->getFieldFromName($ldap_filter_field_names[1]);

					if ($field) {
						$value = TikiLib::lib('trk')->get_field_value($field, $this->getItemData());

						$ldap_filter = preg_replace('/%'. $ldap_filter_field_names[1] .'%/', $value, $ldap_filter);

						// Get LDAP field value
						return array(
							'value' => $ldaplib->get_field($info_ldap['dsn'], $ldap_filter, $this->getOption(1)),
						);
					}
				}
			}
		}
	}

	function renderInput($context = array())
	{
		return $this->getConfiguration('value');
	}
}

