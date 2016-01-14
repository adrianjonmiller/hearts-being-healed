<?php
/**
 * GAM_Event_Manager_Form_Submit_Event class.
 */

class GAM_Event_Manager_Form_Submit_Event extends GAM_Event_Manager_Form {
    
	public    $form_name = 'submit-event';
	protected $event_id;
	protected $preview_event;
	/** @var GAM_Event_Manager_Form_Submit_Event The single instance of the class */
	protected static $_instance = null;
	/**
	 * Main Instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp', array( $this, 'process' ) );
		$this->steps  = (array) apply_filters( 'submit_event_steps', array(
			'submit' => array(
				'name'     => __( 'Submit Details', 'gam-event-manager' ),
				'view'     => array( $this, 'submit' ),
				'handler'  => array( $this, 'submit_handler' ),
				'priority' => 10
				),

			'preview' => array(
				'name'     => __( 'Preview', 'gam-event-manager' ),
				'view'     => array( $this, 'preview' ),
				'handler'  => array( $this, 'preview_handler' ),
				'priority' => 20
			),

			'done' => array(
				'name'     => __( 'Done', 'gam-event-manager' ),
				'view'     => array( $this, 'done' ),
				'priority' => 30
			)
		) );

		uasort( $this->steps, array( $this, 'sort_by_priority' ) );
		// Get step/event
		if ( isset( $_POST['step'] ) ) {
			$this->step = is_numeric( $_POST['step'] ) ? max( absint( $_POST['step'] ), 0 ) : array_search( $_POST['step'], array_keys( $this->steps ) );
		} elseif ( ! empty( $_GET['step'] ) ) {
			$this->step = is_numeric( $_GET['step'] ) ? max( absint( $_GET['step'] ), 0 ) : array_search( $_GET['step'], array_keys( $this->steps ) );
		}

		$this->event_id = ! empty( $_REQUEST['event_id'] ) ? absint( $_REQUEST[ 'event_id' ] ) : 0;
		// Allow resuming from cookie.
		if ( ! $this->event_id && ! empty( $_COOKIE['gam-event-manager-submitting-event-id'] ) && ! empty( $_COOKIE['gam-event-manager-submitting-event-key'] ) ) {
			$event_id     = absint( $_COOKIE['gam-event-manager-submitting-event-id'] );
			$event_status = get_post_status( $event_id );
			if ( 'preview' === $event_status && get_post_meta( $event_id, '_submitting_key', true ) === $_COOKIE['gam-event-manager-submitting-event-key'] ) {
				$this->event_id = $event_id;
			}
		}
		// Load event details
		if ( $this->event_id ) {
			$event_status = get_post_status( $this->event_id );
			if ( 'expired' === $event_status ) {
				if ( ! event_manager_user_can_edit_event( $this->event_id ) ) {
					$this->event_id = 0;
					$this->step   = 0;
				}
			} elseif ( ! in_array( $event_status, apply_filters( 'event_manager_valid_submit_event_statuses', array( 'preview' ) ) ) ) {
				$this->event_id = 0;
				$this->step   = 0;
			}
		}
	}
	
	/**
	 * Get the submitted event ID
	 * @return int
	*/
	public function get_event_id() {
		return absint( $this->event_id );
	}
	/**
	 * init_fields function.
	 */
	public function init_fields() {
		if ( $this->fields ) {
			return;
		}
		
		$allowed_registration_method = get_option( 'event_manager_allowed_registration_method', '' );
		switch ( $allowed_registration_method ) {
			case 'email' :
				$registration_method_label       = __( 'Registration email', 'gam-event-manager' );
				$registration_method_placeholder = __( 'you@yourdomain.com', 'gam-event-manager' );
			break;
			case 'url' :
				$registration_method_label       = __( 'Registration URL', 'gam-event-manager' );
				$registration_method_placeholder = __( 'http://', 'gam-event-manager' );
			break;
			default :
				$registration_method_label       = __( 'Registration email/URL', 'gam-event-manager' );
				$registration_method_placeholder = __( 'Enter an email address or website URL', 'gam-event-manager' );
			break;
		}
		
		$this->fields = apply_filters( 'submit_event_form_fields', array(
			'event' => array(
				'event_title' => array(
					'label'       => __( 'Event Title', 'gam-event-manager' ),
					'type'        => 'text',
					'required'    => true,
					'placeholder' => 'Event title',
					'priority'    => 1
				),

				'event_type' => array(
					'label'       => __( 'Event Type', 'gam-event-manager' ),
					'type'        => 'term-select',
					'required'    => true,
					'placeholder' => '',
					'priority'    => 2,
					'default'     => 'meeting-or-networking-event',
					'taxonomy'    => 'event_listing_type'
				),

				'event_category' => array(
					'label'       => __( 'Event Category', 'gam-event-manager' ),
					'type'        => 'term-multiselect',
					'required'    => true,
					'placeholder' => '',
					'priority'    => 3,
					'default'     => '',
					'taxonomy'    => 'event_listing_category'
				),

				'event_online' => array(
							        'label'=> 'Online Event',							      	
							        'type'  => 'radio',
								    'default'  => 'no',
								    'options'  => array(
											    'yes' => __( 'Yes', 'gam-event-manager' ),
											    'no' => __( 'No', 'gam-event-manager' )
								 		    ),
								    'priority'    => 4,
							        'required'=>true
		 		 ),		
		 		 
		 		 'event_venue_name' => array(
					'label'       => __( 'Venue Name', 'gam-event-manager' ),
					'type'        => 'text',
					'required'    => 'true',					
					'placeholder' => __( 'Please enter the venue name', 'gam-event-manager' ),
					'priority'    => 5
				),			

				'event_address' => array(
					'label'       => __( 'Address', 'gam-event-manager' ),
					'type'        => 'text',
					'required'    => 'true',					
					'placeholder' => __( 'Please enter street name and number', 'gam-event-manager' ),
					'priority'    => 6
				),			

				'event_pincode' => array(
					'label'       => __( 'Pincode', 'gam-event-manager' ),
					'type'        => 'text',
					'required'    => true,
					'placeholder' => __( 'Please enter pincode (Area code)', 'gam-event-manager' ),
					'priority'    => 7
				),

				'event_location' => array(
					'label'       => __( 'Location', 'gam-event-manager' ),
					'type'        => 'text',
					'required'    => true,
					'placeholder' => __( 'e.g. "Berlin","London"', 'gam-event-manager' ),
					'priority'    => 8
				),

				'event_banner' => array(
					'label'       => __( 'Event Banner', 'gam-event-manager' ),
					'type'        => 'file',
					'required'    => true,
					'placeholder' => '',
					'priority'    => 9,
					'ajax'        => true,
					'multiple'    => false,
					'allowed_mime_types' => array(
						'jpg'  => 'image/jpeg',
						'jpeg' => 'image/jpeg',
						'gif'  => 'image/gif',
						'png'  => 'image/png'
					)
				),

				'event_description' => array(
					'label'       => __( 'Description', 'gam-event-manager' ),
					'type'        => 'wp-editor',
					'required'    => true,
					'placeholder' => '',
					'priority'    => 10
				),	
				'registration' => array(
					'label'       => $registration_method_label,
					'type'        => 'text',
					'required'    => true,
					'placeholder' => $registration_method_placeholder,
					'priority'    => 11
				),

				'event_start_date' => array(  
								'label'=> __( 'Start Date', 'gam-event-manager' ),
								'placeholder'  => __( 'Please enter event start date', 'gam-event-manager' ),								
								'type'  => 'date',
								'priority'    => 12,
								'required'=>true	  
							  ),
				'event_start_time' => array(  
								'label'=> __( 'Start Time', 'gam-event-manager' ),
								'placeholder'  => __( 'Please enter event start time', 'gam-event-manager' ),								
								'type'  => 'date',
								'priority'    => 13,
								'required'=>true	  
							  ),

				'event_end_date' => array(
							        'label'=> __( 'End Date', 'gam-event-manager' ),
							        'placeholder'  => __( 'Please enter event end date', 'gam-event-manager' ),							        
							        'type'  => 'date',
								    'priority'    => 14,
							        'required'=>true
							  ),
							  
				'event_end_time' => array(  
								'label'=> __( 'End Time', 'gam-event-manager' ),
								'placeholder'  => __( 'Please enter event end time', 'gam-event-manager' ),								
								'type'  => 'date',
								'priority'    => 15,
								'required'=>true	  
							  ),

				'event_ticket_options' => array(
							        'label'=> __( 'Ticket Options', 'gam-event-manager' ),							      
							        'type'  => 'radio',
								    'default'  => 'free',
								    'options'  => array(
											    'paid' => __( 'Paid', 'gam-event-manager' ),
											    'free' => __( 'Free', 'gam-event-manager' )
								 		    ),
								    'priority'    => 16,
							        'required'=>true
		 		),
                		'event_ticket_price' => array(
							        'label'=> __( 'Ticket Price', 'gam-event-manager' ),                              
							        'placeholder'  => __( 'Please enter ticket price', 'gam-event-manager' ),							        
							        'type'  => 'text',
								'priority'    => 17,
							        'required'=>true
							  ),

				'event_repeat' => array(
								'label'=> __( 'Repeat Event', 'gam-event-manager' ),						     
							    'type'  => 'radio',
								'default'  => 'no',
								'options'  => array(
											'every-week' => __( 'Every Week', 'gam-event-manager' ),
											'every-2week' => __( 'Every 2nd Week', 'gam-event-manager' ),
											'every-month' => __( 'Every Month', 'gam-event-manager' ),
											'no' => __( 'No', 'gam-event-manager' ),
								 		),
								'priority'    => 18,
							        'required'=>true
				),

				'event_link_to_eventpage' => array(
									'label'       => __( 'Link To Event Page', 'gam-event-manager' ),									
									'type'        => 'text',
									'required'    => false,					
									'placeholder' => __( 'e.g http://www.example.com', 'gam-event-manager' ),
									'priority'    => 19
				),

				'event_registration_deadline' => array(
									'label'       => __( 'Registration Deadline', 'gam-event-manager' ),	
									'type'        => 'date',
									'required'    => false,					
									'placeholder' => __( 'Please enter registration deadline', 'gam-event-manager' ),
									'priority'    => 20
				),						 
			),
			
			'organizer' => array(
				'organizer_name' => array(
								'label'       => __( 'Organization name', 'gam-event-manager' ),
								'type'        => 'text',
								'required'    => true,
								'placeholder' => __( 'Enter the name of the organization', 'gam-event-manager' ),
								'priority'    => 1
				),
				'organizer_logo' => array(
								'label'       => __( 'Logo', 'gam-event-manager' ),
								'type'        => 'file',
								'required'    => false,
								'placeholder' => '',
								'priority'    => 2,
								'ajax'        => true,
								'multiple'    => false,
								'allowed_mime_types' => array(
									'jpg'  => 'image/jpeg',
									'jpeg' => 'image/jpeg',
									'gif'  => 'image/gif',
									'png'  => 'image/png'
								)
				),

				'organizer_description' => array(
					'label'       => __( 'Organizer Description', 'gam-event-manager' ),
					'type'        => 'wp-editor',
					'required'    => true,
					'placeholder' => '',
					'priority'    => 3
				),	

				'organizer_contact_person_name' => array(
								'label'       => __( 'Contact Person Name', 'gam-event-manager' ),
								'type'        => 'text',
								'required'    => true,
								'placeholder' => __( 'Enter contact person name in your organization', 'gam-event-manager' ),
								'priority'    => 4
				),

				'organizer_email' => array(
								'label'       => __( 'Organization Email', 'gam-event-manager' ),
								'type'        => 'text',
								'required'    => true,
								'placeholder' => __( 'Enter your email address', 'gam-event-manager' ),
								'priority'    => 5
				),

				'organizer_website' => array(
								'label'       => __( 'Website', 'gam-event-manager' ),
								'type'        => 'text',
								'required'    => false,
								'placeholder' => __( 'Website URL e.g http://www.yourorganization.com', 'gam-event-manager' ),
								'priority'    => 6
				),

				'organizer_video' => array(
								'label'       => __( 'Video', 'gam-event-manager' ),
								'type'        => 'text',
								'required'    => false,
								'placeholder' => __( 'A link to a video about your organization', 'gam-event-manager' ),
								'priority'    => 7
				),

				'organizer_youtube' => array(
								'label'       => __( 'Youtube', 'gam-event-manager' ),
								'type'        => 'text',
								'required'    => false,
								'placeholder' => __( 'Youtube Channel URL e.g http://www.youtube.com/channel/yourcompany', 'gam-event-manager' ),
								'priority'    => 8
				),

				'organizer_google_plus' => array(
								'label'       => __( 'Google+', 'gam-event-manager' ),
								'type'        => 'text',
								'required'    => false,
								'placeholder' => __( 'Google+ URL e.g http://plus.google.com/yourcompany', 'gam-event-manager' ),
								'priority'    => 9
				),

				'organizer_facebook' => array(
								'label'       => __( 'Facebook', 'gam-event-manager' ),
								'type'        => 'text',
								'required'    => false,
								'placeholder' => __( 'Facebook URL e.g http://www.facebook.com/yourcompany', 'gam-event-manager' ),
								'priority'    => 10
				),

				'organizer_linkedin' => array(
								'label'       => __( 'Linkedin', 'gam-event-manager' ),
								'type'        => 'text',
								'required'    => false,
								'placeholder' => __( 'Linkedin URL e.g http://www.linkedin.com/company/yourcompany', 'gam-event-manager' ),
								'priority'    => 11
				),

				'organizer_twitter' => array(
								'label'       => __( 'Twitter', 'gam-event-manager' ),
								'type'        => 'text',
								'required'    => false,
								'placeholder' => __( 'Twitter URL e.g http://twitter.com/yourorganizer', 'gam-event-manager' ),
								'priority'    => 12
				),

				'organizer_xing' => array(
								'label'       => __( 'Xing', 'gam-event-manager' ),
								'type'        => 'text',
								'required'    => false,
								'placeholder' => __( 'Xing URL e.g http://www.xing.com/companies/yourcompany', 'gam-event-manager' ),
								'priority'    => 13
				),

				'organizer_pinterest' => array(
								'label'       => __( 'Pinterest', 'gam-event-manager' ),
								'type'        => 'text',
								'required'    => false,
								'placeholder' => __( 'Pinterest URL e.g http://www.pinterest.com/yourcompany', 'gam-event-manager' ),
								'priority'    => 14
				),

				'organizer_instagram' => array(
								'label'       => __( 'Instagram', 'gam-event-manager' ),
								'type'        => 'text',
								'required'    => false,
								'placeholder' => __( 'Instagram URL e.g http://www.instagram.com/yourcompany', 'gam-event-manager' ),
								'priority'    => 15
				)
			)
		) );

		if ( ! get_option( 'event_manager_enable_categories' ) || wp_count_terms( 'event_listing_category' ) == 0 ) {
			unset( $this->fields['event']['event_category'] );
		}
	}

