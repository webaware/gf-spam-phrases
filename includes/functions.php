<?php
namespace webaware\gf_spammy;

use GFCommon;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * compare Gravity Forms version against target
 * @param string $target
 * @param string $operator
 * @return bool
 */
function gform_version_compare($target, $operator) {
	if (class_exists('GFCommon', false)) {
		return version_compare(GFCommon::$version, $target, $operator);
	}

	return false;
}

/**
 * test whether the minimum required Gravity Forms is installed / activated
 * @return bool
 */
function has_required_gravityforms() {
	return gform_version_compare(MIN_VERSION_GF, '>=');
}
