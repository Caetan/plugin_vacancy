<?php

gatekeeper();
if (is_callable('group_gatekeeper')) {
    group_gatekeeper();
}

$guid = get_input('guid');
$entity = get_entity($guid);

if ($entity->getSubtype() == 'vacancy') {
    $vacancy_guid = $guid;
    $vacancy = $entity;

    $container_guid = $vacancy->container_guid;
    $container = get_entity($container_guid);

    $page_owner = $container;
    if (elgg_instanceof($container, 'object')) {
        $page_owner = $container->getContainerEntity();
    }
    elgg_set_page_owner_guid($page_owner->getGUID());

    elgg_push_breadcrumb($vacancy->title, $vacancy->getURL());
    $title = elgg_echo('vacancy:edit');
    elgg_push_breadcrumb($title);

    //Prepare variables to send
    $vars['entity'] = $vacancy;

    $content = elgg_view('forms/vacancy/edit', $vars);
   
    $body = elgg_view_layout('content', array('filter' => '', 'content' => $content, 'title' => $title));

    echo elgg_view_page($title, $body);
}