<?php

gatekeeper();
if (is_callable('group_gatekeeper')) {
    group_gatekeeper();
}

$container_guid = (int) get_input('container_guid');
$container = get_entity($container_guid);

$page_owner = $container;
if (elgg_instanceof($container, 'object')) {
    $page_owner = $container->getContainerEntity();
}
elgg_set_page_owner_guid($page_owner->getGUID());

$title = elgg_echo('vacancy:show_filter');

//Prepare variables to send
$vars['entity'] = null;
$vars['container_guid'] = $container_guid;
$vars['preferential_sex'] = get_input('preferial_sex');
$vars['tags'] = get_input('tags');
$vars['offset'] = get_input('offset');

elgg_push_breadcrumb($title);

$content = elgg_view('forms/vacancy/show_filter', $vars);

$body = elgg_view_layout('content', array('filter' => '', 'content' => $content, 'title' => $title));

echo elgg_view_page($title, $body);