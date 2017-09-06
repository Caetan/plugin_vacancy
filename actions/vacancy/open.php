<?php

gatekeeper();

$vacancypost = get_input('vacancypost');
$vacancy = get_entity($vacancypost);

if ($vacancy->getSubtype() == "vacancy" && $vacancy->canEdit()) {

    $vacancy->option_activate_value = 'vacancy_activate_now';
    $vacancy->opened = true;
    $vacancy->action = true;

    //Event using the event_manager plugin if it is active
    if (elgg_is_active_plugin('event_manager') && strcmp($vacancy->option_close_value, 'vacancy_not_close') != 0) {

        $event_guid = $vacancy->event_guid;
        if (!($event = get_entity($event_guid))) {
            $event = new Event();	
        }

        $event->title = sprintf(elgg_echo("vacancy:event_manager_title"), $vacancy->title);
	$event->description = $vacancy->getURL();
	$event->container_guid = $vacancy->container_guid;
        $event->access_id = $vacancy->access_id;
	if ($event->save()) {
           $event_guid = $event->getGUID();
           $vacancy->event_guid = $event_guid;
        } else {
           register_error(elgg_echo("vacancy:event_manager_error_save"));
        }
        $event->tags = $vacancy->tags;
        $event->comments_on = 0;
        $event->registration_ended = 1;
	$event->show_attendees = 0;
        $event->max_attendees = "";
        $event->start_day = $vacancy->close_date;
        $event->start_time = $vacancy->close_time;
        $event->end_ts = $vacancy->close_time + 1;
        $event->organizer = elgg_get_logged_in_user_entity()->getDisplayName();
        $event->setAccessToOwningObjects($vacancy->access_id);

    }

    //System message 
    system_message(elgg_echo("vacancy:opened_listing"));
    //Forward
    forward($_SERVER['HTTP_REFERER']);
}

?>
