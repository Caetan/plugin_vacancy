<?php

$group = elgg_get_page_owner_entity();
$group_guid = $group->getGUID();
if ($group->vacancy_enable == "no") {
   return true;
}

elgg_push_context('widgets');

$options = array('type' => 'object','subtype' => 'vacancy','container_guid' => $group_guid,'limit' => 6,'full_view' => false,'pagination' => false);
$content = elgg_list_entities($options);

elgg_pop_context();

if (!$content) {
   $content = '<p>' . elgg_echo('vacancy:none') . '</p>';
}

$all_link = elgg_view('output/url', array('href' => "vacancy/group/$group->guid/all",'text' => elgg_echo('link:view:all'),'is_trusted' => true));

$new_link = elgg_view('output/url', array('href' => "vacancy/add/$group->guid",'text' => elgg_echo('vacancy:add'),'is_trusted' => true));

echo elgg_view('groups/profile/module', array('title' => elgg_echo('vacancy:group'),'content' => $content,'all_link' => $all_link,'add_link' => $new_link));

?>
