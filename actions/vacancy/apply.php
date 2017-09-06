<?php

gatekeeper();

$vacancypost = get_input('vacancypost');
$vacancy = get_entity($vacancypost);

if ($vacancy->getSubtype() == "vacancy") {
   if (vacancy_check_status($vacancy)) {
      $container_guid = $vacancy->container_guid;
      $container = get_entity($container_guid);
      $user_guid = get_input('user_guid');
      $user = get_entity($user_guid);
      
      //Applications
      $options = array('relationship' => 'vacancy_application', 'relationship_guid' => $vacancypost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'vacancy_application', 'order_by' => 'e.time_created desc', 'limit' => 0, 'owner_guid' => $user_guid);
        
       $user_applications = elgg_get_entities_from_relationship($options);
       if (!empty($user_applications)) {
          $user_application = $user_applications[0];
       } else {
          $user_application = "";
       }

       // Cache to the session
       elgg_make_sticky_form('apply_vacancy');

       $application_description = get_input('application_description'); 

       $j = 0;
       $file_save_well = true;
       $file_application_guid = array();
       $file_application_counter = count($_FILES['upload_application_file']['name']);
       
       if (!empty($user_application)) {
          $previous_application_files = elgg_get_entities_from_relationship(array('relationship' => 'application_file_link', 'relationship_guid' => $user_application->getGUID(), 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'vacancy_application_file', 'owner_guid' => $user_guid, 'limit' => 0));
       }

       $count_previous_appplication_files = 0;
       $count_deleted_previous_application_files = 0;
       foreach ($previous_application_files as $one_file) {
          $count_previous_application_files = $count_previous_application_files + 1;
	  $value = get_input($one_file->getGUID());
	  if ($value == '1') {
	     $count_deleted_previous_application_files = $count_deleted_previous_application_files + 1;
	  }
       }
       if ((($file_application_counter == 0) || ($_FILES['upload_application_file']['name'][0] == "")) && ($count_previous_application_files == $count_deleted_previous_application_files)) {
          register_error(elgg_echo('vacancy:not_application'));
	  forward($_SERVER['HTTP_REFERER']);
       }
       if (($file_application_counter > 0) && ($_FILES['upload_application_file']['name'][0] != "")) {
          $file_application_guids = "";
          for ($k = 0; $k < $file_application_counter; $k++) {
             $file_application[$k] = new ApplicationsVacancyPluginFile();
             $file_application[$k]->subtype = "vacancy_application_file";
             $prefix = "file/";
             $filestorename = elgg_strtolower(time() . $_FILES['upload_application_file']['name'][$k]);
             $file_application[$k]->setFilename($prefix . $filestorename);
             $file_application[$k]->setMimeType($_FILES['upload_application_file']['type'][$k]);
             $file_application[$k]->originalfilename = $_FILES['upload_application_file']['name'][$k];
             $file_application[$k]->simpletype = elgg_get_file_simple_type($_FILES['upload_application_file']['type'][$k]);
             $file_application[$k]->open("write");
             if (isset($_FILES['upload_application_file']) && isset($_FILES['upload_application_file']['error'][$k])) {
                $uploaded_file = file_get_contents($_FILES['upload_application_file']['tmp_name'][$k]);
             } else {
                $uploaded_file = false;
             }
             $file_application[$k]->write($uploaded_file);
             $file_application[$k]->close();
             $file_application[$k]->title = $_FILES['upload_application_file']['name'][$k];
	     $file_application[$k]->owner_guid = $user_guid;

             if ($container instanceof ElggGroup) {
	        $file_application[$k]->container_guid = $container_guid;
                $file_application[$k]->access_id = $container->teachers_acl;
             } else {
	        $file_application[$k]->container_guid = $user_guid;
                $file_application[$k]->access_id = 0;
             }           
	     $file_application[$k]->vacancy_guid = $vacancypost;
             $file_application_save = $file_application[$k]->save();
             if (!$file_application_save) {
                $file_save_well = false;
                break;
             } else {
                $file_application_guid[$j] = $file_application[$k]->getGUID();
                if ($k == 0)
                   $file_application_guids .= $file_application[$k]->getGUID();
                else
                   $file_application_guids .= "," . $file_application[$k]->getGUID();
                $j = $j + 1;
             }
          }
       }
              
       if (!$file_save_well) {
          foreach ($file_application_guid as $one_file_guid) {
             $one_file = get_entity($one_file_guid);
             $deleted = $one_file->delete();
             if (!$deleted) {
                register_error(elgg_echo('vacancy:filenotdeleted'));
                forward($_SERVER['HTTP_REFERER']);
             }
          }
          register_error(elgg_echo('vacancy:file_error_save'));
          forward($_SERVER['HTTP_REFERER']);
       }

       $found = false;
       if (!empty($user_application)){
	  $user_application->desc = $application_description;    
          $user_application->employer_comments = 'not_employer_comments';
          $found=true;
       }
         
       if (!$found) {
          // Initialise a new ElggObject to be the application
          $application = new ElggObject();
          $application->subtype = "vacancy_application";
          $application->title = 'application_title';
	  $application->owner_guid = $user_guid;
	  
          if ($container instanceof ElggGroup) {
	     $application->container_guid = $container_guid;
	     $application->access_id = $container->teachers_acl;
	  } else {
	     $application->container_guid = $user_guid;
	     $application->access_id = 0;  
	     
	  }	 

           $application->vacancy_guid = $vacancypost;


          if (!$application->save()) {
             foreach ($file_application_guid as $one_file_guid) {
                 $one_file = get_entity($one_file_guid);
                 $deleted = $one_file->delete();
                  if (!$deleted) {
                     register_error(elgg_echo('vacancy:filenotdeleted'));
                     forward($_SERVER['HTTP_REFERER']);
                 }
             }
             register_error(elgg_echo("vacancy:application_error_save"));
             forward($_SERVER['HTTP_REFERER']);
          }

	  $application->desc = $application_description;
          $application->status = "pending";
	  $application->rejection_reasons = "2";
          $application->employer_comments = "";
          //Relationship between the application and the vacancy
          add_entity_relationship($vacancypost, 'vacancy_application', $application->getGUID()); 
          //Counter of the number of applications
          $vacancy->annotate('all_applications', "1", $vacancy->access_id);
       }

       if (!empty($user_application)) {
          foreach ($previous_application_files as $one_file) {
             $value = get_input($one_file->getGUID());
             if ($value == '1') {
                $file1 = get_entity($one_file->getGUID());
                $deleted = $file1->delete();
                if (!$deleted) {
                   register_error(elgg_echo('vacancy:filenotdeleted'));
                   forward($_SERVER['HTTP_REFERER']);
                }
             }
          }
       }

       $file_application_guids_array = explode(",", $file_application_guids);
       foreach ($file_application_guids_array as $one_file_guid) {
          if (!$found) {
             add_entity_relationship($application->getGUID(), 'application_file_link', $one_file_guid);
          } else {
             add_entity_relationship($user_application->getGUID(), 'application_file_link', $one_file_guid);
          }
       }

      // Remove the vacancy post cache
      elgg_clear_sticky_form('apply_vacancy');
   
      system_message(elgg_echo("vacancy:applied"));
      //Forward
      forward(elgg_get_site_url() . 'vacancy/view/' . $vacancypost . '/' . $vacancy->title);

   } else {
      system_message(elgg_echo("vacancy:closed"));
      forward($_SERVER['HTTP_REFERER']);
   }
}


?>
