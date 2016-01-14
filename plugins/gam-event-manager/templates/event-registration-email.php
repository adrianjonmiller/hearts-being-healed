<p><?php printf( __( 'To register for this event <strong>email your details to</strong> <a class="event_registration_email" href="mailto:%1$s%2$s">%1$s</a>', 'gam-event-manager' ), $register->email, '?subject=' . rawurlencode( $register->subject ) ); ?></p>

<p>
	<?php _e( 'Register using webmail: ', 'gam-event-manager' ); ?>

	<a href="https://mail.google.com/mail/?view=cm&fs=1&to=<?php echo $register->email; ?>&su=<?php echo urlencode( $register->subject ); ?>" target="_blank" class="event_registration_email">Gmail</a> / 
	
	<a href="http://webmail.aol.com/Mail/ComposeMessage.aspx?to=<?php echo $register->email; ?>&subject=<?php echo urlencode( $register->subject ); ?>" target="_blank" class="event_registration_email">AOL</a> / 
	
	<a href="http://compose.mail.yahoo.com/?to=<?php echo $register->email; ?>&subject=<?php echo urlencode( $register->subject ); ?>" target="_blank" class="event_registration_email">Yahoo</a> / 
	
	<a href="http://mail.live.com/mail/EditMessageLight.aspx?n=&to=<?php echo $register->email; ?>&subject=<?php echo urlencode( $register->subject ); ?>" target="_blank" class="event_registration_email">Outlook</a>

</p>