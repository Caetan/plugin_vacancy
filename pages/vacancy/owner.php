<?php

gatekeeper();
if (is_callable('group_gatekeeper')) {
    group_gatekeeper();
}

$owner = elgg_get_page_owner_entity();
if (!$owner) {
    forward('vacancy/all');
}

$owner_guid = $owner->getGUID();
$user_guid = elgg_get_logged_in_user_guid();

elgg_push_breadcrumb($owner->name);

$companies = elgg_get_entities(array('type' => 'object', 'subtype' => 'company', 'limit' => false, 'owner_guid' => $user_guid));

if ($owner instanceof ElggGroup) {
   $group_owner_guid = $owner->owner_guid;
   if (($group_owner_guid==$user_guid)||(check_entity_relationship($user_guid,'group_admin',$owner_guid))) {
      elgg_register_title_button('vacancy', 'add');
      elgg_register_title_button('vacancy','add_company');
      if ($companies)
         elgg_register_title_button('vacancy','edit_company');
   }
} else {
   elgg_register_title_button('vacancy', 'add');
   elgg_register_title_button('vacancy','add_company');
   if ($companies)
      elgg_register_title_button('vacancy','edit_company');
   elgg_register_title_button('vacancy', 'filter');
}


$filter_context = '';
if ($owner_guid == elgg_get_logged_in_user_guid()) {
    $filter_context = 'mine';
}

$title = elgg_echo('vacancy:owner', array($owner->name));

//Prepare variables to send
$vars['owner'] = $owner;

$content = elgg_view('forms/vacancy/owner',$vars);

$params = array('filter_context' => $filter_context, 'content' => $content, 'title' => $title);
if (elgg_instanceof($owner, 'group')) {
    $params['filter'] = '';
}
$body = elgg_view_layout('content', $params);
echo elgg_view_page($title, $body);
