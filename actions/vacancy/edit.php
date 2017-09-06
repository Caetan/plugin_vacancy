<?php

gatekeeper();
action_gatekeeper();

$user_guid = elgg_get_logged_in_user_guid();
$user = get_entity($user_guid);

//Get variables from the previous page
$vacancy_guid = get_input('vacancy_guid');

$close_vacancy = get_input('close_vacancy');

if (strcmp($close_vacancy, "yes") == 0) {
    $vacancy = get_entity($vacancy_guid);
    $container_guid = $vacancy->container_guid;
    $container = get_entity($container_guid);
    $vacancy->option_close_value = 'vacancy_not_close';
    $vacancy->opened = false;
    // Delete the event created with the vacancy (if event_manager plugin)
    if (elgg_is_active_plugin('event_manager')) {
        $event_guid = $vacancy->event_guid;
        if ($event = get_entity($event_guid)) {
            $deleted = $event->delete();
            if (!$deleted) {
                register_error(elgg_echo("vacancy:eventmanagernotdeleted"));
		if ($container instanceof ElggGroup) {
                   forward(elgg_get_site_url() . 'vacancy/group/' . $container_guid);
                } else {
                   forward(elgg_get_site_url() . 'vacancy/owner/' . $user->username);
                }
            }
        }
    }
    forward("vacancy/edit/$vacancy_guid");
}

$container_guid = get_input('container_guid');

$title = get_input('title');
$description = get_input('description');  
$tasks = get_input('tasks');  
$required_training = get_input('required_training');  
$recommended_age = get_input('recommended_age');  
$preferential_sex_array = get_input('preferential_sex');  
$driving_license_requirements_array = get_input('driving_license_requirements');  
$own_vehicle_requirements_array = get_input('own_vehicle_requirements');  
$travelling_availability_array = get_input('travelling_availability');  
$language_requirements_array = get_input('language_requirements');  
$other_requirements = get_input('other_requirements'); 
$contract = get_input('contract'); 
$salary = get_input('salary'); 
$work_shift = get_input('work_shift'); 
$work_place = get_input('work_place'); 
$teleworking_array = get_input('teleworking'); 
$company_transport = get_input('company_transport'); 
$other_conditions = get_input('other_conditions'); 
$vacancy_sector = get_input('vacancy_sector');
$vacancy_company_guid = get_input('vacancy_company_guid');

$rejection_reasons = get_input('rejection_reasons');
$rejection_reasons = array_map('trim', $rejection_reasons);

$access_id = get_input('access_id');
$tags = get_input('vacancytags');  


//Times
$option_activate_value = get_input('option_activate_value');
$option_close_value = get_input('option_close_value');
if (strcmp($option_activate_value, 'vacancy_activate_date') == 0) {
    $opendate = get_input('opendate');
    $opentime = get_input('opentime');
}
if (strcmp($option_close_value, 'vacancy_close_date') == 0) {
    $closedate = get_input('closedate');
    $closetime = get_input('closetime');
}

//Type of application
$application_type = get_input('application_type');
if (strcmp($application_type,'vacancy_application_type_form') == 0)
   $vacancy_form_guid = get_input('vacancy_form_guid');

// Cache to the session
elgg_make_sticky_form('edit_vacancy');


// Convert string of tags into a preformatted array  
$tagarray = string_to_tag_array($tags);


//Times
if (strcmp($option_activate_value, 'vacancy_activate_date') == 0) {
   $mask_time = "[0-2][0-9]:[0-5][0-9]";
   if (!ereg($mask_time, $opentime, $same)) {
      register_error(elgg_echo("vacancy:bad_times"));
      forward($_SERVER['HTTP_REFERER']);
   }
}
if (strcmp($option_close_value, 'vacancy_close_date') == 0) {
   $mask_time = "[0-2][0-9]:[0-5][0-9]";
   if (!ereg($mask_time, $closetime, $same)) {
      register_error(elgg_echo("vacancy:bad_times"));
      forward($_SERVER['HTTP_REFERER']);
   }
}
$now = time();  
if (strcmp($option_activate_value, 'vacancy_activate_now') == 0) {
   $activate_time = $now;
} else {
   $opentime_array = explode(':', $opentime);
   $opentime_h = trim($opentime_array[0]);
   $opentime_m = trim($opentime_array[1]);
   $opendate_array = explode('-', $opendate);
   $opendate_y = trim($opendate_array[0]);
   $opendate_m = trim($opendate_array[1]);
   $opendate_d = trim($opendate_array[2]);
   $activate_date = mktime(0, 0, 0, $opendate_m, $opendate_d, $opendate_y);
   $activate_time = mktime($opentime_h, $opentime_m, 0, $opendate_m, $opendate_d, $opendate_y);

   if ($activate_time < 1) {
      register_error(elgg_echo("vacancy:bad_times"));
      forward($_SERVER['HTTP_REFERER']);
   }
}
if (strcmp($option_close_value, 'vacancy_not_close') == 0) {
   $close_time = $now + 60 * 60 * 24 * 365 * 2;
} else {
   $closetime_array = explode(':', $closetime);
   $closetime_h = trim($closetime_array[0]);
   $closetime_m = trim($closetime_array[1]);
   $closedate_array = explode('-', $closedate);
   $closedate_y = trim($closedate_array[0]);
   $closedate_m = trim($closedate_array[1]);
   $closedate_d = trim($closedate_array[2]);
   $close_date = mktime(0, 0, 0, $closedate_m, $closedate_d, $closedate_y);
   $close_time = mktime($closetime_h, $closetime_m, 0, $closedate_m, $closedate_d, $closedate_y);

   if ($close_time < 1) {
      register_error(elgg_echo("vacancy:bad_times"));
      forward($_SERVER['HTTP_REFERER']);
   }
}
if ($activate_time >= $close_time) {
   register_error(elgg_echo("vacancy:error_times"));
   forward($_SERVER['HTTP_REFERER']);
}

