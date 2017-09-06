<?php

$num = $vars['entity']->num_display;

//$options = array('type' => 'object','subtype' => 'vacancy','container_guid' => $vars['entity']->owner_guid,'limit' => $num,'full_view' => FALSE,'pagination' => FALSE);

$options = array('type' => 'object','subtype' => 'vacancy','limit' => $num,'full_view' => FALSE,'pagination' => FALSE);

$content = elgg_list_entities($options);
echo $content;

if ($content) {
   $vacancy_url = "vacancy/owner/" . elgg_get_page_owner_entity()->username;
   $more_link = elgg_view('output/url', array('href' => $vacancy_url,'text' => elgg_echo('vacancy:morevacancies'),'is_trusted' => true));
   echo "<span class=\"elgg-widget-more\">$more_link</span>";
} else {
   echo elgg_echo('vacancy:none');
}
