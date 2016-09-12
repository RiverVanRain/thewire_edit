<?php
/**
 * View a wire post
 * 
 * @uses $vars['entity']
 */

elgg_load_js('elgg.thewire');

$full = elgg_extract('full_view', $vars, FALSE);
$post = elgg_extract('entity', $vars, FALSE);

if (!$post) {
	return true;
}

// make compatible with posts created with original Curverider plugin
$thread_id = $post->wire_thread;
if (!$thread_id) {
	$post->wire_thread = $post->guid;
}

if (elgg_is_active_plugin('thewire_tools')) {
	$show_thread = false;
	if (!elgg_in_context('thewire_tools_thread') && !elgg_in_context('thewire_thread')) {
		if ($post->countEntitiesFromRelationship('parent') || $post->countEntitiesFromRelationship('parent', true)) {
			$show_thread = true;
		}
	}
}

$owner = $post->getOwnerEntity();
$container = $post->getContainerEntity();
$subtitle = [];

$owner_icon = elgg_view_entity_icon($owner, 'tiny');
$owner_link = elgg_view('output/url', [
	'href' => "thewire/owner/$owner->username",
	'text' => $owner->name,
	'is_trusted' => true,
]);
$subtitle[] = elgg_echo('byline', [$owner_link]);
$subtitle[] = elgg_view_friendly_time($post->time_created);

$metadata = elgg_view_menu('entity', [
	'entity' => $post,
	'handler' => 'thewire',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
]);

// check if need to show group
if (elgg_instanceof($container, 'group') && ($container->getGUID() != elgg_get_page_owner_guid())) {
	$group_link = elgg_view('output/url', [
		'href' => "thewire/group/{$container->getGUID()}",
		'text' => $container->name,
		'class' => 'thewire_tools_object_link',
	]);
	
	$subtitle[] = elgg_echo('river:ingroup', [$group_link]);
}

// show text different in widgets
$text = $post->description;
if (elgg_in_context('widgets')) {
	$text = elgg_get_excerpt($text, 140);
	
	// show more link?
	if (substr($text, -3) == '...') {
		$text .= elgg_view('output/url', [
			'text' => elgg_echo('more'),
			'href' => $post->getURL(),
			'is_trusted' => true,
			'class' => 'mlm',
		]);
	}
}

$content = thewire_filter($text);

if (elgg_is_active_plugin('thewire_tools')) {
$content = thewire_tools_filter($text);

// check for reshare entity
$reshare = $post->getEntitiesFromRelationship(['relationship' => 'reshare', 'limit' => 1]);
if (!empty($reshare)) {
	$content .= elgg_format_element('div', ['class' => 'elgg-divide-left pls'], elgg_view('thewire_tools/reshare_source', ['entity' => $reshare[0]]));
}

if (elgg_is_logged_in() && !elgg_in_context('thewire_tools_thread')) {
	$form_vars = [
		'id' => 'thewire-tools-reply-' . $post->getGUID(),
		'class' => 'hidden',
	];
	$content .= elgg_view_form('thewire/add', $form_vars, ['post' => $post]);
}
}

$params = [
	'entity' => $post,
	'metadata' => $metadata,
	'subtitle' => implode(' ', $subtitle),
	'content' => $content,
	'tags' => false,
];
$params = $params + $vars;
$list_body = elgg_view('object/elements/summary', $params);

if ($post->canEdit()) {
	$form_vars = array('class' => 'thewire-form hidden', 'id' => "edit-{$post->guid}",);
	$list_body .= elgg_view_form('thewire/edit', $form_vars, array('post' => $post));
}

echo elgg_view_image_block($owner_icon, $list_body);

if ($show_thread) {
	echo elgg_format_element('div', [
		'id' => "thewire-thread-{$post->getGUID()}",
		'class' => 'thewire-thread',
		'data-thread' => $post->wire_thread,
		'data-guid' => $post->getGUID(),
	]);
}

if ($post->reply) {
	echo "<div class=\"thewire-parent hidden\" id=\"thewire-previous-{$post->guid}\">";
	echo "</div>";
}
