<?php
namespace WooLentorBlocks;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load general WP action hook
 */
class Scripts {

	/**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Filter]
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
		add_action( 'enqueue_block_assets', [ $this, 'block_assets' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'block_editor_assets' ] );
		add_action('wp_head', [ $this, 'block_attribute_css' ], 100 );
		// $this->generater_css_file();
	}

	/**
	 * Block assets.
	 */
	public function block_assets() {
		
		wp_enqueue_script(
		    'woolentor-block-main',
		    WOOLENTOR_BLOCK_URL . '/src/assets/js/script.js',
		    array(),
		    WOOLENTOR_VERSION,
		    true
		);

		wp_enqueue_style(
		    'woolentor-block-style',
		    WOOLENTOR_BLOCK_URL . '/src/assets/css/style-index.css',
		    array(),
		    WOOLENTOR_VERSION
		);

		if ( is_singular() && has_blocks() ){
			$this->load_css();
		}
		elseif( woolentorBlocks_is_gutenberg_page() ){
			$this->load_css();
		}

		/**
		 * Localize data
		 */
		wp_localize_script( 'woolentor-blocks', 'woolentorData', array(
			'url' 		=> WOOLENTOR_BLOCK_URL,
			'ajax' 		=> admin_url('admin-ajax.php'),
			'security' 	=> wp_create_nonce('woolentorblock-nonce'),
			'options'	=> Blocks_init::$blocksList,
		));

	}

	/**
	 * Load Css File
	 */
	public function load_css(){
		wp_enqueue_style( 'woolentor-block-style-css', WOOLENTOR_BLOCK_URL . '/build/styles/blocks.style.build.css', array(), WOOLENTOR_VERSION );
	}
	/**
	 * Block editor assets.
	 */
	public function block_editor_assets() {

		$styles  =  class_exists('\WooLentor\Assets_Management') ? \WooLentor\Assets_Management::instance()->get_styles() : array();
		$scripts  = class_exists('\WooLentor\Assets_Management') ? \WooLentor\Assets_Management::instance()->get_scripts() : array();

		// Register Styles
        foreach ( $styles as $handle => $style ) {
            $deps = ( isset( $style['deps'] ) ? $style['deps'] : false );
            wp_register_style( $handle, $style['src'], $deps, $style['version'] );
        }

		// Register Scripts
        foreach ( $scripts as $handle => $script ) {
            $deps = ( isset( $script['deps'] ) ? $script['deps'] : false );
            wp_register_script( $handle, $script['src'], $deps, $script['version'], true );
        }

        wp_enqueue_style( 'font-awesome-four' );
		wp_enqueue_style( 'htflexboxgrid' );
		wp_enqueue_style( 'simple-line-icons' );
		wp_enqueue_style( 'slick' );

		$dependencies = require_once( WOOLENTOR_BLOCK_PATH . '/build/blocks-woolentor.asset.php' );
		wp_enqueue_script(
		    'woolentor-blocks',
		    WOOLENTOR_BLOCK_URL . '/build/blocks-woolentor.js',
		    $dependencies['dependencies'],
		    WOOLENTOR_VERSION,
		    true
		);

		wp_enqueue_style( 'woolentor-block-editor-style', WOOLENTOR_BLOCK_URL . '/src/assets/css/editor-style.css', false, WOOLENTOR_VERSION, 'all' );

	}