//If title is empty return
if (empty($title)) {
    register_error(elgg_echo("vacancy:title_blank"));
    forward($_SERVER['HTTP_REFERER']);
}

//Description field is optional

//If tasks is empty return
if (empty($tasks)) {
    register_error(elgg_echo("vacancy:tasks_blank"));
    forward($_SERVER['HTTP_REFERER']);
}

//If required training is empty return
if (empty($required_training)) {
    register_error(elgg_echo("vacancy:required_training_blank"));
    forward($_SERVER['HTTP_REFERER']);
}

//If recommended age is empty return
if (empty($recommended_age)) {
    register_error(elgg_echo("vacancy:recommended_age_blank"));
    forward($_SERVER['HTTP_REFERER']);
}

//If preferential sex is empty return
if (count($preferential_sex_array)<1) {
    register_error(elgg_echo("vacancy:preferential_sex_blank"));
    forward($_SERVER['HTTP_REFERER']);
} 
$preferential_sex = implode(',',$preferential_sex_array);  

//If driving license requirements is empty return
if (count($driving_license_requirements_array)<1) {
    register_error(elgg_echo("vacancy:driving_license_requirements_blank"));
    forward($_SERVER['HTTP_REFERER']);
} 
$driving_license_requirements = implode(',', $driving_license_requirements_array); 

//If own vehicle requirements is empty return
if (count($own_vehicle_requirements_array)<1) {
    register_error(elgg_echo("vacancy:own_vehicle_requirements_blank"));
    forward($_SERVER['HTTP_REFERER']);
} 
$own_vehicle_requirements = implode(',', $own_vehicle_requirements_array); 

//If travelling availability is empty return
if (count($travelling_availability_array)<1) {
    register_error(elgg_echo("vacancy:travelling_availability_blank"));
    forward($_SERVER['HTTP_REFERER']);
} 
$travelling_availability = implode(',', $travelling_availability_array); 

//If language requirements is empty return
if (count($language_requirements_array)<1) {
    register_error(elgg_echo("vacancy:language_requirements_blank"));
    forward($_SERVER['HTTP_REFERER']);
} 
$language_requirements = implode(',', $language_requirements_array); 

//Other requirements field is optional

//Contract field is optional
//If contract is empty return
//if (empty($contract)) {
//    register_error(elgg_echo("vacancy:contract_blank"));
//    forward($_SERVER['HTTP_REFERER']);
//}


//Salary field is optional
//If salary shift is empty return
//if (empty($salary)) {
//    register_error(elgg_echo("vacancy:salary_blank"));
//    forward($_SERVER['HTTP_REFERER']);
//}


//If work shift is empty return
if (empty($work_shift)) {
    register_error(elgg_echo("vacancy:work_shift_blank"));
    forward($_SERVER['HTTP_REFERER']);
}


//If work place is empty return
if (empty($work_place)) {
    register_error(elgg_echo("vacancy:work_place_blank"));
    forward($_SERVER['HTTP_REFERER']);
}


//If teleworking is empty return
if (count($teleworking_array)<1) {
    register_error(elgg_echo("vacancy:teleworking_blank"));
    forward($_SERVER['HTTP_REFERER']);
} 
$teleworking = implode(',', $teleworking_array); 


//Other conditions field is optional

