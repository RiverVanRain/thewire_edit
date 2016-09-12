<?php
/**
 * Wire add form body
 *
 * @uses $vars['post']
 */

elgg_load_js('elgg.thewire');

$post = elgg_extract('post', $vars);
$access_id = elgg_extract('access_id', $vars, ACCESS_DEFAULT);

if (elgg_is_active_plugin('thewire_tools')) {
$char_limit = thewire_tools_get_wire_length();
$reshare = elgg_extract('reshare', $vars); // for reshare functionality
}
else {
$char_limit = (int)elgg_get_plugin_setting('limit', 'thewire');
}

$text = elgg_echo('post');
if ($post) {
	$text = elgg_echo('reply');
}
$chars_left = elgg_echo('thewire:charleft');

$parent_input = '';
if ($post) {
	$parent_input = elgg_view('input/hidden', array(
		'name' => 'parent_guid',
		'value' => $post->guid,
	));
}

if (elgg_is_active_plugin('thewire_tools')) {
	$reshare_input = '';
	$post_value = '';
	if (!empty($reshare)) {
		$reshare_input = elgg_view('input/hidden', [
			'name' => 'reshare_guid',
			'value' => $reshare->getGUID(),
		]);
		
		$reshare_input .= elgg_view('thewire_tools/reshare_source', ['entity' => $reshare]);
		
		if (!empty($reshare->title)) {
			$post_value = $reshare->title;
		} elseif (!empty($reshare->name)) {
			$post_value = $reshare->name;
		} elseif (!empty($reshare->description)) {
			$post_value = elgg_get_excerpt($reshare->description, 140);
		}
		
		$post_value = htmlspecialchars_decode($post_value, ENT_QUOTES);
	}
	
	$count_down = "<span>$char_limit</span> $chars_left";
	$num_lines = 2;
	if ($char_limit == 0) {
		$num_lines = 3;
		$count_down = '';
	} else if ($char_limit > 140) {
		$num_lines = 3;
	}

	$post_input = elgg_view('input/plaintext', [
		'name' => 'body',
		'class' => 'mtm',
		'id' => 'thewire-textarea',
		'rows' => $num_lines,
		'value' => $post_value,
		'data-max-length' => $char_limit,
	]);
}

else {
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
	'id' => 'thewire-textarea',
	'rows' => $num_lines,
	'data-max-length' => $char_limit,
));
}

$access_button = elgg_view('input/access', array(
	'name' => 'access_id',
	'value' => $access_id,
	'id' => 'thewire-access',
));

$submit_button = elgg_view('input/submit', array(
	'value' => $text,
	'id' => 'thewire-submit-button',
));

if (elgg_is_active_plugin('thewire_tools')) {
	$mentions = '';
	$access_input = '';
	if (thewire_tools_groups_enabled()) {
		
		$access_button = '';

		if ($post) {
			$access_input = elgg_view('input/hidden', [
				'name' => 'access_id',
				'value' => $post->access_id,
			]);
		} else {
			$page_owner_entity = elgg_get_page_owner_entity();

			if ($page_owner_entity instanceof ElggGroup) {
				// in a group only allow sharing in the current group
				$access_input = elgg_view('input/hidden', [
					'name' => 'access_id',
					'value' => $page_owner_entity->group_acl,
				]);
				$mentions = elgg_format_element('div', ['class' => 'elgg-subtext mbn'], elgg_echo('thewire_tools:groups:mentions'));
			} else {
				$params = ['name' => 'access_id'];
				
				if (elgg_in_context('widgets')) {
					$params['class'] = 'thewire-tools-widget-access';
				}
				
				elgg_push_context('thewire_add');
				$access_input = elgg_view('input/access', $params);
				elgg_pop_context();
			}
		}
	}

echo $reshare_input . $post_input . '<div class="thewire-characters-remaining">' . $count_down . '</div>' . $mentions . $access_button . '<div class="elgg-foot mts">' . $parent_input . $submit_button . $access_input . '</div>';

	elgg_require_js('thewire_tools/autocomplete');
	if (elgg_is_xhr()) {
		elgg_format_element('script', [], 'require["thewire_tools/autocomplete"];');
	}

}

else {
echo <<<HTML
	$post_input
<div id="thewire-characters-remaining">
	$count_down
</div>
$access_button
<div class="elgg-foot mts">
	$parent_input
	$submit_button
</div>
HTML;

}