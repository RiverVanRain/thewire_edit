<?php
/**
 * Wire edit form body
 *
 * @uses $vars['post']
 */

elgg_load_js('elgg.thewire');

$post = elgg_extract('post', $vars);

if ($post ) {
	$description = $post ->description;
	$guid = $post ->guid;
} 

$text = elgg_echo('thewire:save');
echo elgg_view('input/plaintext', array(
	'name' => 'body',
	'class' => 'mtm',
	'id' => 'thewire-textarea',
	'value' => $description,
));
?>
<div id="thewire-characters-remaining">
	<span>140</span> <?php echo elgg_echo('thewire:charleft'); ?>
</div>

<div class="elgg-foot mts">
<?php
echo elgg_view('input/hidden', array(
		'name' => 'guid',
		'value' => $guid,
));
echo elgg_view('input/submit', array(
	'value' => $text,
	'id' => 'thewire-submit-button',
));
?>
</div>