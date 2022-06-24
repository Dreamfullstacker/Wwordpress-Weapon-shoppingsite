<?php
namespace WooLentorBlocks;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load general WP action hook
 */
class Blocks_init {


	/**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;
    public static $blocksList = [];

    /**
     * [instance] Initializes a singleton instance
     * @return [Blocks_init]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->dynamic_blocks_include();
	}

    /**
     * Load Dynamic blocks
     */
    public function dynamic_blocks_include(){

        $blockList = self::block_list();
		$blockDir = WOOLENTOR_BLOCK_PATH . '/src/blocks/';

		foreach ( $blockList as $key => $block ) {

			$blockDirName = str_replace('woolentor/', '', trim(preg_replace('/\(.+\)/', '', $block['name'])));
			$blockFilepath = $blockDir . $blockDirName . '/index.php';

            if( $block['active'] === true ){
                array_push( self::$blocksList, $blockDirName );
                if( file_exists( $blockFilepath ) ){
                    require_once ( $blockFilepath );
                }
            }
			
		}

    }

    /**
     * Block List
     */
    public static function block_list(){

        $blockList = [
            'brand_logo' => array(
                'label'  => 'Brand Logo',
                'name'   => 'woolentor/brand-logo',
                'active' => true,
            ),
            'category_grid' => array(
                'label'  => 'Category Grid',
                'name'   => 'woolentor/category-grid',
                'active' => true,
            ),
            'image_marker' => array(
                'label'  => 'Image Marker',
                'name'   => 'woolentor/image-marker',
                'active' => true,
            ),
            'special_day_offer' => array(
                'label'  => 'Special Day Offer',
                'name'   => 'woolentor/special-day-offer',
                'active' => true,
            ),
            'store_feature' => array(
                'label'  => 'Store Feature',
                'name'   => 'woolentor/store-feature',
                'active' => true,
            ),
            'product_tab' => array(
                'label'  => 'Product tab',
                'name'   => 'woolentor/product-tab',
                'active' => true,
            ),
            'promo_banner' => array(
                'label'  => 'Promo Banner',
                'name'   => 'woolentor/promo-banner',
                'active' => true,
            ),
            'faq' => array(
                'label'  => 'FAQ',
                'name'   => 'woolentor/faq',
                'active' => true,
            ),
            'product_curvy' => array(
                'label'  => 'Product Curvy',
                'name'   => 'woolentor/product-curvy',
                'active' => true,
            )
            
        ];
        return $blockList;
        
    }


}
