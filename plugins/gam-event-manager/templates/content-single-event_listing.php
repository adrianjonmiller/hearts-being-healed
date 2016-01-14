<div class="single_event_listing" itemscope itemtype="http://schema.org/EventPosting">
	<meta itemprop="title" content="<?php echo esc_attr( $post->post_title ); ?>" />
	
    <!-- Main if condition start -->
	<?php if ( get_option( 'event_manager_hide_expired_content', 1 ) && 'expired' === $post->post_status ) : ?>
		<div class="event-manager-info"><?php _e( 'This listing has expired.', 'gam-event-manager' ); ?></div>
	<?php else : ?>
		<?php
			/**
			 * single_event_listing_start hook
			 *	
			 */
			 
			do_action( 'single_event_listing_start' );
			 
			 do_action( 'set_single_listing_view_count', $post);
		?>
		
		<div class="col-md-12">
                             
            <!-- Event description column start -->                             
            <div class="col-md-8 text-justify"> 
              <div class="event-details">
                <h3 class="section-title"><?php _e( 'Event Overview', 'gam-event-manager' ); ?></h3>  
                <img class="img-responsive img-center event-banner" src="<?php echo get_event_banner();?>" />                              
                <?php echo apply_filters( 'display_event_description', get_the_content() ); ?>
                
              </div>
            </div>                             
            <!-- Event description column end -->
                                                    
           <!-- Organizer logo, Contact button, event location, time and social sharing column start -->                       
            <div class="col-md-4 text-justify ">                            
                <?php  get_event_manager_template_part( 'content', 'single-event_listing-organizer' ); ?>
            </div>  <!-- col-md-4 --> 
           <!-- Organizer logo, Contact button, event location, time and social sharing column end -->
                 
	 	</div>
	 	
	<?php
    /**
     * single_event_listing_end hook
     */
    	do_action( 'single_event_listing_end' );
    ?>
  <?php endif; ?><!-- Main if condition end -->
</div>