	/**
	 * Validate the posted fields
	 *
	 * @return bool on success, WP_ERROR on failure
	 */
	protected function validate_fields( $values ) {
	      foreach ( $this->fields as $group_key => $group_fields )
    	  {     	      
    	       //this filter need to apply for remove required attributes when option online event selected and ticket price.
    	       if(isset($group_fields['event_online'] ) )
				 {
    				if($group_fields['event_online']['value']=='yes')
    				{	  
    				    $group_fields['event_venue_name']['required']=false;
    					$group_fields['event_address']['required']=false;
    					$group_fields['event_pincode']['required']=false;
    					$group_fields['event_location']['required']=false;
    				}
				 }
				 
				 if(isset($group_fields['event_ticket_options']) )
				{
    				if($group_fields['event_ticket_options']['value']=='free')
    				{	
    					$group_fields['event_ticket_price']['required']=false;
    				} 			
				}
		        foreach ( $group_fields as $key => $field ) 
              	{
    				if ( $field['required'] && empty( $values[ $group_key ][ $key ] ) ) {	    
    					return new WP_Error( 'validation-error', sprintf( __( '%s is a required field', 'gam-event-manager' ), $field['label'] ) );
    				}

				    if ( ! empty( $field['taxonomy'] ) && in_array( $field['type'], array( 'term-checklist', 'term-select', 'term-multiselect' ) ) ) {
    					if ( is_array( $values[ $group_key ][ $key ] ) ) {
    						$check_value = $values[ $group_key ][ $key ];
    					} else {
    						$check_value = array( $values[ $group_key ][ $key ] );    
    					}
    					foreach ( $check_value as $term ) {    
    						if ( ! term_exists( $term, $field['taxonomy'] ) ) {
    							return new WP_Error( 'validation-error', sprintf( __( '%s is invalid', 'gam-event-manager' ), $field['label'] ) );    
    						}
    					}
    				}

				if ( 'file' === $field['type'] && ! empty( $field['allowed_mime_types'] ) ) {
					if ( is_array( $values[ $group_key ][ $key ] ) ) {
						$check_value = array_filter( $values[ $group_key ][ $key ] );
					} else {
						$check_value = array_filter( array( $values[ $group_key ][ $key ] ) );
					}
					if ( ! empty( $check_value ) ) {
						foreach ( $check_value as $file_url ) {
							$file_url = current( explode( '?', $file_url ) );
							if ( ( $info = wp_check_filetype( $file_url ) ) && ! in_array( $info['type'], $field['allowed_mime_types'] ) ) {
								throw new Exception( sprintf( __( '"%s" (filetype %s) needs to be one of the following file types: %s', 'gam-event-manager' ), $field['label'], $info['ext'], implode( ', ', array_keys( $field['allowed_mime_types'] ) ) ) );
							}
						}
					}
				}
			}
		}
		
		// Registration method
		if ( isset( $values['event']['registration'] ) && ! empty( $values['event']['registration'] ) ) {
			$allowed_registration_method = get_option( 'event_manager_allowed_registration_method', '' );
			$values['event']['registration'] = str_replace( ' ', '+', $values['event']['registration'] );
			switch ( $allowed_registration_method ) {
				case 'email' :
					if ( ! is_email( $values['event']['registration'] ) ) {
						throw new Exception( __( 'Please enter a valid registration email address', 'gam-event-manager' ) );
					}
				break;
				case 'url' :
					// Prefix http if needed
					if ( ! strstr( $values['event']['registration'], 'http:' ) && ! strstr( $values['event']['registration'], 'https:' ) ) {
						$values['event']['registration'] = 'http://' . $values['event']['registration'];
					}
					if ( ! filter_var( $values['event']['registration'], FILTER_VALIDATE_URL ) ) {
						throw new Exception( __( 'Please enter a valid registration URL', 'gam-event-manager' ) );
					}
				break;
				default :
					if ( ! is_email( $values['event']['registration'] ) ) {
						// Prefix http if needed
						if ( ! strstr( $values['event']['registration'], 'http:' ) && ! strstr( $values['event']['registration'], 'https:' ) ) {
							$values['event']['registration'] = 'http://' . $values['event']['registration'];
						}
						if ( ! filter_var( $values['event']['registration'], FILTER_VALIDATE_URL ) ) {
							throw new Exception( __( 'Please enter a valid registration email address or URL', 'gam-event-manager' ) );
						}
					}
				break;
			}
		}
		
		return apply_filters( 'submit_event_form_validate_fields', true, $this->fields, $values );
	}

