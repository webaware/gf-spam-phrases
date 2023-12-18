<?php
namespace webaware\gf_spammy;

if (!defined('ABSPATH')) {
	exit;
}

// minimum versions required
const MIN_VERSION_GF		= '2.4.18';

/**
 * kick start the plugin
 */
add_action('plugins_loaded', function() {
	require GF_SPAMMY_ROOT . 'includes/functions.php';
	require GF_SPAMMY_ROOT . 'includes/class.Plugin.php';
	Plugin::getInstance()->addHooks();

	if (is_admin() || wp_doing_cron() || (defined('WP_CLI') && WP_CLI)) {
		require GF_SPAMMY_ROOT . 'includes/class.Updater.php';
		new Updater(GF_SPAMMY_NAME, GF_SPAMMY_FILE, 'gf-spam-phrases',
			'https://updates.webaware.net.au/gf-spam-phrases/latest.json');
	}
}, 5);
