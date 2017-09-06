<?php

gatekeeper();

// Get input data
$guid = (int) get_input('guid');
$entity = get_entity($guid);
$owner = get_entity($entity->getOwner());

if ($entity->getSubtype() == 'vacancy') {
    $vacancy = $entity;
    $vacancypost = $vacancy->getGUID();
    if ($vacancy->canEdit()) {
       $container_guid = $vacancy->container_guid;
       $container = get_entity($container_guid);

       //Delete applications

       $application_type = $vacancy->application_type;
       if (strcmp($application_type,"vacancy_application_type_form")==0){
          $vacancy_form_guid = $vacancy->form_guid;
          $vacancy_form = get_entity($vacancy_form_guid);
          $options = array('relationship' => 'form_answer', 'relationship_guid' => $vacancy_form_guid,'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'form_answer', 'order_by' => 'e.time_created desc', 'limit' => 0);
       } else {
          $options = array('relationship' => 'vacancy_application', 'relationship_guid' => $vacancypost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'vacancy_application', 'limit' => 0, 'owner_guid' => $user_guid); 
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
                   // Forward to the main vacancy page
                   if ($container instanceof ElggGroup) {
                      forward(elgg_get_site_url() . 'vacancy/group/' . $container->username);
                   } else {
                      forward(elgg_get_site_url() . 'vacancy/owner/' . $owner->username);
                   }
                } 
             } 
             $deleted = $one_application->delete(); 
             if (!$deleted) { 
                register_error(elgg_echo("vacancy:applicationnotdeleted")); 
                // Forward to the main vacancy page
                if ($container instanceof ElggGroup) {
                   forward(elgg_get_site_url() . 'vacancy/group/' . $container->username);
                } else {
                   forward(elgg_get_site_url() . 'vacancy/owner/' . $owner->username);
                }    
             }  
          }

	  // Delete the event created with the vacancy (if event_manager plugin)
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
        
        // Delete it!
        $deleted = $vacancy->delete();
        if ($deleted > 0) {
            // Success message
            system_message(elgg_echo("vacancy:deleted"));
        } else {
            register_error(elgg_echo("vacancy:notdeleted"));
        }
        // Forward to the main vacancy page
        if ($container instanceof ElggGroup) {
            forward(elgg_get_site_url() . 'vacancy/group/' . $container->username);
        } else {
            forward(elgg_get_site_url() . 'vacancy/owner/' . $owner->username);
        }
    }
}