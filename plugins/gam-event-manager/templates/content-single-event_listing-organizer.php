<?php/** * Single view organizer information box * * Hooked into single_event_listing_start priority 30 * * @since  1.0.0 */if ( ! get_organizer_name() ) {	return;}?><div class="organizer-details" itemscope itemtype="http://data-vocabulary.org/Organization">                                                                <!-- Organizer logo section start -->                               <div class="text-center">                                                                          <?php do_action( 'event_single_organizer_logo_start' ); ?>                                        <?php display_organizer_logo(); ?>                                      <?php do_action( 'event_single_organizer_logo_end' ); ?>                               </div>                               <!-- Organizer logo section end  -->                                                              <!-- Organizer description start -->                              		    	   	            <h3  class="section-title"><?php _e( 'About', 'gam-event-manager' ); ?> <?php echo display_organizer_name(); ?></h3>                                                     	    <div class="text-justify"><?php echo get_organizer_description(); ?></div>                               <!-- Organizer description end-->                                                              <?php if ( attendees_can_apply() ) : ?>                        			<?php get_event_manager_template( 'event-registration.php' ); ?>                        	   <?php endif; ?>                                                              <?php do_action( 'single_event_listing_button_start' ); ?>                                                                   <!-- For more details information start -->                                     <?php if(get_event_link_to_eventpage()): ?>                                         <div class="text-center">                                            <a class="link-button" data-toggle="modal" href="<?php echo  get_event_link_to_eventpage(); ?>"  target="_blank" ><?php _e( 'View More Details', 'gam-event-manager' );?></a>                                          </div>                                      <?php endif; ?>                                                                          <!-- For more details infomation end-->                                                                          <!-- Video popup box section start -->                                     <?php if( get_organizer_video()):?>                                                                                 <div class="text-center">                                                  <a id="event-watch-video-button" class="link-button" data-toggle="modal" ><?php _e( 'Watch Video', 'gam-event-manager' );?></a>                                            </div>                                                                                         <div class="modal fade" id="watch-video-modal" tabindex="-1" role="dialog" aria-labelledby="organizer-watch-video-modal-dialog" aria-hidden="true">                                              <div class="modal-dialog">						                        <div class="modal-content">                                                  <div class="modal-header">                                                      <span class="close" data-dismiss="modal" aria-label="Close" aria-hidden="true">&times;</span>                                                      <h4 class="modal-title" id="organizer-watch-video-modal-dialog"><?php _e( 'Watch Video', 'gam-event-manager' );?></h4>                                                   </div>                                                   <div class="modal-body">                                                      <?php echo wp_oembed_get(display_organizer_video() , array( 'autoplay' => 1, 'rel' => 0) );?>                                                   </div>                                                  </div>                                                </div>                                              </div>                                                                                 <?php endif; ?>                                    <!-- Video popup box section end -->                                                             <?php do_action( 'single_event_listing_button_end' ); ?>                                                                       <!-- Event Ticket start-->                                         <h3 class="section-title"><?php _e( 'Ticket Information', 'gam-event-manager' );?></h3>                                       <ul class="when-where">                                          <li class="event-ticket">#<?php echo get_event_ticket_option();?></li>                                       </ul>                                     <!--Event Ticket End-->                                                                                                                 <!-- Event Location and Time start-->                                                                                <h3 class="section-title"><?php _e( 'When & Where', 'gam-event-manager' );?></h3>                                          <ul class="when-where">                                            <li class="event-start-date">                                                                                        From:<?php $timestamp = strtotime(get_event_start_date()); if($timestamp!=null):echo date("M j, Y",$timestamp).','.date("g:i a",$timestamp); endif;?>                                            </li>                                            <li class="event-end-date">                                                                                          To:<?php $timestamp = strtotime(get_event_end_date());  if($timestamp!=null): echo date("M j, Y",$timestamp).','.date("g:i a",$timestamp);  endif; ?>                                            </li>                    					    <li class="event-location">					                        					     Location:                    					     <?php if(get_event_location($post)=='Anywhere' || get_event_location($post) == '' ):                     						      echo 'Online Event';                     					      else:                    					          echo '<strong>' . get_event_venue_name() . '</strong><br><p class="text-center">'. get_event_address(). ', ' . get_event_pincode() .', '. get_event_location() . '.</p>';                     					      endif;?>                    					    </li>					                     </ul>                                     					                <!-- Event Location and Time End-->                                                                              <!-- Event Registration End Date start-->                     <?php if(get_event_registration_end_date()): ?>                                                                          <div>                                <h3 class="section-title"><?php _e( 'Registration End Date', 'gam-event-manager' );?></h3>                                 <ul class="when-where">                                    <li class="registration-end-date">                                                                                <?php $timestamp = strtotime(get_event_registration_end_date()); if($timestamp!=null): echo date("M j, Y",$timestamp).','.date("g:i a",$timestamp); endif; ?>                                     </li>                                  </ul>                              </div>                                                                    <?php endif; ?>                      <!-- Registration End Date End-->                                               <!-- Organizer website and social networks links section start -->	                        <?php                         $websiteurl= get_organizer_website();			                               $facebook= get_organizer_facebook();			            $twitter= get_organizer_twitter();			                                  $linkedin=get_organizer_linkedin();                        $xing=get_organizer_xing();                        $pinterest=get_organizer_pinterest();                        $instagram=get_organizer_instagram();                        $youtube=get_organizer_youtube();                        $googleplus=get_organizer_google_plus(); 	                        if(   $websiteurl||                               $facebook  ||                               $twitter   ||                               $linkedin  ||                               $xing      ||                               $pinterest ||                               $instagram ||                               $youtube   ||                               $googleplus )                         {  ?>                                               	 <div>                             	<h3 class="section-title"><?php _e( 'Organizer Social', 'gam-event-manager' );?></h3>                                <ul class="organizer-social">                                <!-- Organizer website section start -->                                 <?php if($websiteurl) { ?>                                 	<li><a href=" <?php echo $websiteurl; ?>" class="website-link" target="_blank"><?php _e( 'Website', 'gam-event-manager' );?></a></li>                                 <?php  } ?>			                                    <!-- Organizer website section end -->                                <!-- Organizer facebook section start -->                                <?php if($facebook) { ?>                                	<li><a href=" <?php echo $facebook; ?>"  class="fb-link" target="_blank"><?php _e( 'Facebook', 'gam-event-manager' );?></a></li>                                 <?php } ?>			                                     <!-- Organizer facebook section end -->                                                                                                     <!-- Organizer twitter section start -->                                 <?php if($twitter) { ?>                                 	<li><a href=" <?php echo $twitter; ?>"  class="twitter-link"  target="_blank"><?php _e( 'Twitter', 'gam-event-manager' );?></a></li>                                 <?php } ?>                                 <!-- Organizer twitter section end -->                                                                                                     <!-- Organizer linkedin section start -->                                 <?php if($linkedin) { ?>                                 	<li><a href=" <?php echo $linkedin; ?>" class="linkedin-link" target="_blank"><?php _e( 'Linkedin', 'gam-event-manager' );?></a></li>                                 <?php } ?>			                                     <!-- Organizer linkedin section end -->                                                                             <!-- Organizer xing section start -->                                 <?php if($xing) { ?>                                 	<li><a href=" <?php echo $xing; ?>"  class="xing-link" target="_blank"><?php _e( 'Xing', 'gam-event-manager' );?></a></li>                                 <?php } ?>			                                     <!-- Organizer xing section end -->                                                                              <!-- Organizer pinterest section start -->                                  <?php if($pinterest) { ?>                                  	<li><a href=" <?php echo $pinterest; ?>" class="pinterest-link" target="_blank"><?php _e( 'Pinterest', 'gam-event-manager' );?></a></li>                                  <?php } ?>			                                     <!-- Organizer pinterest section end -->                                                                             <!-- Organizer instagram section start -->                                 <?php if($instagram) { ?>                                 	<li><a href=" <?php echo $instagram; ?>"  class="instagram-link" target="_blank"><?php _e( 'Instagram', 'gam-event-manager' );?></a></li>                                 <?php } ?>			                                     <!-- Organizer instagram section end -->                                                                             <!-- Organizer youtube section start -->                                 <?php if($youtube) { ?>                                 	<li><a href=" <?php echo $youtube; ?>"  class="youtube-link" target="_blank"><?php _e( 'Youtube', 'gam-event-manager' );?></a></li>                                 <?php } ?>			                                     <!-- Organizer youtube section end -->                                                                             <!-- Organizer googleplus section start -->                                 <?php if($googleplus) { ?>                                 	<li><a href=" <?php echo $googleplus; ?>"  class="gplus-link" target="_blank"><?php _e( 'Google', 'gam-event-manager' );?></a></li></li>                                <?php } ?>	                                <!-- Organizer googleplus section end -->			                          	 <?php  } ?>				    	                     	</ul>         		<div>               <!-- Organizer website and social networks links section end -->                                                              <!-- Social sharing section start -->               <div>                                               		                   		<h3 class="section-title"><?php _e( 'Share This Event', 'gam-event-manager' );?></h3>               		<ul class="social-share">                    	<li><a href="http://www.facebook.com/sharer/sharer.php?u=<?php display_event_permalink(); ?>" class="fb-icon" target="_blank"></a></li>                        <li><a href="http://twitter.com/share?text=twitter&url=<?php display_event_permalink(); ?>" class="twitter-icon"  target="_blank"></a></li>                        <li><a href="https://www.linkedin.com/shareArticle?mini=true&url=&title=&summary=&source=<?php display_event_permalink(); ?>" class="linkedin-icon" target="_blank"></a></li>	                        <li><a href="https://www.xing.com/spi/shares/new?url=<?php display_event_permalink(); ?>" class="xing-icon" target="_blank"></a></li>									                        <li><a href="https://plus.google.com/share?url=<?php display_event_permalink(); ?>" class="gplus-icon" target="_blank"></a></li>								                          <li><a href="https://pinterest.com/pin/create/button/?url=<?php display_event_permalink(); ?>" class="pinterest-icon" target="_blank"></a></li>  								                      </ul>              </div>              <!-- Social sharing section end -->                 </div>  <!-- organizer-details end -->   <script>jQuery(document).ready(function($) {      //stop youtube after closing bootstrap dialog   $('#watch-video-modal').on('hidden.bs.modal', function ()    {    	     $("#watch-video-modal iframe").attr("src", $("#watch-video-modal iframe").attr("src"));   });        jQuery("#event-watch-video-button").click(function(){       	 jQuery('#watch-video-modal').modal('show');   });   });</script>