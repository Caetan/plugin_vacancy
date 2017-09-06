<?php

elgg_pop_breadcrumb();
elgg_push_breadcrumb(elgg_echo('vacancies'));

elgg_register_title_button();

$title = elgg_echo('vacancy:all');

$content = elgg_view('forms/vacancy/everyone');

$body = elgg_view_layout('content', array('filter_context' => 'all', 'content' => $content, 'title' => $title));

echo elgg_view_page($title, $body);
