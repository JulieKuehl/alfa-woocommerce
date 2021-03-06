<?php
/**
 * Plugin Name: WooCommerce ALFA Plugin
 * Description: A custom plugin to make WooCommerce work for American Legacy Fine Arts
 * Author: Julie Kuehl
 * Author URI: http://juliekuehl.com
 * Version: 1.0
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this
 * program. If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! class_exists( 'WC_ALFA_Plugin' ) ) :

class WC_ALFA_Plugin {
	protected static $instance = null;

	/**
	 * Initialize the plugin
	 *
	 * @since 1.0
	 */
	private function __construct() {
		if ( class_exists( 'WooCommerce' ) ) {
			// Print an admin notice on the screen
			add_action( 'admin_notices', array( $this, 'WC_ALFA_Plugin_notice' ) );
		}
	}

	/**
	 * Print an admin notice
	 *
	 * @since 1.0
	 */
	public function WC_ALFA_Plugin_notice() {
		global $pagenow;
		if ( $pagenow == 'plugins.php' ) {
			?>
				<div class="updated">
					<p><?php _e( 'WooCommerce has been customized for ALFA using the WooCommerce ALFA Plugin', 'WC_ALFA_Plugin' ); ?></p>
				</div>
			<?php
		}
	}

	/**
	 * Return an instance of this class
	 *
	 * @return object A single instance of this class
	 * @since 1.0
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}

add_action( 'init', array( 'WC_ALFA_Plugin', 'get_instance' ), 0 );

endif;


/**
 * Change the Shop archive page title.
 * @param  string $title
 * @return string
 */
function wc_custom_shop_archive_title( $title ) {
	if ( is_shop() ) {
		return str_replace( __( 'Products', 'woocommerce' ), 'Artwork', $title );
	}

	return $title;
}
add_filter( 'wp_title', 'wc_custom_shop_archive_title' );

function woo_shop_page_title( $page_title ) {
	if( 'Shop' == $page_title) {
		return "Artwork";
	}
}
add_filter( 'woocommerce_page_title', 'woo_shop_page_title');

/**
 * Register the post-to-post connection types
 */
add_action( 'p2p_init', 'alfa_connection_types' );

function alfa_connection_types() {
	p2p_register_connection_type( array (
		'name'  => 'product_to_artist',
		'from'  => 'product',
		'to'    => 'artist',
	) );
	p2p_register_connection_type( array (
		'name'  => 'product_to_exhibition',
		'from'  => 'product',
		'to'    => 'exhibition'
	) );
	p2p_register_connection_type( array(
		'name'  => 'exhibition_to_artist',
		'from'  => 'exhibition',
		'to'    => 'artist',
	));
}


/**
 * Remove the tabs from product pages
 */
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

function woo_remove_product_tabs( $tabs ) {

	unset( $tabs['description'] );      	// Remove the description tab
	unset( $tabs['reviews'] ); 			// Remove the reviews tab
	unset( $tabs['additional_information'] );  	// Remove the additional information tab

	return $tabs;

}

/**
 * Disable the WooCommerce stylesheets
 */

//add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * Enqueue custom WooCommerce stylesheet
 */

function wp_enqueue_woocommerce_style(){
	wp_register_style( 'alfa-woocommerce', get_template_directory_uri() . '/woocommerce.css' );

	if ( class_exists( 'woocommerce' ) ) {
		wp_enqueue_style( 'alfa-woocommerce' );
	}
}
add_action( 'wp_enqueue_scripts', 'wp_enqueue_woocommerce_style' );


/**
 * WooCommerce Extra Feature
 * --------------------------
 *
 * Change number of related products on product page
 * Set your own value for 'posts_per_page'
 *
 */
function woo_related_products_limit() {
	global $product;

	$args['posts_per_page'] = 4;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'jk_related_products_args' );
  function jk_related_products_args( $args ) {

	  $args['posts_per_page'] = 4; // 4 related products
	  $args['columns'] = 4; // arranged in 4 columns
	  return $args;
  }

/**
 * WooCommerce Single Product Page Customization
 * ---------------------------------------------
 *
 * Remove and reorder elements specific to ALFA
 *
 */

/**
 * Product Information Box (before content)
 *
 * @see woocommerce_template_single_title()
 * @see woocommerce_template_single_price()
 * @see woocommerce_template_single_excerpt()
 * @see woocommerce_template_single_meta()
 * @see woocommerce_template_single_sharing()
 */
//add_action( 'alfa_woocommerce_single_product_info', 'woocommerce_template_single_title', 5 );
//add_action( 'alfa_woocommerce_single_product_info', 'woocommerce_template_single_rating', 10 );
//add_action( 'alfa_woocommerce_single_product_info', 'woocommerce_template_single_price', 10 );
//add_action( 'alfa_woocommerce_single_product_info', 'woocommerce_template_single_excerpt', 20 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
//add_action( 'alfa_woocommerce_single_product_info', 'woocommerce_template_single_meta', 40 );
//add_action( 'alfa_woocommerce_single_product_info', 'woocommerce_template_single_sharing', 50 );



/**
 * Product Summary Box (after content)
 *
 * @see woocommerce_template_single_title()
 * @see woocommerce_template_single_price()
 * @see woocommerce_template_single_excerpt()
 * @see woocommerce_template_single_meta()
 * @see woocommerce_template_single_sharing()
 */
//add_action( 'alfa_woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
//add_action( 'alfa_woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
add_action( 'alfa_woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
//add_action( 'alfa_woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
//add_action( 'alfa_woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
add_action( 'alfa_woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );