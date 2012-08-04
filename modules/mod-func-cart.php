<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-cart.php 39469 2012-01-12 21:13:48Z changi67 $

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_cart_info()
{
	return array(
		'name' => tra('Cart'),
		'description' => tra('Displays the content of the cart, allows to modify quantities and proceed to payment.'),
		'prefs' => array('payment_feature'),
	);
}

function module_cart($mod_reference, $module_params)
{
	global $smarty, $access;
	global $cartlib; require_once 'lib/payment/cartlib.php';

	if (isset($_POST['update'], $_POST['cart'])) {
		foreach ($_POST['cart'] as $code => $quantity) {
			$cartlib->update_quantity($code, $quantity);
		}

		$access->redirect($_SERVER['REQUEST_URI'], tra('The quantities in your cart were updated.'));
	}

	if (isset($_POST['checkout'])) {
		$invoice = $cartlib->request_payment();
	
		if ($invoice) {
			$access->redirect('tiki-payment.php?invoice=' . intval($invoice), tr('The order was recorded and is now awaiting payment. Reference number is %0.', $invoice));
		}
	}
	
	if ($cartlib->has_gift_certificate()) {
		if (!empty($_POST['gift_certificate_redeem_code'])) {
			$added = $cartlib->add_gift_certificate($_POST['gift_certificate_redeem_code']);
			if ($added) {
				$access->redirect($_SERVER['REQUEST_URI'], tra('Gift card added'));
			} else {
				$access->redirect($_SERVER['REQUEST_URI'], tra('Gift card not found'));
			}
		}
	
		if (isset($_POST['remove_gift_certificate'])) {
			$cartlib->add_gift_certificate();
			$access->redirect($_SERVER['REQUEST_URI'], tra('Gift card removed'));
		}
	
		$cartlib->get_gift_certificate();
	
		$smarty->assign('has_gift_certificate', true);
		$smarty->assign('gift_certificate_redeem_code', $cartlib->gift_certificate_code);
		$smarty->assign('gift_certificate_amount', $cartlib->gift_certificate_amount);
		$smarty->assign('gift_certificate_mode_symbol_before', $cartlib->gift_certificate_mode_symbol_before);
		$smarty->assign('gift_certificate_mode_symbol_after', $cartlib->gift_certificate_mode_symbol_after);
	}

	$smarty->assign('cart_total', $cartlib->get_total());
	$smarty->assign('cart_content', $cartlib->get_content());
}

