<?php
if (!defined('ABSPATH')) {
	exit;
}
?>

<div class="notice notice-error">
	<p><?php esc_html_e('Gravity Forms Spam Phrases is not fully active.', 'gf-spam-phrases'); ?></p>
	<ul style="list-style:disc;padding-left: 2em">
		<?php foreach ($notices as $notice): ?>
			<li style="list-style:disc"><?php echo $notice; ?></li>
		<?php endforeach; ?>
	</ul>
</div>
