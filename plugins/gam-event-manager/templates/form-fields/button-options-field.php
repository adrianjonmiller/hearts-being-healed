<?php
/**
 * Button Options Field. Example definition:
 *
 * 'test_options' => array(
 * 		'label'    => __( 'Test Button Options', 'gam-event-manager' ),
 * 		'type'     => 'button-options',
 * 		'required' => false, * 		
 * 		'priority' => 1,
 * 		'options'  => array(
 * 			'option1' => 'This is option 1',
 * 		 	'option2' => 'This is option 2'
 * 		)
 * 	)
 */

foreach ( $field['options'] as $option_key => $value ) : ?>

<input type="button" name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>" id="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $option_key ); ?>" /> <?php echo esc_html( $value ); ?>

<?php endforeach; ?>
<?php if ( ! empty( $field['description'] ) ) : ?><small class="description"><?php echo $field['description']; ?></small><?php endif; ?>