	/**
	 * event_types function.
	 */

	private function event_types() {
		$options = array();
		$terms   = get_event_listing_types();
		foreach ( $terms as $term ) {
			$options[ $term->slug ] = $term->name;
		}
		return $options;
	}

	/**
	 * Submit Step
	 */
	public function submit() {
		$this->init_fields();
		// Load data if neccessary
		if ( $this->event_id ) {
			$event = get_post( $this->event_id );
			foreach ( $this->fields as $group_key => $group_fields ) {
				foreach ( $group_fields as $key => $field ) {
					switch ( $key ) {
						case 'event_title' :
							$this->fields[ $group_key ][ $key ]['value'] = $event->post_title;
						break;
						case 'event_description' :
							$this->fields[ $group_key ][ $key ]['value'] = $event->post_content;
						break;
						case 'event_type' :
							$this->fields[ $group_key ][ $key ]['value'] = current( wp_get_object_terms( $event->ID, 'event_listing_type', array( 'fields' => 'ids' ) ) );
						break;
						case 'event_category' :
							$this->fields[ $group_key ][ $key ]['value'] = wp_get_object_terms( $event->ID, 'event_listing_category', array( 'fields' => 'ids' ) );
						break;
						default:
							$this->fields[ $group_key ][ $key ]['value'] = get_post_meta( $event->ID, '_' . $key, true );
						break;
					}
				}
			}

			$this->fields = apply_filters( 'submit_event_form_fields_get_event_data', $this->fields, $event );
		// Get user meta
		} elseif ( is_user_logged_in() && empty( $_POST['submit_event'] ) ) {
			if ( ! empty( $this->fields['organizer'] ) ) {
				foreach ( $this->fields['organizer'] as $key => $field ) {
					$this->fields['organizer'][ $key ]['value'] = get_user_meta( get_current_user_id(), '_' . $key, true );
				}
			}
			
			if ( ! empty( $this->fields['event']['registration'] ) ) {
				$allowed_registration_method = get_option( 'event_manager_allowed_registration_method', '' );
				if ( $allowed_registration_method !== 'url' ) {
					$current_user = wp_get_current_user();
					$this->fields['event']['registration']['value'] = $current_user->user_email;
				}
			}
			
			
			$this->fields = apply_filters( 'submit_event_form_fields_get_user_data', $this->fields, get_current_user_id() );
		}

		wp_enqueue_script( 'gam-event-manager-event-submission' );
		get_event_manager_template( 'event-submit.php', array(
			'form'               => $this->form_name,
			'event_id'             => $this->get_event_id(),
			'action'             => $this->get_action(),
			'event_fields'         => $this->get_fields( 'event' ),
			'organizer_fields'     => $this->get_fields( 'organizer' ),
			'step'               => $this->get_step(),
			'submit_button_text' => apply_filters( 'submit_event_form_submit_button_text', __( 'Preview', 'gam-event-manager' ) )
		) );
	}

