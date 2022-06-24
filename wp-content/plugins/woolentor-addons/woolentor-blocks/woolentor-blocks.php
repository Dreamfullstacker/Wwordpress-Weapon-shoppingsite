<?php
if ( ! class_exists( 'WooLentorBlocks' ) ) :

	/**
	 * Main WooLentorBlocks Class
	 */
	final class WooLentorBlocks{

		/**
		 * $_instance
		 * @var null
		 */
		private static $_instance = null;

		public static function instance(){
			if( is_null( self::$_instance ) && ! ( self::$_instance instanceof WooLentorBlocks ) ){
				self::$_instance = new self();
				self::$_instance->define_constants();
				self::$_instance->includes();
				self::$_instance->dependency_class_instance();
			}
			return self::$_instance;
		} 

		/**
		 * Define the required plugin constants
		 *
		 * @return void
		 */
		public function define_constants() {
			$this->define( 'WOOLENTOR_BLOCK_FILE', __FILE__ );
			$this->define( 'WOOLENTOR_BLOCK_PATH', __DIR__ );
			$this->define( 'WOOLENTOR_BLOCK_URL', plugins_url( '', WOOLENTOR_BLOCK_FILE ) );
			$this->define( 'WOOLENTOR_BLOCK_DIR', plugin_dir_path( WOOLENTOR_BLOCK_FILE ) );
			$this->define( 'WOOLENTOR_BLOCK_ASSETS', WOOLENTOR_BLOCK_URL . '/assets' );
		}

		/**
	     * Define constant if not already set
	     *
	     * @param  string $name
	     * @param  string|bool $value
	     * @return type
	     */
	    private function define( $name, $value ) {
	        if ( ! defined( $name ) ) {
	            define( $name, $value );
	        }
	    }

		/**
		 * Load actions
		 *
		 * @return void
		 */
		private function includes() {
			include( WOOLENTOR_BLOCK_PATH . '/vendor/autoload.php' );
		}

		/**
		 * Load actions
		 *
		 * @return void
		 */
		private function dependency_class_instance() {
			WooLentorBlocks\Scripts::instance();
			WooLentorBlocks\Actions::instance();
			WooLentorBlocks\Blocks_init::instance();
		}


	}
	
endif;

/**
 * The main function for that returns Tutorial
 *
 */
function woolentorblocks() {
	if ( ! empty( $_REQUEST['action'] ) && 'elementor' === $_REQUEST['action'] ) {
		return;
	}elseif( class_exists( 'Classic_Editor' ) ){
		return;
	}else{
		return WooLentorBlocks::instance();
	}
}

// Get the plugin running. Load on plugins_loaded action to avoid issue on multisite.
if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	add_action( 'plugins_loaded', 'woolentorblocks', 90 );
} else {
	woolentorblocks();
}