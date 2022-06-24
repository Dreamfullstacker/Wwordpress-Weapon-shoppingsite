<?php
//Exit if accessed directly
if(!defined('ABSPATH')){
	return;
}
?>
<?php settings_errors(); ?>
<div class="xoo-qv-main-settings">
	<form method="POST" action="options.php" class="xoo-qv-form">
		<?php settings_fields('xoo-qv-group'); ?>
		<?php do_settings_sections('xoo_quickview'); ?>
		<?php 
		//Premium template
		include plugin_dir_path(__FILE__).'/premium/xoo-qv-premium.php' 
		?>
		<?php submit_button(); ?>
	</form>

	<div class="rate-plugin">If you like the plugin , please show your support by rating <a href="https://wordpress.org/support/view/plugin-reviews/quick-view-woocommerce" target="_blank">here.</a>
	</div>

	<div class="plugin-support">
		Use <a href="http://xootix.com/support" target="_blank">Live Chat</a> for instant support.
	</div>

</div>
<div class="xoo-qv-sidebar">
	<div class="xoo-chat">
		<span class="xoo-chhead">Need Help?</span>
		<span class="dashicons dashicons-format-chat xoo-chicon"></span>
		<span class="xoo-chtxt">Use <a href="http://xootix.com/support">Live Chat</a></span>
	</div>
	<a href="http://xootix.com/plugins" class="xoo-more-plugins">Try other awesome plugins.</a>

</div>