	/**
	 * Submit Step is posted
	 */
	public function submit_handler() {
		try {
			// Init fields
			$this->init_fields();
			// Get posted values
			$values = $this->get_posted_fields();
			if ( empty( $_POST['submit_event'] ) ) {
				return;
			}
			// Validate required
			if ( is_wp_error( ( $return = $this->validate_fields( $values ) ) ) ) {
				throw new Exception( $return->get_error_message() );
			}
			// Account creation
			if ( ! is_user_logged_in() ) {
				$create_account = false;
				if ( event_manager_enable_registration() ) {
					if ( event_manager_user_requires_account() ) {
						if ( ! event_manager_generate_username_from_email() && empty( $_POST['create_account_username'] ) ) {
							throw new Exception( __( 'Please enter a username.', 'gam-event-manager' ) );
						}
						if ( empty( $_POST['create_account_email'] ) ) {
							throw new Exception( __( 'Please enter your email address.', 'gam-event-manager' ) );
						}
					}

					if ( ! empty( $_POST['create_account_email'] ) ) {
						$create_account = gam_event_manager_create_account( array(
							'username' => empty( $_POST['create_account_username'] ) ? '' : $_POST['create_account_username'],
							'email'    => $_POST['create_account_email'],
							'role'     => get_option( 'event_manager_registration_role' )
						) );
					}
				}

				if ( is_wp_error( $create_account ) ) {
					throw new Exception( $create_account->get_error_message() );
				}
			}
			if ( event_manager_user_requires_account() && ! is_user_logged_in() ) {
				throw new Exception( __( 'You must be signed in to post a new listing.' ) );
			}

			// Update the event
			$this->save_event( $values['event']['event_title'], $values['event']['event_description'], $this->event_id ? '' : 'preview', $values );
			$this->update_event_data( $values );
			// Successful, show next step
			$this->step ++;
		} catch ( Exception $e ) {
			$this->add_error( $e->getMessage() );
			return;
		}
	}

