<?php 

gatekeeper();
action_gatekeeper();

$user_guid = elgg_get_logged_in_user_guid();
$user = get_entity($user_guid);

//Get variables from the previous page
$company_guid = get_input('company_guid');
$company = get_entity($company_guid);
$container_guid = $company->container_guid;
$container = get_entity($container_guid);
$selected_action = get_input('submit');

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
elgg_make_sticky_form('edit_company');


// Convert string of tags into a preformatted array  
$tagarray = string_to_tag_array($tags);


if ($company->getSubtype() == "company" && $company->canEdit()) {

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

   if (strcmp($selected_action,elgg_echo('vacancy:save'))==0) {
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
      } else {
         system_message(elgg_echo("vacancy:company_updated"));
      }  
      $company->sectors = $company_sectors;
   } else {
      $options = array(
      'types' => 'object',
      'subtypes' => 'vacancy',
      'metadata_name_value_pairs' => array(
      array('name' => 'company_guid', 'value' => $company->getGUID())));
      // Delete vacancys of this company!
      $vacancys = elgg_list_entities_from_metadata($options);
      foreach ($vacancys as $one_vacancy) {
         //Delete applications
	 $one_vacancy_guid = $one_vacancy->getGUID();


        //Caetan
        $application_type = $one_vacancy->application_type;

       if (strcmp($application_type,"vacancy_application_type_form")==0){
          $vacancy_form_guid = $one_vacancy->form_guid;
          $vacancy_form = get_entity($vacancy_form_guid);
          $options = array('relationship' => 'form_answer', 'relationship_guid' => $vacancy_form_guid,'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'form_answer', 'order_by' => 'e.time_created desc', 'limit' => 0);
       } else {
         $options = array('relationship' => 'vacancy_application', 'relationship_guid' => $one_vacancy_guid, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'vacancy_application', 'limit' => 0);
       }
         $users_applications = elgg_get_entities_from_relationship($options); 


         foreach ($users_applications as $one_application) { 
            $one_application_guid = $one_application->getGUID(); 
          if (strcmp($application_type,"vacancy_application_type_form")==0){
             //Questions of the form
             $options = array('relationship' => 'form_question', 'relationship_guid' => $vacancy_form_guid,'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'form_question','limit'=>0);
             $questions=elgg_get_entities_from_relationship($options);
             if (empty($questions)) {
                $num_questions=0;
             } else {
                $num_questions=count($questions);
             }
         $files_application = array();
         if ($num_questions > 0) {
                foreach($questions as $one_question){ 
               $one_question_guid = $one_question->getGUID();
                   $files_application = array_merge($files_application,elgg_get_entities_from_relationship(array('relationship' => 'response_file_link', 'relationship_guid' => $one_question_guid, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'form_response_file')));
            }
         }
          } else {
            $files_application = elgg_get_entities_from_relationship(array('relationship' => 'application_file_link', 'relationship_guid' => $one_application_guid, 'inverse_relationship' => false, 'type' => 'object', 'limit' => 0)); 
            }


            foreach ($files_application as $one_file) { 
               $deleted = $one_file->delete(); 
               if (!$deleted) { 
                  register_error(elgg_echo("vacancy:filenotdeleted")); 
		  forward($_SERVER['HTTP_REFERER']); 
               } 
            } 
            $deleted = $one_application->delete(); 
            if (!$deleted) { 
               register_error(elgg_echo("vacancy:applicationnotdeleted")); 
               forward($_SERVER['HTTP_REFERER']);
            }  
         }

	 // Delete the event created with the vacancy (if event_manager plugin)
	 if (elgg_is_active_plugin('event_manager')) {
            $event_guid = $one_vacancy->event_guid;
            if ($event = get_entity($event_guid)) {
               $deleted = $event->delete();
               if (!$deleted) {
                  register_error(elgg_echo("vacancy:eventmanagernotdeleted"));
                  forward($_SERVER['HTTP_REFERER']);
               }
            }
         } 

         $deleted = $one_vacancy->delete();
         if (!$deleted) {
            register_error(elgg_echo("vacancy:notdeleted"));
            forward($_SERVER['HTTP_REFERER']); 
         }
      }
     // Delete it!
     $deleted = $company->delete();
     if ($deleted > 0) {
        // Success message
        system_message(elgg_echo("vacancy:company_deleted"));
     } else {
        register_error(elgg_echo("vacancy:companynotdeleted"));
        forward($_SERVER['HTTP_REFERER']);
     }
  }


  
    // Now let's add tags.
    if (is_array($tagarray)) {
        $company->tags = $tagarray;
    }


  // Remove the vacancy post cache
  elgg_clear_sticky_form('edit_company');

  // Forward to the main vacancy page
  if ($container instanceof ElggGroup) {
     forward(elgg_get_site_url() . 'vacancy/group/' . $container->username);
  } else {
     forward(elgg_get_site_url() . 'vacancy/owner/' . $user->username);
  }

}
