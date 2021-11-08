<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * maybe show notice of minimum PHP version failure
 */
function gf_spammy_fail_php_version() {
	gf_spammy_load_text_domain();

	$requires = new GfSpammyRequires();

	$requires->addNotice(
		gf_spammy_external_link(
			/* translators: %1$s: minimum required version number, %2$s: installed version number */
			sprintf(esc_html__('It requires PHP %1$s or higher; your website has PHP %2$s which is {{a}}old, obsolete, and unsupported{{/a}}.', 'gf-spam-phrases'),
				esc_html(GF_SPAMMY_MIN_PHP), esc_html(PHP_VERSION)),
			'https://www.php.net/supported-versions.php'
		)
	);
	$requires->addNotice(
		/* translators: %s: minimum recommended version number */
		sprintf(esc_html__('Please upgrade your website hosting. At least PHP %s is recommended.', 'gf-spam-phrases'), '7.3')
	);
}

/**
 * load text translations
 */
function gf_spammy_load_text_domain() {
	load_plugin_textdomain('gf-spam-phrases', false, plugin_basename(GF_SPAMMY_ROOT . 'languages'));
}

/**
 * replace link placeholders with an external link
 * @param string $template
 * @param string $url
 * @return string
 */
function gf_spammy_external_link($template, $url) {
	$search = array(
		'{{a}}',
		'{{/a}}',
	);
	$replace = array(
		sprintf('<a rel="noopener" target="_blank" href="%s">', esc_url($url)),
		'</a>',
	);
	return str_replace($search, $replace, $template);
}
