<?php

gatekeeper();

$vacancypost = get_input('vacancypost');
$vacancy = get_entity($vacancypost);

$this_user_guid = elgg_get_logged_in_user_guid();

if ($vacancy->getSubtype() == "vacancy") {
    $user_guid = get_input('user_guid');
    $user = get_entity($user_guid);
    $offset = get_input('offset');

    $opened = vacancy_check_status($vacancy);

    $owner = $vacancy->getOwnerEntity();
    $owner_guid = $owner->getGUID();

    $container_guid = $vacancy->container_guid;
    $container = get_entity($container_guid);

    $operator = false;
    if ($container instanceof ElggGroup) {
       $group_owner_guid = $container->owner_guid;
       if (($group_owner_guid==$this_user_guid)||(check_entity_relationship($this_user_guid,'group_admin',$container_guid))) 
          $operator = true;
    } else {
       if ($owner_guid == $this_user_guid)
          $operator = true;
    }

    if ((($opened) && (!$operator)) || ((!$opened) && ($operator))) {
    
       //Application
       $application_type = $vacancy->application_type;
       if (strcmp($application_type,"vacancy_application_type_form")==0){
          $vacancy_form_guid = $vacancy->form_guid;
          $vacancy_form = get_entity($vacancy_form_guid);
          $options = array('relationship' => 'form_answer', 'relationship_guid' => $vacancy_form_guid,'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'form_answer', 'order_by' => 'e.time_created desc', 'limit' => 0);
       } else {
          $options = array('relationship' => 'vacancy_application', 'relationship_guid' => $vacancypost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'vacancy_application', 'order_by' => 'e.time_created desc', 'limit' => 0, 'owner_guid' => $user_guid);
       }
       $user_applications = elgg_get_entities_from_relationship($options);
       if (!empty($user_applications)) {
          $user_application = $user_applications[0];
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
             $files_application = elgg_get_entities_from_relationship(array('relationship' => 'application_file_link', 'relationship_guid' => $user_application->getGUID(), 'inverse_relationship' => false, 'type' => 'object', 'limit' => 0));
	  }
          foreach ($files_application as $one_file) {
             $deleted = $one_file->delete();
             if (!$deleted) {
                register_error(elgg_echo("vacancy:filenotdeleted"));
                if (empty($offset))
                   forward("vacancy/view/$vacancypost/");
                else
                   forward("vacancy/view/$vacancypost/?offset=$offset");
             }
	  }
          $deleted = $user_application->delete();
          if (!$deleted) {
             register_error(elgg_echo("vacancy:applicationnotdeleted"));
             if (empty($offset))
                forward("vacancy/view/$vacancypost/");
             else
                forward("vacancy/view/$vacancypost/?offset=$offset");
          }
	  //System message
          system_message(elgg_echo("vacancy:applicationdeleted"));
       }
       
    } else {
       if ($opened)
          register_error(elgg_echo("vacancy:opened"));
       else
          register_error(elgg_echo("vacancy:closed"));
    }
    //Forward
    if (empty($offset))
        forward("vacancy/view/$vacancypost/");
    else
        forward("vacancy/view/$vacancypost/?offset=$offset");
}

?>
