<?php

gatekeeper();
action_gatekeeper();

$user_guid = elgg_get_logged_in_user_guid();
$user = get_entity($user_guid);

$container_guid = get_input('container_guid');

$vacancy_sector = get_input('vacancy_sector');
$vacancy_company_guid_array = get_input('vacancy_company_guid');
$vacancy_company_guid = implode(",",$vacancy_company_guid_array);
$preferential_sex_array = get_input('preferential_sex');  
$preferential_sex = implode(",",$preferential_sex_array);

$language_requirements_array = get_input('language_requirements');  
$language_requirements = implode(",",$language_requirements_array);  
$teleworking_array = get_input('teleworking');  
$teleworking = implode(",",$teleworking_array);  
$driving_license_requirements_array = get_input('driving_license_requirements');  
$driving_license_requirements = implode(",",$driving_license_requirements_array);  
$own_vehicle_requirements_array = get_input('own_vehicle_requirements');  
$own_vehicle_requirements = implode(",",$own_vehicle_requirements_array);  
$travelling_availability_array = get_input('travelling_availability');  
$travelling_availability = implode(",",$travelling_availability_array);  

$tags = get_input('tags');

// Cache to the session
elgg_make_sticky_form('filter_vacancy');

// Remove the post cache
elgg_clear_sticky_form('filter_vacancy');


// Forward
forward("vacancy/show_filter/$container_guid/?vacancy_sector=$vacancy_sector&vacancy_company_guid=$vacancy_company_guid&tags=$tags&preferential_sex=$preferential_sex&language_requirements=$language_requirements&teleworking=$teleworking&driving_license_requirements=$driving_license_requirements&own_vehicle_requirements=$own_vehicle_requirements&travelling_availability=$travelling_availability");  
?>