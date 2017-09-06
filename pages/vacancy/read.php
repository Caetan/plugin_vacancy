<?php

gatekeeper();
if (is_callable('group_gatekeeper')) {
    group_gatekeeper();
}

$vacancy_guid = get_input('guid');
$vacancy = get_entity($vacancy_guid);
$offset = get_input('offset');
if (empty($offset))
    $offset = 0;
$user_guid = elgg_get_logged_in_user_guid();

//If the vacancy does not exist
if (!$vacancy) {
    register_error(elgg_echo('vacancy:notfound'));
    forward($_SERVER['HTTP_REFERER']);
}

elgg_set_page_owner_guid($vacancy->getContainerGUID());

$container = elgg_get_page_owner_entity();

$title = $vacancy->title;

if (elgg_instanceof($container, 'group')) {
    elgg_push_breadcrumb($container->name, "vacancy/group/$container->guid/all");
} else {
    elgg_push_breadcrumb($container->name, "vacancy/owner/$container->username");
}

elgg_push_breadcrumb($title);

$content = '';
$content .= elgg_view_entity($vacancy, array('full_view' => true, 'entity' => $vacancy, 'offset' => $offset));

$body = elgg_view_layout('content', array('filter' => '', 'content' => $content, 'title' => $title));

echo elgg_view_page($title, $body);
