<?php
/*
Plugin Name: Gravity Forms Spam Phrases
Plugin URI:
Update URI: gf-spam-phrases
Description: Create a list of spammy phrases to blocklist Gravity Forms entries as spam
Version: 1.1.0
Author: WebAware
Author URI: https://shop.webaware.com.au/
Text Domain: gf-spam-phrases
Domain Path: /languages/
*/

/*
copyright (c) 2021-2023 WebAware Pty Ltd (email : support@webaware.com.au)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if (!defined('ABSPATH')) {
	exit;
}

// phpcs:disable Modernize.FunctionCalls.Dirname.FileConstant
define('GF_SPAMMY_FILE', __FILE__);
define('GF_SPAMMY_ROOT', dirname(__FILE__) . '/');
define('GF_SPAMMY_NAME', basename(dirname(__FILE__)) . '/' . basename(__FILE__));
define('GF_SPAMMY_MIN_PHP', '7.4');
define('GF_SPAMMY_VERSION', '1.1.0');

require GF_SPAMMY_ROOT . 'includes/functions-global.php';
require GF_SPAMMY_ROOT . 'includes/class.Requires.php';

if (version_compare(PHP_VERSION, GF_SPAMMY_MIN_PHP, '<')) {
	add_action('admin_notices', 'gf_spammy_fail_php_version');
	return;
}

require GF_SPAMMY_ROOT . 'includes/bootstrap.php';
