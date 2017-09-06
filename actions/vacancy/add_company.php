<?php

gatekeeper();
action_gatekeeper();

$user_guid = elgg_get_logged_in_user_guid();
$user = get_entity($user_guid);

//Get variables from the previous page
$container_guid = get_input('container_guid');

$title = get_input('title');
$NIF = get_input('NIF'); 
$URL_comp = get_input('URL_comp');  
$representative_name = get_input('representative_name');  
$representative_liability = get_input('representative_liability');  
$representative_email = get_input('representative_email');  
$representative_tel = get_input('representative_tel');  
$company_sectors_array = get_input('company_sectors');
$company_sectors = implode(';',$company_sectors_array);

$tags = get_input('companytags'); 
$access_id = get_input('access_id');

// Cache to the session
elgg_make_sticky_form('add_company');


// Convert string of tags into a preformatted array
$tagarray = string_to_tag_array($tags);

//If title is empty return
if (empty($title)) {
    register_error(elgg_echo("vacancy:company_title_blank"));
    forward($_SERVER['HTTP_REFERER']);
}



//If NIF is empty return
if (empty($NIF)) {
    register_error(elgg_echo("vacancy:company_NIF_blank"));
    forward($_SERVER['HTTP_REFERER']);
}


//If representative_name is empty return
if (empty($representative_name)) {
    register_error(elgg_echo("vacancy:company_representative_name_blank"));
    forward($_SERVER['HTTP_REFERER']);
}


//If representative_liability is empty return
if (empty($representative_liability)) {
    register_error(elgg_echo("vacancy:company_representative_liability_blank"));
    forward($_SERVER['HTTP_REFERER']);
}


//If representative_email is empty return
if (empty($representative_email)) {
    register_error(elgg_echo("vacancy:company_representative_email_blank"));
    forward($_SERVER['HTTP_REFERER']);
}

//Create new company
$company = new ElggObject();
$company->subtype = "company";
$company->owner_guid = $user_guid;
$company->container_guid = $container_guid;

$company->access_id = $access_id;
$company->title = $title;
$company->NIF = $NIF; 
$company->URL_comp = $URL_comp;  
$company->representative_name = $representative_name;  
$company->representative_liability = $representative_liability;  
$company->representative_email = $representative_email;  
$company->representative_tel = $representative_tel;  

//If there is an error saving the company
if (!$company->save()) {
    register_error(elgg_echo("vacancy:company_error_save"));
    forward($_SERVER['HTTP_REFERER']);
}

$company->sectors = $company_sectors;



// Now let's add tags.
if (is_array($tagarray)) {
    $company->tags = $tagarray;
}

// Remove the vacancy post cache
elgg_clear_sticky_form('add_company');

system_message(elgg_echo("vacancy:company_created"));

// Forward to the main vacancy page
if ($container instanceof ElggGroup) {
   forward(elgg_get_site_url() . 'vacancy/group/' . $container->username);
} else {
   forward(elgg_get_site_url() . 'vacancy/owner/' . $user->username);
}
