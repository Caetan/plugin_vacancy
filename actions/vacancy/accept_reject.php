<?php

gatekeeper();
action_gatekeeper();

$user_guid = elgg_get_logged_in_user_guid();
$user = get_entity($user_guid);
$vacancypost = get_input('vacancypost');
$vacancy = get_entity($vacancypost);
$member_guid = get_input('member_guid');
$member = get_entity($member_guid);
$offset = get_input('offset');
$user_application_guid = get_input('user_application_guid');
$user_application = get_entity($user_application_guid);
$container_guid = $vacancy->container_guid;
$container = get_entity($container_guid);

$application_status = get_input('application_status');
$rejection_reasons = get_input('rejection_reasons');
$rejection_reasons = array_map('trim', $rejection_reasons);
$employer_comments = get_input('employer_comments');

$user_application->status = $application_status;
$user_application->employer_comments = $employer_comments;
$user_application->rejection_reasons = implode(Chr(26),$rejection_reasons);

//Forward
forward(elgg_get_site_url() . 'vacancy/view/' . $vacancypost . '/' . '?offset=' . $offset);


?>
