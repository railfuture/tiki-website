{* $Id: include_payment.tpl 40917 2012-04-15 02:58:23Z marclaporte $ *}
<form action="tiki-admin.php?page=payment" method="post">
	<div class="navbar">
		{button href="tiki-payment.php" _text="{tr}Payments{/tr}"}
		<input type="submit" name="paymentprefs" value="{tr}Change settings{/tr}" style="float:right;" />
	</div>
	{if $prefs.payment_feature neq "y"}
		<fieldset class="admin">
			<legend>{tr}Activate the feature{/tr}</legend>
			{preference name=payment_feature visible="always"}
		</fieldset>
	{/if}
	{tabset}
		{tab name="{tr}Payment{/tr}"}
			{remarksbox title="{tr}Choose payment system{/tr}"}
				{tr}You can use only one payment method: PayPal or Cclite or Tiki User Credits{/tr}<br />
				{tr}PayPal is working at the moment. See PayPal.com{/tr}<br />
				{tr}Cclite: Community currency accounting for local exchange trading systems (LETS). See {/tr}<a href="http://sourceforge.net/projects/cclite/">{tr}sourceforge.net{/tr}</a><br />
				{tr}Tiki User Credits: Requires this other feature to be configured{/tr}
			{/remarksbox}

			<div class="adminoptionboxchild" id="payment_feature_childcontainer">
				<fieldset class="admin">
					{preference name=payment_system}
					{preference name=payment_currency}
					{preference name=payment_default_delay}
					{preference name=payment_manual}
					{preference name=payment_user_only_his_own}
					{preference name=payment_user_only_his_own_past}
				</fieldset>
				<div id="payment_systems">
					<h2>{tr}PayPal{/tr}</h2>
					<div class="admin payment">
						{preference name=payment_paypal_business}

						<div class="adminoptionboxchild">
							{preference name=payment_paypal_environment}
							{preference name=payment_paypal_ipn}
						</div>
						{preference name=payment_invoice_prefix}
					</div>
					<h2>{tr}Cclite{/tr}</h2>
					<div class="admin payment">
						{remarksbox title="{tr}Experimental{/tr}" type="warning" icon="bricks"}
							{tr}Cclite is for creating and managing alternative or complementary trading currencies and groups{/tr}
							{tr}Work in progress since Tiki 6{/tr}
						{/remarksbox}
						{preference name=payment_cclite_registries}
						{preference name=payment_cclite_currencies}
						<div class="adminoptionboxchild">
							{preference name=payment_cclite_gateway}
							{preference name=payment_cclite_merchant_user}
							{preference name=payment_cclite_merchant_key}
							{preference name=payment_cclite_mode}
							{preference name=payment_cclite_hashing_algorithm}
							{preference name=payment_cclite_notify}
						</div>
					</div>
					<h2>{tr}Tiki User Credits{/tr}</h2>
					<div class="admin payment">
						{preference name=payment_tikicredits_types}
						{preference name=payment_tikicredits_xcrates}
					</div>
				</div>
				{jq}if ($.ui) {
	var idx = $("select[name=payment_system]").prop("selectedIndex");
	$("#payment_systems").tiki("accordion", {heading: "h2"});
	if (idx > 0) { $("#payment_systems").accordion("option", "active", idx); }
}{/jq}
			</div>
		{/tab}
		{tab name="{tr}Advanced Shopping Cart{/tr}"}
			<fieldset>
				<legend>{tr}Advanced Cart Tracker Names Setup{/tr}</legend>
				{preference name=payment_cart_product_tracker_name}
				{preference name=payment_cart_orders_tracker_name}
				{preference name=payment_cart_orderitems_tracker_name}
				{preference name=payment_cart_productclasses_tracker_name}
			</fieldset>
			<fieldset>
				<legend>{tr}Products Tracker Setup{/tr}</legend>
				{remarksbox title="{tr}Choose payment system{/tr}"}
					{tr}Depending on which feature you are using, you may need some or all of the following fields to be setup{/tr}
				{/remarksbox}
				{preference name=payment_cart_product_tracker}
				{preference name=payment_cart_inventory_type_field}
				{preference name=payment_cart_inventory_total_field}
				{preference name=payment_cart_inventory_lesshold_field}
				{preference name=payment_cart_product_name_fieldname}
				{preference name=payment_cart_product_price_fieldname}
				{preference name=payment_cart_products_inbundle_fieldname}
				{preference name=payment_cart_associated_event_fieldname}
				{preference name=payment_cart_product_classid_fieldname}
				{preference name=payment_cart_giftcerttemplate_fieldname}
			</fieldset>
			<fieldset>
				<legend>{tr}Features{/tr}</legend>
				{preference name=payment_cart_inventory}
					<div class="adminoptionboxchild" id="payment_cart_inventory_childcontainer">
					{preference name=payment_cart_inventoryhold_expiry}
					</div>
				{preference name=payment_cart_bundles}
				{preference name=payment_cart_orders}
				<div class="adminoptionboxchild" id="payment_cart_orders_childcontainer">
					{preference name=payment_cart_orders_profile}
					{preference name=payment_cart_orderitems_profile}
				</div>
				{preference name=payment_cart_anonymous}
				<div class="adminoptionboxchild" id="payment_cart_anonymous_childcontainer">
					{preference name=payment_cart_anonorders_profile}
					{preference name=payment_cart_anonorderitems_profile}
					{preference name=payment_cart_anonshopper_profile}
					{preference name=payment_cart_anon_reviewpage}
					{preference name=payment_cart_anon_group}
				</div>
				{preference name=payment_cart_associatedevent}
				<div class="adminoptionboxchild" id="payment_cart_associatedevent_childcontainer">
					{preference name=payment_cart_event_tracker}
					{preference name=payment_cart_event_tracker_name}
					{preference name=payment_cart_eventstart_fieldname}
					{preference name=payment_cart_eventend_fieldname}
				</div>
				{preference name=payment_cart_exchange}
				<div class="adminoptionboxchild" id="payment_cart_exchange_childcontainer">
					{preference name=payment_cart_orderitems_tracker}
				</div>
				{preference name=payment_cart_giftcerts}
				<div class="adminoptionboxchild" id="payment_cart_giftcerts_childcontainer">
					{preference name=payment_cart_giftcert_tracker}
					{preference name=payment_cart_giftcert_tracker_name}
				</div>
			</fieldset>
		{/tab}

		{tab name="{tr}Plugins{/tr}"}

			<fieldset class="admin">
				<legend>{tr}Plugins{/tr}</legend>
				{preference name=wikiplugin_addtocart}
				{preference name=wikiplugin_adjustinventory}
				{preference name=wikiplugin_cartmissinguserinfo}
				{preference name=wikiplugin_extendcarthold}
				{preference name=wikiplugin_hasbought}
				{preference name=wikiplugin_memberpayment}
				{preference name=wikiplugin_payment}
				{preference name=wikiplugin_shopperinfo}
			</fieldset>

		{/tab}

		{tab name="{tr}Shipping{/tr}"}
			{preference name=shipping_service}

			{preference name=shipping_fedex_enable}
			<div class="adminoptionboxchild" id="shipping_fedex_enable_childcontainer">
				{preference name=shipping_fedex_key}
				{preference name=shipping_fedex_password}
				{preference name=shipping_fedex_account}
				{preference name=shipping_fedex_meter}
			</div>

			{preference name=shipping_ups_enable}
			<div class="adminoptionboxchild" id="shipping_ups_enable_childcontainer">
				{preference name=shipping_ups_license}
				{preference name=shipping_ups_username}
				{preference name=shipping_ups_password}
			</div>
				{preference name=shipping_custom_provider}
		{/tab}
	{/tabset}
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" name="paymentprefs" value="{tr}Change settings{/tr}" />
	</div>
</form>
