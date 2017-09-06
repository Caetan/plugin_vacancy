<div class="contentWrapper">

   <?php
   if (isset($vars['entity'])) {
      $vacancy = $vars['entity'];
      $vacancypost = $vacancy->getGUID();
      $user_guid = $vars['user_guid'];
      $user = get_entity($user_guid);
      $company_guid = $vacancy->company_guid;  //Caetan
      $company = get_entity($company_guid);  //Caetan

    
      //Applications
      $user_application = "";
      $user_application_guid = "";

      $application_type = $vacancy->application_type;
      if (strcmp($application_type,"vacancy_application_type_form")==0){
         $vacancy_form_guid = $vacancy->form_guid;
          $vacancy_form = get_entity($vacancy_form_guid);
          $options = array('relationship' => 'form_answer', 'relationship_guid' => $vacancy_form_guid,'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'form_answer', 'order_by' => 'e.time_created desc', 'limit' => 0, 'owner_guid' => $user_guid);

          if(form_check_status($vacancy_form)) {
              $vacancy_form->option_close_value = 'form_not_close';   
              $vacancy_form->opened = false;
              $vacancy_form->action = true;

                 if (elgg_is_active_plugin('event_manager')){
                    $event_guid = $vacancy_form->event_guid;
                    if ($event = get_entity($event_guid)){
                       $deleted = $event->delete();
                       if (!$deleted){
                          register_error(elgg_echo("form:eventmanagernotdeleted"));
                       }
                  
                     }
                  }  
            } 

        } else {
            $options = array('relationship' => 'vacancy_application', 'relationship_guid' => $vacancypost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'vacancy_application', 'order_by' => 'e.time_created desc', 'limit' => 0, 'owner_guid' => $user_guid);
        }

       $user_applications = elgg_get_entities_from_relationship($options);

       if (!empty($user_applications)) {
          $user_application = $user_applications[0];
          $user_application_guid = $user_application->getGUID();
       }

       //General comments

       $comments_body = "<br>";
       $comments_body .= "<div class=\"vacancy_frame\">";

       $num_comments = $vacancy->countComments();
       if ($num_comments > 0) {
          $vacancy_general_comments_label = elgg_echo('vacancy:general_comments') . " (" . $num_comments . ")";
       } else {
          $vacancy_general_comments_label = elgg_echo('vacancy:general_comments');
       }
       $comments_body .= "<p align=\"left\"><a onclick=\"vacancy_show_general_comments();\" style=\"cursor:hand;\">$vacancy_general_comments_label</a></p>";
       $comments_body .= "<div id=\"commentsDiv\" style=\"display:none;\">";
       $comments_body .= elgg_view_comments($vacancy);
       $comments_body .= "</div>";
       $comments_body .= "</div>";
       $comments_body .= "<br>";

       echo ($comments_body);

      //Show vacancy
      $vacancy_body = "";
      $vacancy_body .= elgg_view('forms/vacancy/show_vacancy_information',array('entity' => $vacancy));

      echo elgg_echo($vacancy_body);

      if (!empty($user_application)) {
         if (strcmp($application_type,"vacancy_application_type_form")==0){
            $application_form_label = "<b>".elgg_echo("vacancy:applications_form_label")."</b><br>";
            $link_form="<a href=\"{$vacancy_form->getURL()}\">{$vacancy_form->title}</a>";
            echo $application_form_label;
            echo $link_form;
            echo "<br><br>";
         } else {        
            $application_description = $user_application->desc;
	    if (strcmp($application_description, "") != 0) {
               $application_description_body .= "<p><b>" . elgg_echo('vacancy:application_description_label') . "</p></b>";
               $application_description_body .= "<div class=\"vacancy_frame\">";
               $application_description_body .= elgg_view('output/longtext', array('value' => $application_description));
               $application_description_body .= "</div><br>";
            }
        
            $application_files = elgg_get_entities_from_relationship(array('relationship' => 'application_file_link', 'relationship_guid' => $user_application->getGUID(), 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'vacancy_application_file', 'owner_guid' => $user_guid, 'limit' => 0));
            $application_file_guids = "";
            if ((count($application_files) > 0) && (strcmp($application_files[0]->title, "") != 0)) {
               foreach ($application_files as $file) {
                  if (strcmp($application_file_guids, "") == 0)
                     $application_file_guids .= $file->getGUID();
                  else
                     $application_file_guids .= "," . $file->getGUID();
               }
            }
            $application_file_guids_array = explode(",", $application_file_guids);

            $form_body = "";

	    if (strcmp($application_description_body,"")!=0) {
               $form_body .= $application_description_body;
	    }
          
	    if ((count($application_file_guids_array) > 0) && (strcmp($application_file_guids_array[0], "") != 0)) {
	       $form_body .=  "<p><b>" . elgg_echo('vacancy:show_application_files_label') . "</p></b>";
               foreach ($application_file_guids_array as $one_file_guid) {
                  $application_file = get_entity($one_file_guid);
                  $params = $one_file_guid . "_application";
                  //$icon = questions_set_icon_url($application_file, "small");
                  $url_file = elgg_get_site_url() . "mod/vacancy/download.php?params=$params";
                  $trozos = explode(".", $application_file->title);
                  $ext = strtolower(end($trozos));
                  if (($ext == 'jpg') || ($ext == 'png') || ($ext == 'gif') || ($ext == 'tif') || ($ext == 'tiff') || ($ext == 'jpeg'))
                     $form_body .= "<p align=\"center\"><a href=\"" . $url_file . "\">" . "<img src=\"" . $url_file . "\" width=\"600px\">" . "</a></p>";
                  else
                     $form_body .= "<p><a href=\"" . $url_file . "\">" . "<img src=\"" . elgg_get_site_url() . $icon . "\">" . $application_file->title . "</a></p>";
               }
            }
            $form_body .= "</div><br>";
	    echo elgg_echo($form_body);
        }  

    $application_status_body = "";
	//Coger el estado, si está vacío ponerlo a pending, mostrarlo como Estado: estado.
    $status =  array('pending' => elgg_echo("vacancy:pending_application"),'accepted' => elgg_echo("vacancy:accepted_application"), 'rejected' => elgg_echo("vacancy:rejected_application"));

    $status_label = elgg_echo("vacancy:application_status");
    $application_status = $user_application->status;
    if (empty($application_status)) {
       $shwo_application_status =  elgg_echo("vacancy:pending_application"); 
    } else {
       if (strcmp($application_status,'pending')==0) {
          $show_application_status =  elgg_echo("vacancy:pending_application"); 
       } elseif (strcmp($application_status,'accepted')==0) {
          $show_application_status =  elgg_echo("vacancy:accepted_application"); 
       } else {
          $show_application_status =  elgg_echo("vacancy:rejected_application"); 
       }
    }

    $application_status_body .= "<p><b>" . $status_label . "</b><br>";
    $application_status_body .= $show_application_status . "</p>";

    if (strcmp($application_status,"rejected")==0) {
      $rejection_reasons = explode(Chr(26),$vacancy->rejection_reasons);
      $rejection_reasons = array_map('trim', $rejection_reasons);
      if (!empty($rejection_reasons)) {
         $rejection_reasons_label = elgg_echo("vacancy:show_rejection_reasons");
         $application_rejection_reasons = explode(Chr(26),$user_application->rejection_reasons);
         $application_rejection_reasons = array_map('trim', $application_rejection_reasons);

         $rejection = "";
         foreach ($rejection_reasons as $one_rejection_reason) {
            $rejection .= $one_rejection_reason;
            $rejection .= "\n";
         }
	 $application_status_body .= "<p><b>" . $rejection_reasons_label . "</b>";
	 $application_status_body .= "<div class=\"vacancy_frame\">";
         $application_status_body .= elgg_view('output/longtext', array('value' => $rejection)) . "</p></div></br>";

      }
   }

   if (strcmp($application_status_body,"")!=0) {
      echo elgg_echo($application_status_body);
   }

	//Employer comments
        $employer_comments_body = "";
	$employer_comments = $user_application->employer_comments;
        if ((strcmp($employer_comments, "not_employer_comments") != 0) && (strcmp($employer_comments, "") != 0)) {
           $employer_comments_body .= "<p><b>" . elgg_echo('vacancy:employer_comments_label') . "</p></b>";
           $employer_comments_body .= "<div class=\"vacancy_frame\">";
           $employer_comments_body .= elgg_view('output/longtext', array('value' => $employer_comments));
           $employer_comments_body .= "</div><br>";
        }

	if (strcmp($employer_comments_body,"")!=0) {
	   echo elgg_echo($employer_comments_body);
	}


   } else {
        $form_body .= "<p>" . elgg_echo('vacancy:not_previous_application') . "</p>";
        echo elgg_echo($form_body);
     }

}

?>

</div>

<script type="text/javascript">
    function vacancy_show_general_comments() {
        var commentsDiv = document.getElementById('commentsDiv');
        if (commentsDiv.style.display == 'none') {
            commentsDiv.style.display = 'block';
        } else {
            commentsDiv.style.display = 'none';
        }
    }

 </script>



