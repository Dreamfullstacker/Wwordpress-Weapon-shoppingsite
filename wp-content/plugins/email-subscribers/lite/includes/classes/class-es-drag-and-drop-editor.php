<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ES_Drag_And_Drop_Editor {

	public static $instance;

	public function __construct() {
		wp_register_script( 'es_editor_js', ES_PLUGIN_URL . 'lite/admin/js/editor.js', array( ), ES_PLUGIN_VERSION, true );
		wp_enqueue_script( 'es_editor_js' );
		wp_enqueue_style( 'es_editor_css', ES_PLUGIN_URL . 'lite/admin/css/editor.css', array(), ES_PLUGIN_VERSION, 'all' );

	}

	public function es_draganddrop_callback() {
		?>
		<div class="mt-6 mr-6 p-2 rounded-lg border-dashed border bg-white">
			<div class="text-xl leading-relaxed ">
				<?php esc_html_e('How to use this?', 'email-subscribers'); ?>
			</div>
			<div class="text-sm">
				<?php esc_html_e('Create the content by dragging elements displayed on the right. After you are done click on "Export HTML" ', 'email-subscribers'); ?><span title="Export HTML " class="fa fa-download"></span>
				<?php esc_html_e(' to get your html content. Use it while sending campaigns.', 'email-subscribers'); ?>
			</div>
		</div>
		<div id="ig-es-dnd-builder"></div>
	   <?php
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
