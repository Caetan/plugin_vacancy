<div class="contentWrapper">
    <?php
    $action = "vacancy/accept_reject";
    $vacancy = $vars['entity'];
    $vacancypost = $vacancy->getGUID();
    $member = $vars['member'];
    $member_guid = $member->getGUID();
    $offset = $vars['offset'];  
    $container_guid = $vacancy->container_guid;
    $container = get_entity($container_guid);

    ?>
    <form action="<?php echo elgg_get_site_url() . "action/" . $action ?>" name="accept_reject_vacancy" enctype="multipart/form-data" method="post">

    <?php
    echo elgg_view('input/securitytoken');

      $form_body = "";

      //Application
      $user_application = "";
      $user_application_guid = "";

      $application_type = $vacancy->application_type;
      if (strcmp($application_type,"vacancy_application_type_form")==0){
          $vacancy_form_guid = $vacancy->form_guid;
          $vacancy_form = get_entity($vacancy_form_guid);
          $options = array('relationship' => 'form_answer', 'relationship_guid' => $vacancy_form_guid,'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'form_answer', 'order_by' => 'e.time_created desc', 'limit' => 0, 'owner_guid' => $member_guid); 

        } else {
            $options = array('relationship' => 'vacancy_application', 'relationship_guid' => $vacancypost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'vacancy_application', 'order_by' => 'e.time_created desc', 'limit' => 0, 'owner_guid' => $member_guid);
        }

       $user_applications = elgg_get_entities_from_relationship($options);

       if (!empty($user_applications)) {
          $user_application = $user_applications[0];
          $user_application_guid = $user_application->getGUID();
       }

       if (!empty($user_application)) {
         if (strcmp($application_type,"vacancy_application_type_form")==0){
            $application_form_label = "<b>".elgg_echo("vacancy:applications_form_label")."</b><br>";
            $link_form="<a href=\"{$vacancy_form->getURL()}\">{$vacancy_form->title}</a>";
            echo "<br>";
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
        
            $application_files = elgg_get_entities_from_relationship(array('relationship' => 'application_file_link', 'relationship_guid' => $user_application->getGUID(), 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'vacancy_application_file', 'owner_guid' => $member_guid, 'limit' => 0));
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
  
    $form_body = "";

    $opened = vacancy_check_status($vacancy);

    $status =  array('pending' => elgg_echo("vacancy:pending_application"),'accepted' => elgg_echo("vacancy:accepted_application"), 'rejected' => elgg_echo("vacancy:rejected_application"));

    $status_label = elgg_echo("vacancy:application_status");
    $application_status = $user_application->status;
    if (empty($application_status))
       $application_status = "pending"; 
    $rejection_reasons = explode(Chr(26),$vacancy->rejection_reasons);
    $rejection_reasons = array_map('trim', $rejection_reasons);
    if (!empty($rejection_reasons)) {
       $rejection_reasons_label = elgg_echo("vacancy:show_rejection_reasons");
       $application_rejection_reasons = explode(Chr(26),$user_application->rejection_reasons);
       $application_rejection_reasons = array_map('trim', $application_rejection_reasons);
       if (strcmp($application_status,"rejected")==0){
          $style_display_application_status = "display:block";
       } else {
	  $style_display_application_status = "display:none";
       }
    }

    $form_body .= "<p><b>" . $status_label . "</b><br>";

    if ($opened) {
       $form_body .= "<select name = \"application_status\" disabled>";
    } else {
       $form_body .= "<select name = \"application_status\" onchange=\"vacancy_show_rejection_reasons(this)\">";
    }
    foreach ($status as $one_status_key => $one_status) {  
    	  
       if (strcmp($one_status_key,$application_status)==0) {
          $form_body .= "<option value=". $one_status_key . " selected=\"selected\">" . $one_status . "</option>";
       } else {
          $form_body .= "<option value=". $one_status_key . ">" . $one_status . "</option>";
       }
    }
    $form_body .= "</select>";

    if (!empty($rejection_reasons)) {
       $form_body .= "<div id=\"resultsDiv_application_status\" style=" . $style_display_application_status . ">";
       $form_body .= "<p><b>" . $rejection_reasons_label . "</b><br>";
       if ($opened) {
          $form_body .= "<select multiple disabled name = \"rejection_reasons[]\">";
       } else {
          $form_body .= "<select multiple name = \"rejection_reasons[]\">";
       }
       if (is_array($rejection_reasons)){
          foreach ($rejection_reasons as $one_rejection_reason) {  
	     if (in_array($one_rejection_reason,$application_rejection_reasons)) {
                $form_body .= "<option value=". $one_rejection_reason . " selected=\"selected\">" . $one_rejection_reason . "</option>";
	     } else {
                $form_body .= "<option value=". $one_rejection_reason . ">" . $one_rejection_reason . "</option>";
	     }
          }
       } else {
          if ($rejection_reasons == $application_rejection_reasons) {
	     $form_body .= "<option value=". $rejection_reasons . "selected=\"selected\">" . $rejection_reasons . "</option>";
	  } else {
	     $form_body .= "<option value=". $rejection_reasons . ">" . $rejection_reasons . "</option>";
	  }   
       }
       $form_body .= "</select>";
       $form_body .= "</div>";
   }
   echo elgg_echo($form_body);


   if($opened) {  
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
      $employer_comments = $user_application->employer_comments;
      echo elgg_view("input/longtext", array('name' => 'employer_comments' , 'value' => $employer_comments));
    }


    $submit_label = elgg_echo("submit");
    if(!$opened) {
         $submit_input_application = elgg_view('input/submit', array('name' => 'submit', 'value' => $submit_label));
	 $entity_hidden = elgg_view('input/hidden', array('name' => 'vacancypost', 'value' => $vacancypost));
         $entity_hidden .= elgg_view('input/hidden', array('name' => 'user_application_guid', 'value' => $user_application_guid)); 
         $entity_hidden .= elgg_view('input/hidden', array('name' => 'member_guid', 'value' => $member_guid));
         $entity_hidden .= elgg_view('input/hidden', array('name' => 'offset', 'value' => $offset));   
         echo $submit_input_application . $entity_hidden;
      }


} else {
      $vacancy_body = "";
      $vacancy_body .= elgg_echo('vacancy:not_previous_application');
      echo elgg_echo($vacancy_body);
}

?>
</div>


<script type="text/javascript">
   function vacancy_show_rejection_reasons(select){
      var resultsDiv_application_status = document.getElementById('resultsDiv_application_status');
      var application_status = select.options[select.selectedIndex].value;
      if (application_status == 'rejected') {
         
         resultsDiv_application_status.style.display = 'block';
      } else {  
         resultsDiv_application_status.style.display = 'none';
      }
   }    
</script>


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