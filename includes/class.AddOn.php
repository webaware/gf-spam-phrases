<?php
namespace webaware\gf_spammy;

use GFAddOn;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * implement a Gravity Forms Add-on instance
 */
class AddOn extends GFAddOn {

	protected $blocklist = null;
	protected $found_phrase;

	/**
	 * static method for getting the instance of this singleton object
	 * @return self
	 */
	public static function get_instance() {
		static $instance = null;

		if ($instance === null) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * declare detail to GF Add-On framework
	 */
	public function __construct() {
		$this->_version						= GF_SPAMMY_VERSION;
		$this->_min_gravityforms_version	= MIN_VERSION_GF;
		$this->_slug						= 'gf-spam-phrases';
		$this->_path						= GF_SPAMMY_NAME;
		$this->_full_path					= GF_SPAMMY_FILE;
		$this->_title						= 'Spam Phrases';			// NB: no localisation yet
		$this->_short_title					= 'Spam Phrases';			// NB: no localisation yet

		// define capabilities in case role/permissions have been customised (e.g. Members plugin)
		$this->_capabilities_settings_page	= 'gravityforms_edit_settings';
		$this->_capabilities_form_settings	= 'gravityforms_edit_forms';
		$this->_capabilities_uninstall		= 'gravityforms_uninstall';

		parent::__construct();

		add_action('init', [$this, 'lateLocalise'], 9);							// priority 9 to get in before init_admin()
		add_filter('gform_entry_is_spam', [$this, 'entryCheckSpam'], 10, 3);	// priority 50 to let other plugins check first
		add_filter('gform_notes_avatar', [$this, 'notes_avatar'], 10, 2);
	}

	/**
	 * late localisation of strings, after load_plugin_textdomain() has been called
	 */
	public function lateLocalise() {
		$this->_title			= esc_html_x('Spam Phrases', 'add-on full title', 'gf-spam-phrases');
		$this->_short_title		= esc_html_x('Spam Phrases', 'add-on short title', 'gf-spam-phrases');
	}

	/**
	 * null the add-on framework load of text domain, because we already did it, thanks.
	 */
	public function load_text_domain() { }

	/**
	 * set icon for settings page in GF < 2.5
	 * @return string
	 */
	public function plugin_settings_icon() {
		return $this->get_svg_icon();
	}

	/**
	 * set the icon for the settings page menu in GF >= 2.5
	 * @return string
	 */
	public function get_menu_icon() {
		return $this->get_svg_icon();
	}

	/**
	 * get SVG icon used for plugin
	 * @return string
	 */
	protected function get_svg_icon() {
		return file_get_contents(GF_SPAMMY_ROOT . '/static/images/menu-icon.svg');
	}

	/**
	 * set the entry note avatar image
	 * @return string
	 */
	public function note_avatar() {
		return plugins_url('static/images/menu-icon.svg', GF_SPAMMY_FILE);
	}

	/**
	 * specify the settings fields to be rendered on the plugin settings page
	 * @return array
	 */
	public function plugin_settings_fields() {
		$settings = [

			[
				'fields'					=> [

					[
						'name'				=> 'blocklist',
						'label'				=> _x('Block list', 'feed field name', 'gf-spam-phrases'),
						'description'		=> __('List words or phrases one per line. URLs are accepted too. Try not to catch things that might not be spam!', 'gf-spam-phrases'),
						'type'				=> 'textarea',
					],

				],
			],

		];

		return $settings;
	}

	/**
	 * check a form entry for spam using WordPress Comments moderation list
	 * @param bool $is_spam
	 * @param array $form
	 * @param array $entry
	 * @return bool
	 */
	public function entryCheckSpam($is_spam, $form, $entry) {
		// if it's already been marked as spam, go no further
		if ($is_spam) {
			return $is_spam;
		}

		// cache exploded blocklist as an array
		if ($this->blocklist === null) {
			$blocklist = trim($this->get_plugin_setting('blocklist'));
			$blocklist = str_replace(["\r\n", "\r"], "\n", $blocklist);
			$blocklist = explode("\n", $blocklist);
			$this->blocklist = array_unique(array_filter($blocklist, 'trim'));
		}

		// check fields for matching spammy phrases
		foreach ($form['fields'] as $field) {
			if ($field->is_administrative()) {
				continue;
			}

			// check value for spammy phrases, stop looking if spam found
			if ($this->isSpammy($field, $entry)) {
				$is_spam = true;
				add_action('gform_entry_created', [$this, 'addNote']);
				break;
			}
		}

		return $is_spam;
	}

	/**
	 * check a field value for spammy phrases
	 * @param GF_Field $field
	 * @param array $entry
	 * @return bool
	 */
	protected function isSpammy($field, $entry) {
		// get single value for field (including compound fields), short-circuit if empty
		$value = $field->get_value_export($entry);
		if ($value === '') {
			return false;
		}

		// check value for any of the phrases in the blocklist
		$cleaned = wp_strip_all_tags($value);
		foreach ($this->blocklist as $s) {
			if (stripos($cleaned, $s) !== false) {
				$this->log_debug("form: {$entry['form_id']}, entry: {$entry['id']}, field: {$field->id}");
				$this->log_debug("found: $s");
				$this->log_debug("value: $value");
				$this->found_phrase = $s;
				return true;
			}
		}

		return false;
	}

	/**
	 * add a note to the entry to show that it was marked as spam by this add-on
	 * @param array $entry
	 */
	public function addNote($entry) {
		if (rgar($entry, 'status') === 'spam' && $this->found_phrase) {
			/* translators: %s = the suspected spam phrase found in the email */
			$msg = sprintf(__('Detected spam phrase: "%s"', 'gf-spam-phrases'), $this->found_phrase);
			$this->add_note($entry['id'], $msg, 'success');
		}
	}

}
