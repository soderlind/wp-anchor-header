<?php
/*
Plugin Name: WP Anchorific
Plugin URI: http://soderlind.no/
Description: Generates anchored headings and nested anchored-based navigations based on header tags
Author: Per Soderlind
Version: 0.1.1
Author URI: http://soderlind.no
Text Domain: wp-anchorific
Domain Path: /languages
*/

defined( 'ABSPATH' ) or die();


define( 'ANCHORIFIC_URL',   plugin_dir_url( __FILE__ ));
define( 'ANCHORIFIC_VERSION', '0.1.0' );
/**
 * Adds Foo_Widget widget.
 */
class Soderlind_Anchorific extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'soderlind_anchorific', // Base ID
			__( 'WP Anchorific', 'wp-anchorific' ), // Name
			array( 'description' => __( 'Generates anchored headings and nested anchored-based navigations based on header tags', 'wp-anchorific' ), ) // Args
		);
		add_action('wp_enqueue_scripts', array($this,'add_script_style'));
		add_action('plugins_loaded', function(){
			load_plugin_textdomain( 'wp-anchorific', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		});
	}

	function add_script_style() {
		wp_enqueue_style('wp-anchorific-css',   ANCHORIFIC_URL . '/css/style.css', array(), ANCHORIFIC_VERSION );
		wp_enqueue_script('anchorific',         ANCHORIFIC_URL . '/js/anchorific.min.js',  array('jquery'), ANCHORIFIC_VERSION );
		wp_enqueue_script('wp-anchorific',      ANCHORIFIC_URL . '/js/wp-anchorific.js',array('jquery','anchorific'), ANCHORIFIC_VERSION );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		if ( is_singular() ) {
			echo $args['before_widget'];
			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
			}
			echo '<div class="anchorific"></div>';
			echo $args['after_widget'];
		}
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'wp-anchorific' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} // class Soderlind_Anchorific

// register Foo_Widget widget
function register_foo_widget() {
    register_widget( 'Soderlind_Anchorific' );
}


add_action( 'widgets_init', 'register_foo_widget' );