	/**
	 * Update or create a event listing from posted data
	 *
	 * @param  string $post_title
	 * @param  string $post_content
	 * @param  string $status
	 * @param  array $values
	 * @param  bool $update_slug
	 */
	protected function save_event( $post_title, $post_content, $status = 'preview', $values = array(), $update_slug = true ) {
		$event_data = array(
			'post_title'     => $post_title,
			'post_content'   => $post_content,
			'post_type'      => 'event_listing',
			'comment_status' => 'closed'
		);

		if ( $update_slug ) {
			$event_slug   = array();
			// Prepend with organizer name
			if ( apply_filters( 'submit_event_form_prefix_post_name_with_organizer', true ) && ! empty( $values['organizer']['organizer_name'] ) ) {
				$event_slug[] = $values['organizer']['organizer_name'];
			}
			// Prepend location
			if ( apply_filters( 'submit_event_form_prefix_post_name_with_location', true ) && ! empty( $values['event']['event_location'] ) ) {
				$event_slug[] = $values['event']['event_location'];
			}
			// Prepend with event type
			if ( apply_filters( 'submit_event_form_prefix_post_name_with_event_type', true ) && ! empty( $values['event']['event_type'] ) ) {
				$event_slug[] = $values['event']['event_type'];
			}
			$event_slug[]            = $post_title;
			$event_data['post_name'] = sanitize_title( implode( '-', $event_slug ) );
		}
		if ( $status ) {
			$event_data['post_status'] = $status;
		}
		$event_data = apply_filters( 'submit_event_form_save_event_data', $event_data, $post_title, $post_content, $status, $values );
		if ( $this->event_id ) {
			$event_data['ID'] = $this->event_id;
			wp_update_post( $event_data );
		} else {
			$this->event_id = wp_insert_post( $event_data );
			if ( ! headers_sent() ) {
				$submitting_key = uniqid();
				setcookie( 'gam-event-manager-submitting-event-id', $this->event_id, false, COOKIEPATH, COOKIE_DOMAIN, false );
				setcookie( 'gam-event-manager-submitting-event-key', $submitting_key, false, COOKIEPATH, COOKIE_DOMAIN, false );
				update_post_meta( $this->event_id, '_submitting_key', $submitting_key );
			}
		}
	}