//Type of application form
if (strcmp($application_type,'vacancy_application_type_form')==0) {
   $vacancy_form = get_entity($vacancy_form_guid);
   if ($vacancy_form->subgroups) {
      register_error(elgg_echo("vacancy:form_not_subgroups"));
      forward($_SERVER['HTTP_REFERER']);
   }
   $previous_vacancy = elgg_get_entities_from_relationship(array('relationship'=> 'form_vacancy', 'relationship_guid'=> $vacancy_form_guid, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'vacancy', 'limit' => 0));
   if (!empty($previous_vacancy)) {
      $previous_vacancy_guid = $previous_vacancy[0]->getGUID();
      if ((!$vacancy_guid)||($vacancy_guid!=$previous_vacancy_guid)) {
         register_error(elgg_echo("vacancy:only_one_per_form"));
	 forward($_SERVER['HTTP_REFERER']);
      }
   }
}

if ($vacancy_guid) {
    $vacancy = get_entity($vacancy_guid);
    $vacancy->access_id = $access_id;
    $vacancy->title = $title;
    $vacancy->description = $description;  

    //If there is an error saving the vacancy
    if (!$vacancy->save()) {
       register_error(elgg_echo("vacancy:error_save"));
       forward($_SERVER['HTTP_REFERER']);
    }
    $vacancy->sector = $vacancy_sector;
    $vacancy->company_guid = $vacancy_company_guid;

    $vacancy->tasks = $tasks;  
    $vacancy->required_training = $required_training;  
    $vacancy->recommended_age = $recommended_age;  
    $vacancy->preferential_sex = $preferential_sex;  
    $vacancy->driving_license_requirements = $driving_license_requirements;  
    $vacancy->own_vehicle_requirements = $own_vehicle_requirements;  
    $vacancy->travelling_availability = $travelling_availability;  
    $vacancy->language_requirements = $language_requirements;  
    $vacancy->other_requirements = $other_requirements;  
    $vacancy->contract = $contract;  
    $vacancy->salary = $salary;  
    $vacancy->work_shift = $work_shift;  
    $vacancy->work_place = $work_place;  
    $vacancy->teleworking = $teleworking;  
    if (strcmp($company_transport,"on")==0) {
       $vacancy->company_transport = true;
    } else {
       $vacancy->company_transport = false;  
    }
    $vacancy->other_conditions = $other_conditions;  

    if (is_array($tagarray)) {
        $vacancy->tags = $tagarray;
    }

    
    //Set times
    $vacancy->option_activate_value = $option_activate_value;
    $vacancy->option_close_value = $option_close_value;
    if (strcmp($option_activate_value, 'vacancy_activate_now') != 0) {
        $vacancy->activate_date = $activate_date;
        $vacancy->activate_time = $activate_time;
        $vacancy->form_activate_date = $activate_date;
        $vacancy->form_activate_time = $opentime;
    }
    if (strcmp($option_close_value, 'vacancy_not_close') != 0) {
        $vacancy->close_date = $close_date;
        $vacancy->close_time = $close_time;
        $vacancy->form_close_date = $close_date;
        $vacancy->form_close_time = $closetime;
    }
    if ((strcmp($option_activate_value, 'vacancy_activate_date') == 0) && (strcmp($option_close_value, 'vacancy_close_date') == 0)) {
        if (($now >= $activate_time) && ($now < $close_time)) {
            $vacancy->opened = true;
        } else {
            $vacancy->opened = false;
        }
    } elseif (strcmp($option_activate_value, 'vacancy_activate_date') == 0) {
        if ($now >= $activate_time) {
            $vacancy->opened = true;
        } else {
            $vacancy->opened = false;
        }
    } elseif (strcmp($option_close_value, 'vacancy_close_date') == 0) {
        if ($now < $close_time) {
            $vacancy->opened = true;
        } else {
            $vacancy->opened = false;
        }
    } else {
        $vacancy->opened = true;
    }
    
    $vacancy->application_type = $application_type;
    if (strcmp($application_type,'vacancy_application_type_form') == 0) {
       $previous_vacancy_form_guid = $vacancy->form_guid;
       if ($previous_vacancy_form_guid) {
          if ($previous_vacancy_form_guid != $vacancy_form_guid) {
             $vacancy->form_guid = $vacancy_form_guid;
             remove_entity_relationship($previous_vacancy_form_guid,'form_vacancy',$vacancy_guid);
             add_entity_relationship($vacancy_form_guid,'form_vacancy',$vacancy_guid);
          }
       } else {
          $vacancy->form_guid = $vacancy_form_guid;
	  add_entity_relationship($vacancy_form_guid,'form_vacancy',$vacancy_guid);	
       }
    }
    $vacancy->rejection_reasons = implode(Chr(26),$rejection_reasons);

} else {
    //Create new vacancy
    $vacancy = new ElggObject();
    $vacancy->subtype = "vacancy";
    $vacancy->owner_guid = $user_guid;
    $vacancy->container_guid = $container_guid;
    $vacancy->access_id = $access_id;
    $vacancy->title = $title;
    $vacancy->description = $description;  

    //If there is an error saving the vacancy
    if (!$vacancy->save()) {
       register_error(elgg_echo("vacancy:error_save"));
       forward($_SERVER['HTTP_REFERER']);
    }

    $vacancy_guid = $vacancy->getGUID();

    $vacancy->sector = $vacancy_sector;
    $vacancy->company_guid = $vacancy_company_guid;

    $vacancy->tasks = $tasks;  
    $vacancy->required_training = $required_training;  
    $vacancy->recommended_age = $recommended_age;  
    $vacancy->preferential_sex = $preferential_sex;  
    $vacancy->driving_license_requirements = $driving_license_requirements;  
    $vacancy->own_vehicle_requirements = $own_vehicle_requirements;  
    $vacancy->travelling_availability = $travelling_availability;  
    $vacancy->language_requirements = $language_requirements;  
    $vacancy->other_requirements = $other_requirements;  
    $vacancy->contract = $contract;  
    $vacancy->salary = $salary;  
    $vacancy->work_shift = $work_shift;  
    $vacancy->work_place = $work_place;  
    $vacancy->teleworking = $teleworking;  
    if (strcmp($company_transport,"on")==0) {
       $vacancy->company_transport = true;
    } else {
       $vacancy->company_transport = false;  
    }
    $vacancy->other_conditions = $other_conditions; 

    if (is_array($tagarray)) {
        $vacancy->tags = $tagarray;
    }

    
    //Set times
    $vacancy->option_activate_value = $option_activate_value;
    $vacancy->option_close_value = $option_close_value;
    if (strcmp($option_activate_value, 'vacancy_activate_now') != 0) {
        $vacancy->activate_date = $activate_date;
        $vacancy->activate_time = $activate_time;
        $vacancy->form_activate_date = $activate_date;
        $vacancy->form_activate_time = $opentime;
    }
    if (strcmp($option_close_value, 'vacancy_not_close') != 0) {
        $vacancy->close_date = $close_date;
        $vacancy->close_time = $close_time;
        $vacancy->form_close_date = $close_date;
        $vacancy->form_close_time = $closetime;
    }
    if ((strcmp($option_activate_value, 'vacancy_activate_date') == 0) && (strcmp($option_close_value, 'vacancy_close_date') == 0)) {
        if (($now >= $activate_time) && ($now < $close_time)) {
            $vacancy->opened = true;
        } else {
            $vacancy->opened = false;
        }
    } elseif (strcmp($option_activate_value, 'vacancy_activate_date') == 0) {
        if ($now >= $activate_time) {
            $vacancy->opened = true;
        } else {
            $vacancy->opened = false;
        }
    } elseif (strcmp($option_close_value, 'vacancy_close_date') == 0) {
        if ($now < $close_time) {
            $vacancy->opened = true;
        } else {
            $vacancy->opened = false;
        }
    } else {
        $vacancy->opened = true;
    } 

    $vacancy->application_type = $application_type;
    if (strcmp($application_type,'vacancy_application_type_form') == 0) {
       $vacancy->form_guid = $vacancy_form_guid;
       add_entity_relationship($vacancy_form_guid,'form_vacancy',$vacancy_guid);	
    }
    $vacancy->rejection_reasons = implode(Chr(26),$rejection_reasons);
}

