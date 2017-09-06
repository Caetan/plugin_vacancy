<?php

gatekeeper();
if (is_callable('group_gatekeeper')) {
    group_gatekeeper();
}

$vacancypost = (int) get_input('guid');
$vacancy = get_entity($vacancypost);

$container_guid = $vacancy->container_guid;
$container = get_entity($container_guid);

$member_guid = (int) get_input('member_guid');

$offset = (int) get_input('offset');

$page_owner = $container;
if (elgg_instanceof($container, 'object')) {
    $page_owner = $container->getContainerEntity();
}
elgg_set_page_owner_guid($page_owner->getGUID());

$title = elgg_echo('vacancy:accept_reject');
elgg_push_breadcrumb($title);

//Prepare variables to send
$vars['entity'] = $vacancy;
$vars['container_guid'] = $container_guid;
$vars['member'] = get_entity($member_guid);
$vars['offset'] = $offset;

$content = elgg_view('forms/vacancy/accept_reject', $vars);

$body = elgg_view_layout('content', array('filter' => '', 'content' => $content, 'title' => $title));

echo elgg_view_page($title, $body);