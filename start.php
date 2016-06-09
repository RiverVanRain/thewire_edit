<?php
/**
 * Elgg wire plugin (extended)
 *
 * @author RiverVanRain
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2
 * @copyright (c) wZm 2k16
 *
 * @link http://o.wzm.me/crewz/p/1983/personal-net
 *
 */

elgg_register_event_handler('init', 'system', 'thewire_edit_init');

function thewire_edit_init() {

	elgg_register_action('thewire/edit', dirname(__FILE__) . '/actions/edit.php');
	elgg_unregister_action('thewire/add');

	if (elgg_is_active_plugin('thewire_tools')) {
		$thewire_tools_base = elgg_get_plugins_path() . 'thewire_tools/actions/thewire';
		elgg_unregister_action('thewire/add', '$thewire_tools_base/add.php');
	}
	elgg_register_action('thewire/add', dirname(__FILE__) . '/actions/add.php');

	elgg_unregister_plugin_hook_handler('register', 'menu:entity', 'thewire_setup_entity_menu_items');
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'thewire_edit_entity_menu_items');
	elgg_register_plugin_hook_handler('register', 'menu:river', 'thewire_edit_river_menu_setup', 600);

}

function thewire_edit_entity_menu_items($hook, $type, $value, $params) {
	$handler = elgg_extract('handler', $params, false);
	if ($handler != 'thewire') {
		return $value;
	}

	foreach ($value as $index => $item) {
		$name = $item->getName();
		if ($name == 'access') {
			unset($value[$index]);
		}
	}

	$entity = $params['entity'];

	if (elgg_is_logged_in()) {
		$options = array(
			'name' => 'reply',
			'text' => elgg_echo('thewire:reply'),
			'href' => "thewire/reply/$entity->guid",
			'priority' => 150,
		);
		$value[] = ElggMenuItem::factory($options);
	}

	if ($entity->reply) {
		$options = array(
			'name' => 'previous',
			'text' => elgg_echo('thewire:previous'),
			'href' => "thewire/previous/$entity->guid",
			'priority' => 160,
			'link_class' => 'thewire-previous',
			'title' => elgg_echo('thewire:previous:help'),
		);
		$value[] = ElggMenuItem::factory($options);
	}

	$options = array(
		'name' => 'thread',
		'text' => elgg_echo('thewire:thread'),
		'href' => "thewire/thread/$entity->wire_thread",
		'priority' => 170,
	);
	$value[] = ElggMenuItem::factory($options);

   if ($entity->canEdit()) {
		$options = array(
			'name' => 'edit',
			'text' => elgg_echo('thewire:edit'),
			'href' => "#edit-$entity->guid",
			'priority' => 180,
			'rel' => 'toggle',
			'link_class' => 'thewire-edit',
			'title' => elgg_echo('thewire:edit:title'),
		);
		$value[] = ElggMenuItem::factory($options);
	}

	return $value;
}

function thewire_edit_river_menu_setup($hook, $type, $return, $params) {
	if (!elgg_is_logged_in() || elgg_in_context('widgets')) {
		return;
	}

	$item = $params['item'];

	if ($item->subtype != "thewire") {
		return;
	}

	$object = $item->getObjectEntity();

	if ($object->countEntitiesFromRelationship("parent") || $object->countEntitiesFromRelationship("parent", true)) {
        $options = array(
          'name' => 'thread',
          'text' => elgg_echo('thewire:thread'),
          'href' => "thewire/thread/$object->wire_thread",
          'priority' => 170,
        );
        $return[] = ElggMenuItem::factory($options);
    }

	if ($object->canEdit()) {
		$options = array(
			'name' => 'edit',
			'text' => elgg_echo('thewire:edit'),
			'href' => "#edit-$object->guid",
			'priority' => 180,
			'rel' => 'toggle',
			'link_class' => 'thewire-edit',
			'title' => elgg_echo('thewire:edit:title'),
		);
		$return[] = ElggMenuItem::factory($options);
	}

	return $return;
}
