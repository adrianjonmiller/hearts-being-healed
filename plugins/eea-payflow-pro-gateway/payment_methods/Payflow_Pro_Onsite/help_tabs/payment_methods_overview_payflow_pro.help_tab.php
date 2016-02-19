<h3>
	<?php _e( 'Payflow Pro Gateway', 'event_espresso' ); ?>
</h3>
<p>
	<?php printf( __('Settings Tips for the PayPal Payflow Pro Gateway. For more information please read %1$s Payflow Pro Documentation. %2$s'), '<a href="https://developer.paypal.com/docs/classic/products/payflow-gateway/">', '</a>' ) ?>
</p>
<h3>
	<?php _e( 'Payflow Pro Settings', 'event_espresso' ); ?>
</h3>
<ul>
	<li>
		<strong><?php _e( 'Vendor ID', 'event_espresso' ); ?></strong><br/>
		<?php _e( 'Case-sensitive Vendor ID that you created while registering for the account.', 'event_espresso' ); ?>
	</li>
	<li>
		<strong><?php _e( 'User ID', 'event_espresso' ); ?></strong><br/>
		<?php _e( 'Case-sensitive login ID for the Gateway account that you created while registering for the account.', 'event_espresso' ); ?><br/>
		<?php printf( __( '%1$s Note: %2$s If you set up one or more additional users on the account, this value is the ID of the user authorized to process transactions. If, however, you have not set up additional users on the account, %3$s USER has the same value as VENDOR. %4$s', 'event_espresso' ), '<strong>', '</strong>', '<strong>', '</strong>' ); ?>
	</li>
	<li>
		<strong><?php _e( 'Password', 'event_espresso' ); ?></strong><br/>
		<?php _e( 'The password that you defined while registering for the account.', 'event_espresso' ); ?>
	</li>
	<li>
		<strong><?php _e( 'Partner ID', 'event_espresso' ); ?></strong><br/>
		<?php _e( 'The ID provided to you by the authorized PayPal Reseller who registered you for the Gateway gateway. If you purchased your account directly from PayPal, use "PayPal".', 'event_espresso' ); ?>
	</li>
</ul>
<p>
	<strong><?php printf( __('Important note for Fraud Protection Services users', 'event_espresso' ) );?></strong><br/>
		<?php printf( __( 'If you are using Fraud Protecteion Services (FPS), it is recommended you set the default filter action to "Reject", not "Review", %1$ssimilar to what is shown here%2$s.', 'event_espresso' ), 
				'<a href="https://drive.google.com/file/d/0B2KCao4zFjaxSFNqclZ4WXlvQjg/view?usp=drivesdk" target="_blank">',
				'</a>' );?><br/>
		<?php printf( __( 'If you set the default filter action to "Review", every time you manually reject a transaction from the Payment Manager, you will also have to mark the payment as declined in Event Espresso because Payflow does not notify Event Espresso when this occurs. You can read more at %1$s Payflow Fraud Protection Services User\'s Guide %2$s'), 
				'<a href="https://developer.paypal.com/docs/classic/payflow/fraud-protection/" target="_blank">',
				'</a>'
				 ) ?>
</p>