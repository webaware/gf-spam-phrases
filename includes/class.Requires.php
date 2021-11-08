<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * collect and display admin notices for failed prerequisites
 */
class GfSpammyRequires {

	private $notices;

	/**
	 * set up the notices container, and hook actions for displaying notices
	 * only called when a notice is being added
	 */
	protected function init() {
		if (!is_array($this->notices)) {
			$this->notices = array();

			// hook admin_notices, again! so need to hook later than 10
			add_action('admin_notices', array($this, 'maybeShowAdminNotices'), 20);

			// show Requires notices before update information, so hook earlier than 10
			add_action('after_plugin_row_' . GF_SPAMMY_NAME, array($this, 'showPluginRowNotices'), 9, 2);
		}
	}

	/**
	 * add a Requires notices
	 * @param string $notice
	 */
	public function addNotice($notice) {
		$this->init();
		$this->notices[] = $notice;
	}

	/**
	 * maybe show admin notices, if on an appropriate admin page with admin or similar logged in
	 */
	public function maybeShowAdminNotices() {
		if ($this->canShowAdminNotices()) {
			$notices = $this->notices;
			require GF_SPAMMY_ROOT . 'views/requires-admin-notice.php';
		}
	}

	/**
	 * show plugin page row with requires notices
	 */
	public function showPluginRowNotices() {
		global $wp_list_table;

		if (empty($wp_list_table)) {
			return;
		}

		$notices = $this->notices;
		require GF_SPAMMY_ROOT . 'views/requires-plugin-notice.php';
	}

	/**
	 * test whether we can show admin-related notices
	 * @return bool
	 */
	protected function canShowAdminNotices() {
		global $hook_suffix;

		// only on specific pages
		$subview = empty($_GET['subview']) ? 'settings' : $_GET['subview'];
		if ($hook_suffix !== 'forms_page_gf_settings' || $subview !== 'settings') {
			return false;
		}

		// only bother admins / plugin installers / option setters with this stuff
		if (!current_user_can('activate_plugins') && !current_user_can('manage_options')) {
			return false;
		}

		return true;
	}

}
