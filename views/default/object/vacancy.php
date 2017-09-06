<?php

$full = elgg_extract('full_view', $vars, FALSE);
$vacancy = elgg_extract('entity', $vars, FALSE);
$offset = elgg_extract('offset', $vars, FALSE);

if (!$vacancy) {
   return TRUE;
}

$owner = $vacancy->getOwnerEntity();
$owner_guid = $owner->getGUID();
$owner_icon = elgg_view_entity_icon($owner, 'tiny');
$owner_link = elgg_view('output/url', array('href' => "profile/$owner->username",'text' => $owner->name,'is_trusted' => true));
$author_text = elgg_echo('byline', array($owner_link));
$date = elgg_view_friendly_time($vacancy->time_created);
$subtitle = "$author_text $date";
$opened = vacancy_check_status($vacancy);  
$vacancypost = $vacancy->getGUID();
$tags = elgg_view('output/tags', array('tags' => $vacancy->tags));

$metadata = elgg_view_menu('entity', array('entity' => $vacancy,'handler' => 'vacancy','sort_by' => 'priority','class' => 'elgg-menu-hz'));

if (elgg_in_context('widgets')) {
	$metadata = '';
}

$user_guid = elgg_get_logged_in_user_guid();
$user = get_entity($user_guid);

$container_guid = $vacancy->container_guid;
$container = get_entity($container_guid);

$operator = false;
if ($container instanceof ElggGroup) {
   $group_owner_guid = $container->owner_guid;
   if (($group_owner_guid==$user_guid)||(check_entity_relationship($user_guid,'group_admin',$container_guid))) 
      $operator = true;
} else {
   if ($owner_guid == $user_guid)
      $operator = true;
}

if ($operator){
   if ($opened) {
      //Close
      $url_close = elgg_add_action_tokens_to_url(elgg_get_site_url() . "action/vacancy/close?edit=no&vacancypost=" . $vacancypost);
      $word_close = elgg_echo("vacancy:close_in_listing");
      $link_open_close = "<a href=\"{$url_close}\">{$word_close}</a>";
   } else {
      //Open
      $url_open = elgg_add_action_tokens_to_url(elgg_get_site_url() . "action/vacancy/open?vacancypost=" . $vacancypost);
      $word_open = elgg_echo("vacancy:open_in_listing");
      $link_open_close = "<a href=\"{$url_open}\">{$word_open}</a>";
   }

   $body_open_close = "<br>" . $link_open_close;  
}

//Open interval
if ($opened) {
    if ((strcmp($vacancy->option_activate_value, 'vacancy_activate_date') == 0) && (strcmp($vacancy->option_close_value, 'vacancy_close_date') == 0)) {
        $friendlytime_from = date("j M Y", $vacancy->activate_time) . " " . elgg_echo("vacancy:at") . " " . date("G:i", $vacancy->activate_time);
        $friendlytime_to = date("j M Y", $vacancy->close_time) . " " . elgg_echo("vacancy:at") . " " . date("G:i", $vacancy->close_time);
        $open_interval = elgg_echo('vacancy:opened_from') . ": " . $friendlytime_from . " " . elgg_echo('vacancy:to') . ": " . $friendlytime_to;

    } elseif (strcmp($vacancy->option_activate_value, 'vacancy_activate_date') == 0) {
        $friendlytime_from = date("j M Y", $vacancy->activate_time) . " " . elgg_echo("vacancy:at") . " " . date("G:i", $vacancy->activate_time);
        $open_interval = elgg_echo('vacancy:opened_from') . ": " . $friendlytime_from;
    } elseif (strcmp($vacancy->option_close_value, 'vacancy_close_date') == 0) {
        $friendlytime_to = date("j M Y", $vacancy->close_time) . " " . elgg_echo("vacancy:at") . " " . date("G:i", $vacancy->close_time);
        $open_interval = elgg_echo('vacancy:opened_to') . ": " . $friendlytime_to;
    } else {
        $open_interval = elgg_echo('vacancy:is_opened');
    }
} else {
    $open_interval = elgg_echo('vacancy:is_closed');
    if (elgg_is_active_plugin('event_manager')) {
        $event_guid = $vacancy->event_guid;
        if ($event = get_entity($event_guid)) {
            $now = time();
            if ($now > $vacancy->close_time)
                $deleted = $event->delete();
        }
    }
}


if ($full) {
   
   if (!$opened) {
      $title="<div class=\"vacancy_title\"><a class=\"closed_title_vacancy\" href=\"{$vacancy->getURL()}\">{$vacancy->title}</a></div>";
   } else {
      $title="<div class=\"vacancy_title\"><a class=\"opened_title_vacancy\" href=\"{$vacancy->getURL()}\">{$vacancy->title}</a></div>";
   }

   $body = "";
   $params = array('entity' => $vacancy,'title' => $title,'metadata' => $metadata,'subtitle' => $subtitle, 'tags' => $tags);
   $params = $params + $vars;
   $summary = elgg_view('object/elements/summary', $params);

   $body .= $open_interval;  

   if ($operator)
      $body .= $body_open_close;  

   if ($operator) {
      $body .= elgg_view('forms/vacancy/show_applications',array('entity' => $vacancy,'offset' => $offset));
   } else {
      if ($opened) {
         $body .= elgg_view('forms/vacancy/apply',array('entity' => $vacancy, 'user_guid' => $user_guid));
      } else {
         $body .= elgg_view('forms/vacancy/show_application',array('entity' => $vacancy, 'user_guid' => $user_guid));
      }
   }
   
   echo elgg_view('object/elements/full', array('summary' => $summary,'icon' => $owner_icon,'body' => $body));

} else {

   $num_comments = $vacancy->countComments();
   if ($num_comments != 1) {
      $label_num_comments = elgg_echo('vacancy:num_comments');
   } else {
      $label_num_comments = elgg_echo('vacancy:num_comment');
   }
   
   if (!$opened) {
      $title="<div class=\"vacancy_title\"><a class=\"closed_title_vacancy\" href=\"{$vacancy->getURL()}\">{$vacancy->title}</a></div>";
   } else {
      $title="<div class=\"vacancy_title\"><a class=\"opened_title_vacancy\" href=\"{$vacancy->getURL()}\">{$vacancy->title}</a></div>";
   }

   $params = array('entity' => $vacancy,'title' => $title, 'metadata' => $metadata,'subtitle' => $subtitle,'tags' => $tags);
   $params = $params + $vars;
   $list_body = elgg_view('object/elements/summary', $params);

   $body = "";

   $body .= $open_interval . "<br>";  
   $body .= $num_comments . " " . $label_num_comments;  

   if ($operator)
      $body .= $body_open_close;
 
   $list_body .= $body;

   echo elgg_view_image_block($owner_icon, $list_body);

}

?>
