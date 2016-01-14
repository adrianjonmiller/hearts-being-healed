<?php
/*
* This file use for settings at admin site for gam event manager plugin.
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * GAM_Event_Manager_Settings class.
 */

class GAM_Event_Manager_Settings {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */

	public function __construct() {

		$this->settings_group = 'event_manager';

		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * init_settings function.
	 *
	 * @access protected
	 * @return void
	 */

	protected function init_settings() {

		// Prepare roles option

		$roles         = get_editable_roles();

		$account_roles = array();
		foreach ( $roles as $key => $role ) {

			if ( $key == 'administrator' ) {

				continue;
			}

			$account_roles[ $key ] = $role['name'];
		}

		$this->settings = apply_filters( 'event_manager_settings',

			array(

				'event_listings' => array(

					__( 'Event Listings', 'gam-event-manager' ),

					array(

						array(

							'name'        => 'event_manager_per_page',

							'std'         => '10',

							'placeholder' => '',

							'label'       => __( 'Listings Per Page', 'gam-event-manager' ),

							'desc'        => __( 'How many listings should be shown per page by default?', 'gam-event-manager' ),

							'attributes'  => array()
						),

						array(

							'name'       => 'event_manager_hide_cancelled_events',

							'std'        => '0',

							'label'      => __( 'Cancelled Events', 'gam-event-manager' ),

							'cb_label'   => __( 'Hide cancelled events', 'gam-event-manager' ),

							'desc'       => __( 'If enabled, cancelled events will be hidden from archives.', 'gam-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_hide_expired_content',

							'std'        => '1',

							'label'      => __( 'Expired Listings', 'gam-event-manager' ),

							'cb_label'   => __( 'Hide content within expired listings', 'gam-event-manager' ),

							'desc'       => __( 'If enabled, the content within expired listings will be hidden. Otherwise, expired listings will be displayed as normal (without the event registration area).', 'gam-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_enable_categories',

							'std'        => '0',

							'label'      => __( 'Categories', 'gam-event-manager' ),

							'cb_label'   => __( 'Enable categories for listings', 'gam-event-manager' ),

							'desc'       => __( 'Choose whether to enable categories. Categories must be setup by an admin to allow users to choose them during submission.', 'gam-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_enable_event_types',

							'std'        => '0',

							'label'      => __( 'Event Types', 'gam-event-manager' ),

							'cb_label'   => __( 'Enable event types for listings', 'gam-event-manager' ),

							'desc'       => __( 'Choose whether to enable event types. event types must be setup by an admin to allow users to choose them during submission.', 'gam-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_enable_event_ticket_prices',

							'std'        => '0',

							'label'      => __( 'Ticket prices', 'gam-event-manager' ),

							'cb_label'   => __( 'Enable ticket prices for listings', 'gam-event-manager' ),

							'desc'       => __( 'Choose whether to enable ticket prices. Ticket prices must be setup by an admin to allow users to choose them during submission.', 'gam-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),						

						array(

							'name'       => 'event_manager_enable_default_category_multiselect',

							'std'        => '0',

							'label'      => __( 'Multi-select Categories', 'gam-event-manager' ),

							'cb_label'   => __( 'Enable category multiselect by default', 'gam-event-manager' ),

							'desc'       => __( 'If enabled, the category select box will default to a multi select on the [events] shortcode.', 'gam-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_enable_default_event_type_multiselect',

							'std'        => '0',

							'label'      => __( 'Multi-select Event Types', 'gam-event-manager' ),

							'cb_label'   => __( 'Enable event type multiselect by default', 'gam-event-manager' ),

							'desc'       => __( 'If enabled, the event type select box will default to a multi select on the [events] shortcode.', 'gam-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_category_filter_type',

							'std'        => 'any',

							'label'      => __( 'Category Filter Type', 'gam-event-manager' ),

							'desc'       => __( 'If enabled, the category select box will default to a multi select on the [events] shortcode.', 'gam-event-manager' ),

							'type'       => 'select',

							'options' => array(

								'any'  => __( 'Events will be shown if within ANY selected category', 'gam-event-manager' ),

								'all' => __( 'Events will be shown if within ALL selected categories', 'gam-event-manager' ),
							)
						),

						array(

							'name'       => 'event_manager_event_type_filter_type',

							'std'        => 'any',

							'label'      => __( 'Event Type Filter', 'gam-event-manager' ),

							'desc'       => __( 'If enabled, the event type select box will default to a multi select on the [events] shortcode.', 'gam-event-manager' ),

							'type'       => 'select',

							'options' => array(

								'any'  => __( 'Events will be shown if within ANY selected event type', 'gam-event-manager' ),

								'all' => __( 'Events will be shown if within ALL selected event types', 'gam-event-manager' ),
							)
						)
					),
				),

				'event_submission' => array(

					__( 'Event Submission', 'gam-event-manager' ),

					array(

						array(

							'name'       => 'event_manager_user_requires_account',

							'std'        => '1',

							'label'      => __( 'Account Required', 'gam-event-manager' ),

							'cb_label'   => __( 'Submitting listings requires an account', 'gam-event-manager' ),

							'desc'       => __( 'If disabled, non-logged in users will be able to submit listings without creating an account.', 'gam-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_enable_registration',

							'std'        => '1',

							'label'      => __( 'Account Creation', 'gam-event-manager' ),

							'cb_label'   => __( 'Allow account creation', 'gam-event-manager' ),

							'desc'       => __( 'If enabled, non-logged in users will be able to create an account by entering their email address on the submission form.', 'gam-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_generate_username_from_email',

							'std'        => '1',

							'label'      => __( 'Account Username', 'gam-event-manager' ),

							'cb_label'   => __( 'Automatically Generate Username from Email Address', 'gam-event-manager' ),

							'desc'       => __( 'If enabled, a username will be generated from the first part of the user email address. Otherwise, a username field will be shown.', 'gam-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_registration_role',

							'std'        => 'employer',

							'label'      => __( 'Account Role', 'gam-event-manager' ),

							'desc'       => __( 'If you enable registration on your submission form, choose a role for the new user.', 'gam-event-manager' ),

							'type'       => 'select',

							'options'    => $account_roles
						),

						array(

							'name'       => 'event_manager_submission_requires_approval',

							'std'        => '1',

							'label'      => __( 'Moderate New Listings', 'gam-event-manager' ),

							'cb_label'   => __( 'New listing submissions require admin approval', 'gam-event-manager' ),

							'desc'       => __( 'If enabled, new submissions will be inactive, pending admin approval.', 'gam-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_user_can_edit_pending_submissions',

							'std'        => '0',

							'label'      => __( 'Allow Pending Edits', 'gam-event-manager' ),

							'cb_label'   => __( 'Submissions awaiting approval can be edited', 'gam-event-manager' ),

							'desc'       => __( 'If enabled, submissions awaiting admin approval can be edited by the user.', 'gam-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),
						
						array(

							'name'       => 'event_manager_delete_expired_events',

							'std'        => '0',

							'label'      => __( 'Delete Expired listings', 'gam-event-manager' ),

							'cb_label'   => __( 'Expired listings are deleted after 30 days', 'gam-event-manager' ),

							'desc'       => __( 'If enabled, expired listings will automatically deleted after 30 days.', 'gam-event-manager' ),

							'type'       => 'checkbox',

							'attributes' => array()
						),

						array(

							'name'       => 'event_manager_submission_expire_options',

							'std'        => 'event_end_date',

							'label'      => __( 'Listing Expire', 'gam-event-manager' ),

							'desc'       => __( 'You can set event submission expiry time either event end date or specific days.', 'gam-event-manager' ),

							'type'       => 'select',

							'options' => array(

								'event_end_date'  => __( 'Listing expire on Event End Date', 'gam-event-manager' ),

								'days' => __( 'Listing expire on Specified Below Days', 'gam-event-manager' ),
							)
						),

						array(

							'name'       => 'event_manager_submission_duration',

							'std'        => '30',

							'label'      => __( 'Listing Duration', 'gam-event-manager' ),

							'desc'       => __( 'How many <strong>days</strong> listings are live before expiring. Can be left blank to never expire.', 'gam-event-manager' ),

							'attributes' => array()
						),
						array(
							'name'       => 'event_manager_allowed_registration_method',
							'std'        => '',
							'label'      => __( 'Registration Method', 'gam-event-manager' ),
							'desc'       => __( 'Choose the registratoin method for listings.', 'gam-event-manager' ),
							'type'       => 'select',
							'options'    => array(
								''      => __( 'Email address or website URL', 'gam-event-manager' ),
								'email' => __( 'Email addresses only', 'gam-event-manager' ),
								'url'   => __( 'Website URLs only', 'gam-event-manager' ),
							)
						)
					)
				),

				'event_pages' => array(

					__( 'Pages', 'gam-event-manager' ),

					array(

						array(

							'name' 		=> 'event_manager_submit_event_form_page_id',

							'std' 		=> '',

							'label' 	=> __( 'Submit Event Form Page', 'gam-event-manager' ),

							'desc'		=> __( 'Select the page where you have placed the [submit_event_form] shortcode. This lets the plugin know where the form is located.', 'gam-event-manager' ),

							'type'      => 'page'
						),

						array(

							'name' 		=> 'event_manager_event_dashboard_page_id',

							'std' 		=> '',

							'label' 	=> __( 'Event Dashboard Page', 'gam-event-manager' ),

							'desc'		=> __( 'Select the page where you have placed the [event_dashboard] shortcode. This lets the plugin know where the dashboard is located.', 'gam-event-manager' ),

							'type'      => 'page'
						),

						array(

							'name' 		=> 'event_manager_events_page_id',

							'std' 		=> '',

							'label' 	=> __( 'Event Listings Page', 'gam-event-manager' ),

							'desc'		=> __( 'Select the page where you have placed the [events] shortcode. This lets the plugin know where the event listings page is located.', 'gam-event-manager' ),

							'type'      => 'page'
						),
					)
				)
			)
		);
	}

	/**
	 * register_settings function.
	 *
	 * @access public
	 * @return void
	 */

	public function register_settings() {

		$this->init_settings();

		foreach ( $this->settings as $section ) {

			foreach ( $section[1] as $option ) {

				if ( isset( $option['std'] ) )

					add_option( $option['name'], $option['std'] );

				register_setting( $this->settings_group, $option['name'] );
			}
		}
	}

	/**
	 * output function.
	 *
	 * @access public
	 * @return void
	 */

	public function output() {

		$this->init_settings();

		?>
		
	
       
		<div class="wrap event-manager-settings-wrap">	

			<form method="post" name="event-manager-settings-form" action="options.php">	

				<?php settings_fields( $this->settings_group ); ?>

			    <h2 class="nav-tab-wrapper">

			    	<?php

			    		foreach ( $this->settings as $key => $section ) {

			    			echo '<a href="#settings-' . sanitize_title( $key ) . '" class="nav-tab">' . esc_html( $section[0] ) . '</a>';
			    		}
			    	?>
			    </h2>
			    
			 <div class="admin-setting-left">
			     	
			     <div class="white-background">
			     		
				<?php

					if ( ! empty( $_GET['settings-updated'] ) ) {

						flush_rewrite_rules();

						echo '<div class="updated fade event-manager-updated"><p>' . __( 'Settings successfully saved', 'gam-event-manager' ) . '</p></div>';
					}

					foreach ( $this->settings as $key => $section ) {

						echo '<div id="settings-' . sanitize_title( $key ) . '" class="settings_panel">';

						echo '<table class="form-table">';

						foreach ( $section[1] as $option ) {

							$placeholder    = ( ! empty( $option['placeholder'] ) ) ? 'placeholder="' . $option['placeholder'] . '"' : '';

							$class          = ! empty( $option['class'] ) ? $option['class'] : '';

							$value          = get_option( $option['name'] );

							$option['type'] = ! empty( $option['type'] ) ? $option['type'] : '';

							$attributes     = array();

							if ( ! empty( $option['attributes'] ) && is_array( $option['attributes'] ) )

								foreach ( $option['attributes'] as $attribute_name => $attribute_value )

									$attributes[] = esc_attr( $attribute_name ) . '="' . esc_attr( $attribute_value ) . '"';

							echo '<tr valign="top" class="' . $class . '"><th scope="row"><label for="setting-' . $option['name'] . '">' . $option['label'] . '</a></th><td>';

							switch ( $option['type'] ) {

								case "checkbox" :

									?><label><input id="setting-<?php echo $option['name']; ?>" name="<?php echo $option['name']; ?>" type="checkbox" value="1" <?php echo implode( ' ', $attributes ); ?> <?php checked( '1', $value ); ?> /> <?php echo $option['cb_label']; ?></label><?php

									if ( $option['desc'] )

										echo ' <p class="description">' . $option['desc'] . '</p>';

								break;

								case "textarea" :

									?><textarea id="setting-<?php echo $option['name']; ?>" class="large-text" cols="50" rows="3" name="<?php echo $option['name']; ?>" <?php echo implode( ' ', $attributes ); ?> <?php echo $placeholder; ?>><?php echo esc_textarea( $value ); ?></textarea><?php

									if ( $option['desc'] )

										echo ' <p class="description">' . $option['desc'] . '</p>';

								break;

								case "select" :

									?><select id="setting-<?php echo $option['name']; ?>" class="regular-text" name="<?php echo $option['name']; ?>" <?php echo implode( ' ', $attributes ); ?>><?php

										foreach( $option['options'] as $key => $name )

											echo '<option value="' . esc_attr( $key ) . '" ' . selected( $value, $key, false ) . '>' . esc_html( $name ) . '</option>';

									?></select><?php

									if ( $option['desc'] ) {

										echo ' <p class="description">' . $option['desc'] . '</p>';

									}

								break;

								case "page" :

									$args = array(

										'name'             => $option['name'],

										'id'               => $option['name'],

										'sort_column'      => 'menu_order',

										'sort_order'       => 'ASC',

										'show_option_none' => __( '--no page--', 'gam-event-manager' ),

										'echo'             => false,

										'selected'         => absint( $value )

									);

									echo str_replace(' id=', " data-placeholder='" . __( 'Select a page&hellip;', 'gam-event-manager' ) .  "' id=", wp_dropdown_pages( $args ) );

									if ( $option['desc'] ) {

										echo ' <p class="description">' . $option['desc'] . '</p>';

									}
									
								break;

								case "password" :

									?><input id="setting-<?php echo $option['name']; ?>" class="regular-text" type="password" name="<?php echo $option['name']; ?>" value="<?php esc_attr_e( $value ); ?>" <?php echo implode( ' ', $attributes ); ?> <?php echo $placeholder; ?> /><?php

									if ( $option['desc'] ) {

										echo ' <p class="description">' . $option['desc'] . '</p>';
									}

								break;

								case "" :

								case "input" :

								case "text" :

									?><input id="setting-<?php echo $option['name']; ?>" class="regular-text" type="text" name="<?php echo $option['name']; ?>" value="<?php esc_attr_e( $value ); ?>" <?php echo implode( ' ', $attributes ); ?> <?php echo $placeholder; ?> /><?php

									if ( $option['desc'] ) {

										echo ' <p class="description">' . $option['desc'] . '</p>';
								}

								break;

								default :

									do_action( 'gam_event_manager_admin_field_' . $option['type'], $option, $attributes, $value, $placeholder );

								break;
							}
							echo '</td></tr>';
						}
						echo '</table></div>';
					}
				?>
				 </div>   <!-- .white-background- -->
				<p class="submit">
					<input type="submit" class="button-primary" id="save-changes" value="<?php _e( 'Save Changes', 'gam-event-manager' ); ?>" />
				</p>
			 </div>  <!-- .admin-setting-left -->						
		    </form>
		    
            <div id="plugin_info" class="box-info">
                <div class="box-title" title="Click to toggle"><br></div><h3><span>Plugin Info</span></h3>
                    <div class="inside">
                        <p> 
                             <span class="premium-icon"></span><b>Help to improve this plugin!</b> <br>Enjoyed this plugin? You can help by rating this plugin on <a href="https://wordpress.org/plugins/gam-event-manager/" target="_blank" >wordpress.org.</a>
                        </p>
                        <p>  
                           <span class="help-icon"></span><b>Need help?</b> <br>
                           Read the <a href="http://www.gamthemes.com/plugins-documentation/gam-event-manager/" target="_blank" >plugin documentation.</a><br>
                           Check the <a href="http://www.gamthemes.com/faq/" target="_blank">FAQ.</a><br>
                        </p>
                        <p>  
                           <span class="connect-icon"></span><b>Demo</b> <br>Visit the <a href="http://marketplace.gamthemes.com/plugins/gam-event-manager" target="_blank">plugin demo.</a><br>
                           Visit the <a href="http://www.gamthemes.com/product-category/plugins/" target="_blank">premium add-ons</a>.<br>                           
                        </p>
                        
                        <p><span class="light-grey">This plugin was made with by</span> <a href="http://www.gamthemes.com" target="_blank">gamthemes.com</a>
                        </p>
                    </div>
                </div>
            </div>
	  	

		<?php  wp_enqueue_script( 'gam-event-manager-admin-settings');
	}
}