// Remove the vacancy post cache
elgg_clear_sticky_form('edit_vacancy');

if (!$vacancy_guid) {
    
    system_message(elgg_echo("vacancy:created"));
    add_to_river('river/object/vacancy/create', 'create', $user_guid, $vacancy_guid);
} else {
    system_message(elgg_echo("vacancy:updated"));
    add_to_river('river/object/vacancy/update', 'update', $user_guid, $vacancy_guid);
}


//Event using the event_manager plugin if it is active
    if (elgg_is_active_plugin('event_manager') && strcmp($option_close_value, 'vacancy_not_close') != 0) {

        $event_guid = $vacancy->event_guid;
        if (!($event = get_entity($event_guid))) {
            $event = new Event();
        }

	$event->title = sprintf(elgg_echo("vacancy:event_manager_title"), $vacancy->title);
        $event->description = $vacancy->getURL();
        $event->container_guid = $container_guid;
        $event->access_id = $access_id;
        if ($event->save()) {
           $event_guid = $event->getGUID();
           $vacancy->event_guid = $event_guid;
        } else {
           register_error(elgg_echo("vacancy:event_manager_error_save"));
	}
	$event->tags = string_to_tag_array($tags);
        $event->comments_on = 0;
        $event->registration_ended = 1;
        $event->show_attendees = 0;
        $event->max_attendees = "";
        $event->start_day = $close_date;
        $event->start_time = $close_time;
        $event->end_ts = $close_time + 1;
        $event->organizer = $user->getDisplayName();
        $event->setAccessToOwningObjects($access_id);     
        
    }


//Forward
forward(elgg_get_site_url() . 'vacancy/view/' . $vacancy_guid . '/' . $title);
