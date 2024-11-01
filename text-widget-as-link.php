<?php
/**
 * Text widget as link plugin file.
 *
 * @package Text Widget As Link
 * @version 1.4.9
 */

/*
Plugin Name: Text widget as link
Plugin URI: http://www.leanderlindahl.se
Description: Link the entire widget to a specific URL
Version: 1.4.9
Author: Leander Lindahl, leander@leanderlindahl.se
Author URI: http://www.leanderlindahl.se
*/

/**
 *  Creating the widget.
 */
class Text_Widget_As_Link extends WP_Widget {

	/**
	 * The inherited WP Widget constructor.
	 */
	public function __construct() {
		parent::__construct(
			// Base ID of your widget.
			'text_widget_as_link',
			// Widget name will appear in UI.
			__( 'Text Widget as link', 'text_widget_as_link_domain' ),
			// Widget description.
			array( 'description' => __( 'Write some text and have the entire widget link to a named URL', 'text_widget_as_link_domain' ) )
		);
	}

	/**
	 * Display widget – creating the widget front-end.
	 *
	 * @param array $args           Display arguments including 'before_title', 'after_title',
	 * 'before_widget', and 'after_widget'.
	 * @param array $instance       The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {
		// These are the widget options.
		$title      = apply_filters( 'widget_title', $instance['title'] );
		$target_url = $instance['target_url'];
		$textarea   = wpautop( apply_filters( 'widget_textarea', empty( $instance['textarea'] ) ? '' : $instance['textarea'], $instance ) );

		// Echo $args['before_widget'] won't pass PHPCS.
		// Interesting discussion here:
		// https://wordpress.stackexchange.com/questions/
		// 249015/php-coding-standards-widgets-and-sanitization.
		echo $args['before_widget'];

		// Check if target_url is set.
		if ( $target_url ) {
			echo '<a href="' . esc_url( $target_url ) . '">';
		}

		// Display the widget.
		echo '<div class="widget-text text-widget-as-link">';

		// Check if title is set.
		if ( $title ) {
			echo $args['before_title'];
			echo esc_attr( $title );
			echo $args['after_title'];
		}

		// Check if textarea is set.
		if ( $textarea ) {
			echo wp_kses_post( $textarea );
		}

		echo '</div>';
		if ( $target_url ) {
			echo '</a>';
		}
		echo $args['after_widget'];
	}

	/**
	 * Widget Backend. Outputs the settings update form.
	 *
	 * @param array $instance       Array of current settings.
	 */
	public function form( $instance ) {

		// Check values.
		if ( $instance ) {
			$title      = esc_attr( $instance['title'] );
			$target_url = esc_attr( $instance['target_url'] );
			$textarea   = esc_textarea( $instance['textarea'] );
		} else {
			$title      = '';
			$target_url = '';
			$textarea   = '';
		}

		// Widget admin form.
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'target_url' ) ); ?>"><?php esc_html_e( 'Target URL:' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'target_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'target_url' ) ); ?>" type="text" value="<?php echo esc_url( $target_url ); ?>" />
		</p>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'textarea' ) ); ?>"><?php esc_attr_e( 'Textarea:', 'wp_widget_plugin' ); ?></label>
		<textarea class="widefat" rows="10" id="<?php echo esc_attr( $this->get_field_id( 'textarea' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'textarea' ) ); ?>"><?php echo wp_kses_post( $textarea ); ?></textarea>
		</p>
		<?php
	}

	/**
	 *  Updates a particular instance of a widget.
	 *
	 * @param array $new_instance       New settings for this instance.
	 * @param array $old_instance       Old settings for this instance.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		// Fields.
		$instance['title']      = wp_strip_all_tags( $new_instance['title'] );
		$instance['target_url'] = wp_strip_all_tags( $new_instance['target_url'] );
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['textarea'] = $new_instance['textarea'];
		} else {
			$instance['textarea'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['textarea'] ) ) );
		}
		return $instance;
	}

}

/**
 * Register and load the widget.
 */
function wpb_load_widget() {
	register_widget( 'text_widget_as_link' );
}
add_action( 'widgets_init', 'wpb_load_widget' );
