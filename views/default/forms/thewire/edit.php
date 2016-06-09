<?php
/**
 * Wire edit form body
 *
 * @uses $vars['post']
 */

elgg_load_js('elgg.thewire');
elgg_require_js('thewire/thewire_edit');

$post = elgg_extract('post', $vars);

if ($post ) {
	$description = $post ->description;
	$guid = $post ->guid;
	$access_id = $post ->access_id;
}

$char_limit = (int)elgg_get_plugin_setting('limit', 'thewire');

$text = elgg_echo('thewire:save');

$chars_left = elgg_echo('thewire:charleft');
$count_down = "<span>$char_limit</span> $chars_left";
$num_lines = 2;
if ($char_limit == 0) {
	$num_lines = 3;
	$count_down = '';
} else if ($char_limit > 140) {
	$num_lines = 3;
}

$post_input = elgg_view('input/plaintext', array(
	'name' => 'body',
	'class' => 'mtm',
	'id' => 'thewire-edit-textarea',
	'rows' => $num_lines,
	'data-max-length' => $char_limit,
	'value' => $description,
));

$access_button = elgg_view('input/access', array(
	'name' => 'access_id',
	'value' => $access_id,
	'id' => 'thewire-access',
));

$guid_input = elgg_view('input/hidden', array(
		'name' => 'guid',
		'value' => $guid,
));

$submit_button = elgg_view('input/submit', array(
	'value' => $text,
	'id' => 'thewire-submit-button',
));

echo <<<HTML
	$post_input
<div id="thewire-characters-remaining">
	$count_down
</div>
$access_button
<div class="elgg-foot mts">
	$guid_input
	$submit_button
</div>
HTML;
