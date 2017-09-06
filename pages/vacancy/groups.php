<?php

$title = elgg_echo('vacancy:my_groups');
elgg_push_breadcrumb(elgg_echo('vacancy:my_groups'));
elgg_register_title_button();

$user_guid = elgg_get_logged_in_user_guid();

//Get user's groups
$options = ['relationship' => 'member', 'relationship_guid' => $user_guid,];
$groups = elgg_get_entities_from_relationship($options);

//Get guid from the user's groups
$data = array();
foreach ($groups as $group) {
    array_push($data, $group->getGuid());
}

//Search vacancies opened from user's group
$options = array(
    'types' => 'object',
    'subtypes' => 'vacancy',
    'container_guids' => $data,
);
$vacancies_opened = elgg_get_entities_from_metadata($options);

$limit = 10;
$i = 0;
$content_opened = array();
foreach ($vacancies_opened as $one_vacancy){
   if (vacancy_check_status($one_vacancy)){
      $content_opened[$i]=$one_vacancy;
      $i=$i+1;
   }
   if ($i==$limit)
      break;
}

//Search vacancies closed from user's group
$options = array(
    'types' => 'object',
    'subtypes' => 'vacancy',
    'container_guids' => $data,
);
$vacancies_closed = elgg_get_entities_from_metadata($options);
$i = 0;
$content_closed = array();
foreach ($vacancies_closed as $one_vacancy){
   if (!vacancy_check_status($one_vacancy)){
      $content_closed[$i]=$one_vacancy;
      $i=$i+1;
   }
   if ($i==$limit)
      break;
}

//Prepare variables to send
$vars['opened'] = $content_opened;
$vars['closed'] = $content_closed;

$content = elgg_view('forms/vacancy/groups', $vars);

$body = elgg_view_layout('content', array('filter_context' => 'groups', 'content' => $content, 'title' => $title));

echo elgg_view_page($title, $body);
