<?php
/**
 * Action for editing a wire post
 * 
 */

$guid = (int) get_input('guid');
$body = get_input('body', '', false);
$access_id = ACCESS_PUBLIC;

elgg_make_sticky_form('edit');

if (empty($body)) {
	register_error(elgg_echo("thewire:blank"));
	forward(REFERER);
}

if ($guid) {
	$entity = get_entity($guid);
	
	if (!elgg_instanceof($entity, 'object', 'thewire') && $entity->canEdit()) {
		register_error(elgg_echo('noaccess'));
		forward(REFERER);
	}
} 

if (!$guid) {
	register_error(elgg_echo("thewire:error"));
	forward(REFERER);
}

$entity->description = $body;
$entity->access_id = $access_id;

if ($entity->save()) {
elgg_clear_sticky_form('edit');

system_message(elgg_echo("thewire:saved"));
forward(REFERER);
}
else {
	register_error(elgg_echo("thewire:error"));
	forward(REFERER);
}
system_message(elgg_echo("thewire:saved"));
forward(REFERER);