	/**
	 * Set event meta + terms based on posted values
	 *
	 * @param  array $values
	 */
	protected function update_event_data( $values ) {
		// Set defaults
		add_post_meta( $this->event_id, '_cancelled', 0, true );
		add_post_meta( $this->event_id, '_featured', 0, true );
		$maybe_attach = array();

		// Loop fields and save meta and term data
		foreach ( $this->fields as $group_key => $group_fields ) {
			foreach ( $group_fields as $key => $field ) {
				// Save taxonomies
				if ( ! empty( $field['taxonomy'] ) ) {
					if ( is_array( $values[ $group_key ][ $key ] ) ) {
						wp_set_object_terms( $this->event_id, $values[ $group_key ][ $key ], $field['taxonomy'], false );
					} else {
						wp_set_object_terms( $this->event_id, array( $values[ $group_key ][ $key ] ), $field['taxonomy'], false );
					}				
				// Save meta data
				}
				 else { 
					update_post_meta( $this->event_id, '_' . $key, $values[ $group_key ][ $key ] );
				}

				// Handle attachments
				if ( 'file' === $field['type'] ) {
					// Must be absolute
					if ( is_array( $values[ $group_key ][ $key ] ) ) {
						foreach ( $values[ $group_key ][ $key ] as $file_url ) {
							if ( strstr( $file_url, WP_CONTENT_URL ) ) {
								$maybe_attach[] = str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, $file_url );
							}
						}
					} else {
						if ( strstr( $values[ $group_key ][ $key ], WP_CONTENT_URL ) ) {
							$maybe_attach[] = str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, $values[ $group_key ][ $key ] );
						}
					}
				}
			}
		}
		// Handle attachments
		if ( sizeof( $maybe_attach ) && apply_filters( 'event_manager_attach_uploaded_files', true ) ) {
			/** WordPress Administration Image API */
			include_once( ABSPATH . 'wp-admin/includes/image.php' );
			// Get attachments
			$attachments     = get_posts( 'post_parent=' . $this->event_id . '&post_type=attachment&fields=ids&post_mime_type=image&numberposts=-1' );
			$attachment_urls = array();
			// Loop attachments already attached to the event
			foreach ( $attachments as $attachment_key => $attachment ) {
				$attachment_urls[] = str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, wp_get_attachment_url( $attachment ) );
			}
			foreach ( $maybe_attach as $attachment_url ) {
				if ( ! in_array( $attachment_url, $attachment_urls ) ) {
					$attachment = array(
						'post_title'   => get_the_title( $this->event_id ),
						'post_content' => '',
						'post_status'  => 'inherit',
						'post_parent'  => $this->event_id,
						'guid'         => $attachment_url
					);
					if ( $info = wp_check_filetype( $attachment_url ) ) {
						$attachment['post_mime_type'] = $info['type'];
					}
					$attachment_id = wp_insert_attachment( $attachment, $attachment_url, $this->event_id );
					if ( ! is_wp_error( $attachment_id ) ) {
						wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $attachment_url ) );
					}
				}
			}
		}

		// And user meta to save time in future
		if ( is_user_logged_in() ) {
			update_user_meta( get_current_user_id(), '_organizer_name', isset( $values['organizer']['organizer_name'] ) ? $values['organizer']['organizer_name'] : '' );
			update_user_meta( get_current_user_id(), '_organizer_website', isset( $values['organizer']['organizer_website'] ) ? $values['organizer']['organizer_website'] : '' );
			update_user_meta( get_current_user_id(), '_organizer_tagline', isset( $values['organizer']['organizer_tagline'] ) ? $values['organizer']['organizer_tagline'] : '' );
			update_user_meta( get_current_user_id(), '_organizer_twitter', isset( $values['organizer']['organizer_twitter'] ) ? $values['organizer']['organizer_twitter'] : '' );
			update_user_meta( get_current_user_id(), '_organizer_logo', isset( $values['organizer']['organizer_logo'] ) ? $values['organizer']['organizer_logo'] : '' );
			update_user_meta( get_current_user_id(), '_organizer_video', isset( $values['organizer']['organizer_video'] ) ? $values['organizer']['organizer_video'] : '' );
		}
		do_action( 'event_manager_update_event_data', $this->event_id, $values );
	}

	/**
	 * Preview Step
	 */

	public function preview() {
		global $post, $event_preview;
		if ( $this->event_id ) {
			$event_preview       = true;
			$action            = $this->get_action();
			$post              = get_post( $this->event_id );
			setup_postdata( $post );
			$post->post_status = 'preview';
			?>
			<form method="post" id="event_preview" action="<?php echo esc_url( $action ); ?>">
				<div class="event_listing_preview_title">
					<input type="submit" name="continue" id="event_preview_submit_button" class="button" value="<?php echo apply_filters( 'submit_event_step_preview_submit_text', __( 'Submit Listing →', 'gam-event-manager' ) ); ?>" />
					<input type="submit" name="edit_event" class="button" value="<?php _e( '← Edit listing', 'gam-event-manager' ); ?>" />
					<input type="hidden" name="event_id" value="<?php echo esc_attr( $this->event_id ); ?>" />
					<input type="hidden" name="step" value="<?php echo esc_attr( $this->step ); ?>" />
					<input type="hidden" name="event_manager_form" value="<?php echo $this->form_name; ?>" />
					<h2>
						<?php _e( 'Preview', 'gam-event-manager' ); ?>
					</h2>
				</div>
				<div class="event_listing_preview single_event_listing">			
					<?php get_event_manager_template_part( 'content-single', 'event_listing' ); ?>
				</div>
			</form>
			<?php
			wp_reset_postdata();
		}
	}
	
	/**
	 * Preview Step Form handler
	 */
	public function preview_handler() {
		if ( ! $_POST ) {
			return;
		}
		// Edit = show submit form again
		if ( ! empty( $_POST['edit_event'] ) ) {
			$this->step --;
		}
		// Continue = change event status then show next screen
		if ( ! empty( $_POST['continue'] ) ) {
			$event = get_post( $this->event_id );
			if ( in_array( $event->post_status, array( 'preview', 'expired' ) ) ) {
				// Reset expiry
				delete_post_meta( $event->ID, '_event_expiry_date' );
				// Update event listing
				$update_event                  = array();
				$update_event['ID']            = $event->ID;
				$update_event['post_status']   = get_option( 'event_manager_submission_requires_approval' ) ? 'pending' : 'publish';
				$update_event['post_date']     = current_time( 'mysql' );
				$update_event['post_date_gmt'] = current_time( 'mysql', 1 );
				wp_update_post( $update_event );
			}			
			$this->step ++;
		}
	}
	
	/**
	 * Done Step
	 */
	public function done() {
		do_action( 'event_manager_event_submitted', $this->event_id );
		get_event_manager_template( 'event-submitted.php', array( 'event' => get_post( $this->event_id ) ) );
	}
}