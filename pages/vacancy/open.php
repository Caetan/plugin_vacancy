<?php

$title = elgg_echo('vacancy:open');
elgg_push_breadcrumb(elgg_echo('vacancy:open'));
elgg_register_title_button();

$user_guid = elgg_get_logged_in_user_guid();

$content = elgg_view('forms/vacancy/open');

$body = elgg_view_layout('content', array('filter_context' => 'open', 'content' => $content, 'title' => $title));

echo elgg_view_page($title, $body);