	public function generater_css_file(){
		
		global $wp_filesystem;
		if ( ! $wp_filesystem ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		$dirmkpath = trailingslashit( WOOLENTOR_BLOCK_DIR ) . 'build/styles/';
		WP_Filesystem( false, WOOLENTOR_BLOCK_DIR, true );
		if( ! $wp_filesystem->is_dir( $dirmkpath ) ) {
			$wp_filesystem->mkdir( $dirmkpath );
		}

		$styleFile = fopen( WOOLENTOR_BLOCK_DIR . '/build/styles/blocks.style.build.css', 'w' );

		$blockList = Blocks_init::block_list();
		$blockDir = WOOLENTOR_BLOCK_URL . '/src/blocks/';

		foreach ( $blockList as $key => $block ) {

			$blockDirName = str_replace('woolentor/', '', trim(preg_replace('/\(.+\)/', '', $block['name'])));
			$src = $blockDir . $blockDirName . '/style.css';

			$css  = file_get_contents( $src );
			$css .= sprintf( '%s /** Block: %s Style End **/', "\n", $blockDirName );

			fwrite( $styleFile, $css."\n" );
			
		}
		fclose( $styleFile );

	}

	public function block_attribute_css(){

		$get_blocks = array_unique( woolentorBlocks_get_blocks(), SORT_REGULAR );
		$blockList = Blocks_init::block_list();
    	$blocks_css = "";

		foreach( $get_blocks as $block ){

			$blockName = str_replace('woolentor/', '', trim(preg_replace('/\(.+\)/', '', $block['blockName'])));

			if( isset( $blockName ) && isset( $block['attrs']['blockUniqId'] ) && $block['attrs']['blockUniqId'] != '' ){

				$uniqClass = 'woolentorblock-'.$block['attrs']['blockUniqId'];
				
				switch ( $block['blockName'] ){

					case 'woolentor/brand-logo':

						$singleItemAreaBorderType = woolentorBlocks_generate_css( $block['attrs'], 'singleItemAreaBorderType', 'border-style' );
						$singleItemAreaBorderWidth = woolentorBlocks_Dimention_Control( $block['attrs'], 'singleItemAreaBorderWidth', 'border-width' );
						$singleItemAreaBorderRadius = woolentorBlocks_Dimention_Control( $block['attrs'], 'singleItemAreaBorderRadius', 'border-radius' );
						$singleItemAreaMargin = woolentorBlocks_Dimention_Control( $block['attrs'], 'singleItemAreaMargin', 'margin' );
						$singleItemAreaPadding = woolentorBlocks_Dimention_Control( $block['attrs'], 'singleItemAreaPadding', 'padding' );
						$brandAlignment = woolentorBlocks_generate_css( $block['attrs'], 'brandAlignment', 'text-align' );
						$singleItemAreaBorderColor = woolentorBlocks_generate_css( $block['attrs'], 'singleItemAreaBorderColor', 'border-color' );

						$brandImageBorderType = woolentorBlocks_generate_css( $block['attrs'], 'brandImageBorderType', 'border-style' );
						$brandImageBorderWidth = woolentorBlocks_Dimention_Control( $block['attrs'], 'brandImageBorderWidth', 'border-width' );
						$brandImageBorderRadius = woolentorBlocks_Dimention_Control( $block['attrs'], 'brandImageBorderRadius', 'border-radius' );
						$brandImageBorderColor = woolentorBlocks_generate_css( $block['attrs'], 'brandImageBorderColor', 'border-color' );

						$itemSpace = ! isset( $block['attrs']['noGutter'] ) ? ( isset( $block['attrs']['itemSpace'] ) ? $block['attrs']['itemSpace'] : 15 ) : '0';

						$blocks_css .= "
							.{$uniqClass} .wl-single-brand{
								{$singleItemAreaBorderType}
								{$singleItemAreaBorderWidth}
								{$singleItemAreaBorderRadius}
								{$singleItemAreaMargin}
								{$singleItemAreaPadding}
								{$brandAlignment}
								{$singleItemAreaBorderColor}
							}
							.{$uniqClass} .wl-single-brand img{
								{$brandImageBorderType}
								{$brandImageBorderWidth}
								{$brandImageBorderRadius}
								{$brandImageBorderColor}
							}
							.{$uniqClass} .woolentor-row > [class*='woolentor-col-']{
								padding: 0  {$itemSpace}px;
							}
						";
						break;

					case 'woolentor/category-grid':

						$itemMarginBottom = isset( $block['attrs']['itemSpace'] ) ? ( $block['attrs']['itemSpace'] + $block['attrs']['itemSpace'] ) : 0;
						$areaPadding = woolentorBlocks_Dimention_Control( $block['attrs'], 'areaPadding', 'padding' );
						$areaBackground = woolentorBlocks_generate_css( $block['attrs'], 'areaBackgroundColor', 'background-color' );

						$imageBoxColor = woolentorBlocks_generate_css( $block['attrs'], 'imageBoxColor', 'border-color' );
						$imageMargin = woolentorBlocks_Dimention_Control( $block['attrs'], 'imageMargin', 'margin' );
						$imageBorderRadius = woolentorBlocks_Dimention_Control( $block['attrs'], 'imageBorderRadius', 'border-radius' );

						$titleColor = woolentorBlocks_generate_css( $block['attrs'], 'titleColor', 'color' );
						$titleHoverColor = woolentorBlocks_generate_css( $block['attrs'], 'titleHoverColor', 'color' );
						$titleMargin = woolentorBlocks_Dimention_Control( $block['attrs'], 'titleMargin', 'margin' );

						$countColor = woolentorBlocks_generate_css( $block['attrs'], 'countColor', 'color' );
						$countBeforeColor = woolentorBlocks_generate_css( $block['attrs'], 'countBeforeColor', 'background-color' );

						$itemSpace = isset( $block['attrs']['itemSpace'] ) ? $block['attrs']['itemSpace'] : 15;

						$blocks_css .= "
							.{$uniqClass} .woolentor-row:not(.wlno-gutters) > [class*='woolentor-col-']{
								padding: 0  {$itemSpace}px;
							}
							.{$uniqClass} .woolentor-row:not(.wlno-gutters) > [class*='woolentor-col-'].woolentor_margin_top{
								margin-top: {$itemMarginBottom}px;
							}
							.{$uniqClass} [class*='ht-category-wrap']{
								{$areaPadding}
								{$areaBackground}
							}
							.{$uniqClass} .ht-category-wrap .ht-category-image a.ht-category-border::before,.{$uniqClass} .ht-category-wrap-2:hover::before,.{$uniqClass} .ht-category-wrap .ht-category-image a.ht-category-border-2::before{
								{$imageBoxColor}
							}
							.{$uniqClass} [class*='ht-category-wrap'] [class*='ht-category-image']{
								{$imageMargin}
							}
							.{$uniqClass} .ht-category-wrap .ht-category-image, {$uniqClass} .ht-category-wrap .ht-category-image a.ht-category-border::before,{$uniqClass} [class*='ht-category-wrap'] [class*='ht-category-image-']{
								{$imageBorderRadius}
							}
							.{$uniqClass} [class*='ht-category-wrap'] [class*='ht-category-content'] h3 a{
								{$titleColor}
								{$titleMargin}
							}
							.{$uniqClass} [class*='ht-category-wrap'] [class*='ht-category-content'] h3 a:hover{
								{$titleHoverColor}
							}
							.{$uniqClass} .ht-category-wrap [class*='ht-category-content'] span{
								{$countColor}
							}
							.{$uniqClass} .ht-category-wrap [class*='ht-category-content'] span::before{
								{$countBeforeColor}
							}
						";
						break;
					
					case 'woolentor/image-marker':

						$markerColor = woolentorBlocks_generate_css( $block['attrs'], 'markerColor', 'color' );
						$markerBGColor = woolentorBlocks_generate_css( $block['attrs'], 'markerBGColor', 'background-color' );
						$markerBorderColor = woolentorBlocks_generate_css( $block['attrs'], 'markerBorderColor', 'border-color' );
						$markerBorderRadius = woolentorBlocks_Dimention_Control( $block['attrs'], 'markerBorderRadius', 'border-radius' );
						$markerPadding = woolentorBlocks_Dimention_Control( $block['attrs'], 'markerPadding', 'padding' );

						$markerContentBGColor = woolentorBlocks_generate_css( $block['attrs'], 'markerContentBGColor', 'background-color' );
						$markerContentBorderRadius = woolentorBlocks_Dimention_Control( $block['attrs'], 'markerContentBorderRadius', 'border-radius' );
						$markerContentPadding = woolentorBlocks_Dimention_Control( $block['attrs'], 'markerContentPadding', 'padding' );

						$markerTitleColor = woolentorBlocks_generate_css( $block['attrs'], 'markerTitleColor', 'color' );
						$markerTitleSize = woolentorBlocks_generate_css( $block['attrs'], 'markerTitleSize', 'font-size' );
						$markerTitleMargin = woolentorBlocks_Dimention_Control( $block['attrs'], 'markerTitleMargin', 'margin' );

						$markerDescriptionColor = woolentorBlocks_generate_css( $block['attrs'], 'markerDescriptionColor', 'color' );
						$markerDescriptionSize = woolentorBlocks_generate_css( $block['attrs'], 'markerDescriptionSize', 'font-size' );
						$markerDescriptionMargin = woolentorBlocks_Dimention_Control( $block['attrs'], 'markerDescriptionMargin', 'margin' );

						$blocks_css .= "
							.{$uniqClass} .wlb_image_pointer::before{
								{$markerColor}
							}
							.{$uniqClass} .wlb_image_pointer{
								{$markerBGColor}
								{$markerBorderColor}
								{$markerBorderRadius}
								{$markerPadding}
							}
							.{$uniqClass} .wlb_image_pointer .wlb_pointer_box{
								{$markerContentBGColor}
								{$markerContentBorderRadius}
								{$markerContentPadding}
							}
							.{$uniqClass} .wlb_image_pointer .wlb_pointer_box h4{
								{$markerTitleColor}
								{$markerTitleSize}
								{$markerTitleMargin}
							}
							.{$uniqClass} .wlb_image_pointer .wlb_pointer_box p{
								{$markerDescriptionColor}
								{$markerDescriptionSize}
								{$markerDescriptionMargin}
							}
						";
						break;

					case 'woolentor/special-day-offer':
						$badgeHorizontalPos = woolentorBlocks_generate_css( $block['attrs'], 'badgeHorizontalPos', 'left', '%' );
						$badgeVerticlePos 	= woolentorBlocks_generate_css( $block['attrs'], 'badgeVerticlePos', 'top', '%' );

						$contentAlignment = woolentorBlocks_generate_css( $block['attrs'], 'contentAlignment', 'text-align' );
						$contentPadding = woolentorBlocks_Dimention_Control( $block['attrs'], 'contentAreaPadding', 'padding' );
						$contentMargin = woolentorBlocks_Dimention_Control( $block['attrs'], 'contentAreaMargin', 'margin' );

						$titleColor = woolentorBlocks_generate_css( $block['attrs'], 'titleColor', 'color' );
						$titleSize = woolentorBlocks_generate_css( $block['attrs'], 'titleSize', 'font-size' );
						$titleMargin = woolentorBlocks_Dimention_Control( $block['attrs'], 'titleMargin', 'margin' );
						$titlePadding = woolentorBlocks_Dimention_Control( $block['attrs'], 'titlePadding', 'padding' );

						$titleSubColor = woolentorBlocks_generate_css( $block['attrs'], 'titleSubColor', 'color' );
						$titleSubSize = woolentorBlocks_generate_css( $block['attrs'], 'titleSubSize', 'font-size' );
						$subTitleMargin = woolentorBlocks_Dimention_Control( $block['attrs'], 'subTitleMargin', 'margin' );
						$subTitlePadding = woolentorBlocks_Dimention_Control( $block['attrs'], 'subTitlePadding', 'padding' );

						$desColor = woolentorBlocks_generate_css( $block['attrs'], 'desColor', 'color' );
						$desSize = woolentorBlocks_generate_css( $block['attrs'], 'desSize', 'font-size' );
						$desMargin = woolentorBlocks_Dimention_Control( $block['attrs'], 'desMargin', 'margin' );
						$desPadding = woolentorBlocks_Dimention_Control( $block['attrs'], 'desPadding', 'padding' );

						$offerColor = woolentorBlocks_generate_css( $block['attrs'], 'offerColor', 'color' );
						$offerSize = woolentorBlocks_generate_css( $block['attrs'], 'offerSize', 'font-size' );
						$offerMargin = woolentorBlocks_Dimention_Control( $block['attrs'], 'offerMargin', 'margin' );

						$offerTagColor = woolentorBlocks_generate_css( $block['attrs'], 'offerTagColor', 'color' );
						$offerTagSize = woolentorBlocks_generate_css( $block['attrs'], 'offerTagSize', 'font-size' );
						$offerTagMargin = woolentorBlocks_Dimention_Control( $block['attrs'], 'offerTagMargin', 'margin' );

						$buttonColor = woolentorBlocks_generate_css( $block['attrs'], 'buttonColor', 'color' );
						$buttonHoverColor = woolentorBlocks_generate_css( $block['attrs'], 'buttonHoverColor', 'color' );
						$buttonSize = woolentorBlocks_generate_css( $block['attrs'], 'buttonSize', 'font-size' );
						$buttonMargin = woolentorBlocks_Dimention_Control( $block['attrs'], 'buttonMargin', 'margin' );

						$blocks_css .= "
							.{$uniqClass} .banner-content{
								{$contentAlignment}
								{$contentPadding}
								{$contentMargin}
							}
							.{$uniqClass} .wlbanner-badgeimage{
								{$badgeHorizontalPos}
								{$badgeVerticlePos}
							}
							.{$uniqClass} .banner-content h2{
								{$titleColor}
								{$titleSize}
								{$titleMargin}
								{$titlePadding}
							}
							.{$uniqClass} .banner-content h6{
								{$titleSubColor}
								{$titleSubSize}
								{$subTitleMargin}
								{$subTitlePadding}
							}
							.{$uniqClass} .banner-content p{
								{$desColor}
								{$desSize}
								{$desMargin}
								{$desPadding}
							}
							.{$uniqClass} .banner-content h5{
								{$offerColor}
								{$offerSize}
								{$offerMargin}
							}
							.{$uniqClass} .banner-content h5 span{
								{$offerTagColor}
								{$offerTagSize}
								{$offerTagMargin}
							}
							.{$uniqClass} .banner-content a{
								{$buttonColor}
								{$buttonSize}
								{$buttonMargin}
							}
							.{$uniqClass} .banner-content a:hover{
								{$buttonHoverColor}
							}
						";
						break;

					case 'woolentor/store-feature':

						$areaBorderColor = woolentorBlocks_generate_css( $block['attrs'], 'areaBorderColor', 'border-color' );
						$areaHoverBorderColor = woolentorBlocks_generate_css( $block['attrs'], 'areaHoverBorderColor', 'border-color', ' !important' );
						$areaBackgroundColor = woolentorBlocks_generate_css( $block['attrs'], 'areaBackgroundColor', 'background-color' );
						$areaMargin = woolentorBlocks_Dimention_Control( $block['attrs'], 'areaMargin', 'margin' );
						$areaPadding = woolentorBlocks_Dimention_Control( $block['attrs'], 'areaPadding', 'padding' );

						$titleColor = woolentorBlocks_generate_css( $block['attrs'], 'titleColor', 'color' );
						$titleSize = woolentorBlocks_generate_css( $block['attrs'], 'titleSize', 'font-size' );
						$titleMargin = woolentorBlocks_Dimention_Control( $block['attrs'], 'titleMargin', 'margin' );

						$subTitleColor = woolentorBlocks_generate_css( $block['attrs'], 'subTitleColor', 'color' );
						$subTitleSize = woolentorBlocks_generate_css( $block['attrs'], 'subTitleSize', 'font-size' );
						$subTitleMargin = woolentorBlocks_Dimention_Control( $block['attrs'], 'subTitleMargin', 'margin' );

						$blocks_css .= "

							.{$uniqClass}.ht-feature-wrap{
								{$areaBackgroundColor}
							}
							.{$uniqClass}.ht-feature-wrap .ht-feature-inner{
								{$areaBorderColor}
								{$areaMargin}
								{$areaPadding}
							}
							.{$uniqClass}.ht-feature-wrap:hover .ht-feature-inner{
								{$areaHoverBorderColor}
							}
							.{$uniqClass}.ht-feature-wrap .ht-feature-content h4{
								{$titleColor}
								{$titleSize}
								{$titleMargin}
							}
							.{$uniqClass}.ht-feature-wrap .ht-feature-content p{
								{$subTitleColor}
								{$subTitleSize}
								{$subTitleMargin}
							}

						";
						break;
					
					case 'woolentor/product-tab':

						$titleColor = woolentorBlocks_generate_css( $block['attrs'], 'titleColor', 'color' );
						$titleHoverColor = woolentorBlocks_generate_css( $block['attrs'], 'titleHoverColor', 'color' );
						$titleAlignment = woolentorBlocks_generate_css( $block['attrs'], 'titleAlign', 'text-align' );

						$priceColor = woolentorBlocks_generate_css( $block['attrs'], 'priceColor', 'color' );
						$contentAlign = woolentorBlocks_generate_css( $block['attrs'], 'contentAlign', 'text-align' );

						$actionBtnColor = woolentorBlocks_generate_css( $block['attrs'], 'actionBtnColor', 'color' );
						$actionBtnBgColor = woolentorBlocks_generate_css( $block['attrs'], 'actionBtnBgColor', 'background-color' );
						$actionBtnHoverColor = woolentorBlocks_generate_css( $block['attrs'], 'actionBtnHoverColor', 'color' );
						$actionBtnHoverBgColor = woolentorBlocks_generate_css( $block['attrs'], 'actionBtnHoverBgColor', 'background-color' );

						$blocks_css .= "
							.{$uniqClass} .product-item .product-inner .content .title a{
								{$titleColor}
							}
							.{$uniqClass} .product-item .product-inner .content .title a:hover{
								{$titleHoverColor}
							}
							.{$uniqClass} .product-item .product-inner .content .title{
								{$titleAlignment}
							}
							.{$uniqClass} .product-item .product-inner .content .price,.{$uniqClass} .product-item .product-inner .content .price .amount{
								{$priceColor}
							}
							.{$uniqClass} .product-item .product-inner .content{
								{$contentAlign}
							}
							.{$uniqClass} .product-item .actions a, .{$uniqClass} .product-item .woocommerce.compare-button a.button, .{$uniqClass} .product-item .actions a::before{
								{$actionBtnColor}
							}
							.{$uniqClass} .product-item .actions{
								{$actionBtnBgColor}
							}
							.{$uniqClass} .product-item .actions a:hover, .{$uniqClass} .product-item .woocommerce.compare-button a.button:hover, .{$uniqClass} .product-item .actions a:hover::before{
								{$actionBtnHoverColor}
							}
							.{$uniqClass} .product-item .actions:hover{
								{$actionBtnHoverBgColor}
							}
						";
						break;
					
					case 'woolentor/faq':

						$headBGColor = woolentorBlocks_generate_css( $block['attrs'], 'headBackgroundColor', 'background-color' );
						$titleBorderStyle = woolentorBlocks_generate_css( $block['attrs'], 'titleBorderType', 'border-style' );
						$titleBorderWidth = woolentorBlocks_Dimention_Control( $block['attrs'], 'titleBorderWidth', 'border-width' );
						$titleBorderColor = woolentorBlocks_generate_css( $block['attrs'], 'titleBorderColor', 'border-color' );
						$titleBorderRadius = woolentorBlocks_Dimention_Control( $block['attrs'], 'titleBorderRadius', 'border-radius' );
						$titleColor = woolentorBlocks_generate_css( $block['attrs'], 'faqTitleColor', 'color' );
						$titleSize = woolentorBlocks_generate_css( $block['attrs'], 'titleSize', 'font-size' );
						$iconColor = woolentorBlocks_generate_css( $block['attrs'], 'iconColor', 'background-color', '!important' );
						$contentColor = woolentorBlocks_generate_css( $block['attrs'], 'contentColor', 'color' );
						$contentSize = woolentorBlocks_generate_css( $block['attrs'], 'contentSize', 'font-size' );

						$headActiveBGColor = woolentorBlocks_generate_css( $block['attrs'], 'activeHeadBackgroundColor', 'background-color' );
						$titleActiveColor = woolentorBlocks_generate_css( $block['attrs'], 'activeFaqTitleColor', 'color' );
						$iconActiveColor = woolentorBlocks_generate_css( $block['attrs'], 'activeIconColor', 'background-color', '!important' );

						$blocks_css .= "
							.{$uniqClass} .htwoolentor-faq-head{
								{$headBGColor}
								{$titleBorderStyle}
								{$titleBorderWidth}
								{$titleBorderColor}
								{$titleBorderRadius}
							}
							.{$uniqClass} .htwoolentor-faq-head-text{
								{$titleColor}
								{$titleSize}
							}
							.{$uniqClass} .htwoolentor-faq-head-indicator:before,.{$uniqClass} .htwoolentor-faq-head-indicator:after{
								{$iconColor}
							}
							.{$uniqClass} .htwoolentor-faq-content{
								{$contentColor}
							}
							.{$uniqClass} .htwoolentor-faq-content p,.{$uniqClass} .htwoolentor-faq-content{
								{$contentSize}
							}

							.{$uniqClass} .is-active .htwoolentor-faq-head{
								{$headActiveBGColor}
							}
							.{$uniqClass} .is-active .htwoolentor-faq-head-text{
								{$titleActiveColor}
							}
							.{$uniqClass} .is-active .htwoolentor-faq-head-indicator:before,.{$uniqClass} .is-active .htwoolentor-faq-head-indicator:after{
								{$iconActiveColor}
							}

						";

						break;

					case 'woolentor/product-curvy':

						$itemSpace = isset( $block['attrs']['itemSpace'] ) ? $block['attrs']['itemSpace'] : 15;
						$itemMarginBottom = isset( $block['attrs']['itemMarginBottom'] ) ? $block['attrs']['itemMarginBottom'] : 15;

						$areaMargin = woolentorBlocks_Dimention_Control( $block['attrs'], 'areaMargin', 'margin' );
						$areaPadding = woolentorBlocks_Dimention_Control( $block['attrs'], 'areaPadding', 'padding' );
						$backgroundImage = woolentorBlocks_Background_Control( $block['attrs'], 'areaBGProperty' );

						$itemAreaBGColor = woolentorBlocks_generate_css( $block['attrs'], 'itemAreaBGColor', 'background-color' );

						$titleSize = woolentorBlocks_generate_css( $block['attrs'], 'titleSize', 'font-size', '!important' );
						$titleColor = woolentorBlocks_generate_css( $block['attrs'], 'titleColor', 'color', '!important' );
						$titleHoverColor = woolentorBlocks_generate_css( $block['attrs'], 'titleHoverColor', 'color', '!important' );
						$titleMargin = woolentorBlocks_Dimention_Control( $block['attrs'], 'titleMargin', 'margin', '!important' );

						$salePriceColor = woolentorBlocks_generate_css( $block['attrs'], 'salePriceColor', 'color', '!important' );
						$regulerPriceColor = woolentorBlocks_generate_css( $block['attrs'], 'regulerPriceColor', 'color', '!important' );

						$cotentColor = woolentorBlocks_generate_css( $block['attrs'], 'cotentColor', 'color' );
						$contentSize = woolentorBlocks_generate_css( $block['attrs'], 'contentSize', 'font-size' );
						$contentMargin = woolentorBlocks_Dimention_Control( $block['attrs'], 'contentMargin', 'margin' );

						$emptyRatingColor = woolentorBlocks_generate_css( $block['attrs'], 'emptyRatingColor', 'color' );
						$ratingColor = woolentorBlocks_generate_css( $block['attrs'], 'ratingColor', 'color' );
						$ratingMargin = woolentorBlocks_Dimention_Control( $block['attrs'], 'ratingMargin', 'margin' );

						$actionBtnAreaBGColor = woolentorBlocks_generate_css( $block['attrs'], 'actionBtnAreaBGColor', 'background-color', '!important' );
						$actionBtnColor = woolentorBlocks_generate_css( $block['attrs'], 'actionBtnColor', 'color', '!important' );
						$actionBtnBGColor = woolentorBlocks_generate_css( $block['attrs'], 'actionBtnBGColor', 'background-color', '!important' );
						$actionBtnHoverColor = woolentorBlocks_generate_css( $block['attrs'], 'actionBtnHoverColor', 'color', '!important' );
						$actionBtnBgHoverColor = woolentorBlocks_generate_css( $block['attrs'], 'actionBtnBgHoverColor', 'background-color', '!important' );
						$actionBtnBorderRadius = woolentorBlocks_Dimention_Control( $block['attrs'], 'actionBtnBorderRadius', 'border-radius', '!important' );

						$imageBorderColor = woolentorBlocks_generate_css( $block['attrs'], 'imageBorderColor', 'border-color', '!important' );

						$blocks_css .= "
							.{$uniqClass} {
								{$areaMargin}
								{$areaPadding}
								{$backgroundImage}
							}
							.{$uniqClass} .woolentor-row:not(.wlno-gutters) > [class*='woolentor-col-']{
								padding: 0  {$itemSpace}px;
								margin-bottom: {$itemMarginBottom}px;
							}

							.{$uniqClass} .wl_single-product-item, .{$uniqClass} .wl_single-product-item.wl_dark-item .product-content{
								{$itemAreaBGColor}
							}
							.{$uniqClass} .product-content .product-content-top .title{
								{$titleSize}
								{$titleMargin}
							}
							.{$uniqClass} .product-content .product-content-top .title a{
								{$titleColor}
							}
							.{$uniqClass} .product-content .product-content-top .title a:hover{
								{$titleHoverColor}
							}
							
							.{$uniqClass} .product-content .product-content-top .product-price{
								{$salePriceColor}
							}
							.{$uniqClass} .product-content .product-content-top .product-price del{
								{$regulerPriceColor}
							}

							.{$uniqClass} .product-content .product-content-top p{
								{$cotentColor}
								{$contentSize}
								{$contentMargin}
							}

							.{$uniqClass} .product-content .product-content-top .star-rating,.{$uniqClass} .product-content .product-content-top .star-rating::before{
								{$emptyRatingColor}
							}
							.{$uniqClass} .product-content .product-content-top .star-rating span{
								{$ratingColor}
							}
							.{$uniqClass} .product-content .product-content-top .star-rating{
								{$ratingMargin}
							}

							.{$uniqClass} .product-content .action{
								{$actionBtnAreaBGColor}
							}
							.{$uniqClass} .product-content .action li a,.{$uniqClass} .product-content .action li .woolentor-compare.compare::before{
								{$actionBtnColor}
							}
							.{$uniqClass} .product-content .action li a{
								{$actionBtnBGColor}
								{$actionBtnBorderRadius}
							}
							.{$uniqClass} .product-content .action li a:hover,.{$uniqClass} .product-content .action li .woolentor-compare.compare:hover::before{
								{$actionBtnHoverColor}
							}
							.{$uniqClass} .product-content .action li a:hover{
								{$actionBtnBgHoverColor}
							}
							.{$uniqClass} .product-thumbnail{
								{$imageBorderColor}
							}

						";
						break;

					default:
						break;

				}

			}

		}

		if( $blocks_css ) {
			echo '<style type="text/css">'.$blocks_css.'</style>';
		}

	}
	
}