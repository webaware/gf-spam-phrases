<?php
namespace webaware\gf_spammy;

use GfSpammyRequires as Requires;
use GFAddOn;
use GFCommon;
use GFForms;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * class for managing the plugin
 */
class Plugin {

	/**
	 * static method for getting the instance of this singleton object
	 * @return self
	 */
	public static function getInstance() {
		static $instance = null;

		if ($instance === null) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * hide constructor
	 */
	private function __construct() { }

	/**
	 * hook WordPress
	 */
	public function addHooks() {
		add_action('gform_loaded', [$this, 'addonInit']);
		add_action('init', 'gf_spammy_load_text_domain', 8);			// priority 8 to get in before add-on uses translated text
		add_action('admin_init', [$this, 'checkPrerequisites']);
		add_filter('plugin_row_meta', [$this, 'pluginDetailsLinks'], 10, 2);
	}

	/**
	 * initialise the Gravity Forms add-on
	 */
	public function addonInit() {
		if (!method_exists('GFForms', 'include_feed_addon_framework')) {
			return;
		}

		if (has_required_gravityforms()) {
			// load add-on framework and hook our add-on
			GFForms::include_feed_addon_framework();

			require GF_SPAMMY_ROOT . 'includes/class.AddOn.php';
			GFAddOn::register(__NAMESPACE__ . '\\AddOn');
		}
	}

	/**
	 * check for required prerequisites, tell admin if any are missing
	 */
	public function checkPrerequisites() {
		$requires = new Requires();

		// we need Gravity Forms
		if (!class_exists('GFCommon', false)) {
			$requires->addNotice(
				gf_spammy_external_link(
					esc_html__('Requires {{a}}Gravity Forms{{/a}} to be installed and activated.', 'gf-spam-phrases'),
					'https://webaware.com.au/get-gravity-forms'
				)
			);
		}
		elseif (!has_required_gravityforms()) {
			$requires->addNotice(
				gf_spammy_external_link(
					// translators: %1$s = target version number, %2$s = current version number
					sprintf(esc_html__('Requires {{a}}Gravity Forms{{/a}} version %1$s or higher; your website has Gravity Forms version %2$s', 'gf-spam-phrases'),
						esc_html(MIN_VERSION_GF), esc_html(GFCommon::$version)),
					'https://webaware.com.au/get-gravity-forms'
				)
			);
		}
	}

	/**
	 * action hook for adding plugin details links
	 */
	public function pluginDetailsLinks($links, $file) {
		if ($file === GF_SPAMMY_NAME) {
			$links[] = sprintf('<a href="https://translate.webaware.com.au/glotpress/projects/gf-spam-phrases/" target="_blank" rel="noopener">%s</a>', esc_html_x('Translate', 'plugin details links', 'gf-spam-phrases'));
		}

		return $links;
	}

}
