<?php

gatekeeper();

$vacancypost = get_input('vacancypost');
$vacancy = get_entity($vacancypost);
$container_guid = $vacancy->container_guid;
$container = get_entity($container_guid);
$edit = get_input('edit');

if ($vacancy->getSubtype() == "vacancy" && $vacancy->canEdit()) {

    $vacancy->option_close_value = 'vacancy_not_close';
    $vacancy->opened = false;
    $vacancy->action = true;

    if (elgg_is_active_plugin('event_manager')) {
        $event_guid = $vacancy->event_guid;
        if ($event = get_entity($event_guid)) {
            $deleted = $event->delete();
            if (!$deleted) {
                register_error(elgg_echo("vacancy:eventmanagernotdeleted"));
		 // Forward to the main vacancy page
                 if ($container instanceof ElggGroup) {
                    forward(elgg_get_site_url() . 'vacancy/group/' . $container->username);
                 } else {
                    forward(elgg_get_site_url() . 'vacancy/owner/' . $owner->username);
                 }
            }
        }
    }

    //System message 
    system_message(elgg_echo("vacancy:closed_listing"));
    //Forward
    if (strcmp($edit, 'no') == 0) {
        forward($_SERVER['HTTP_REFERER']);
    } else {
        forward("vacancy/edit/$vacancypost");
    }
}

?>
