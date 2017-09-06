<?php
   if (isset($vars['entity'])) {
      $vacancy = $vars['entity'];
      $offset = $vars['offset'];
      $vacancypost = $vacancy->getGUID();
      
      $limit = 10;
      $this_limit = $offset + $limit;

      $vacancy_body = "";

      $opened = vacancy_check_status($vacancy);

      $wwwroot = elgg_get_config('wwwroot');
      $img_template = '<img border="0" width="20" height="20" alt="%s" title="%s" src="' . $wwwroot . 'mod/vacancy/graphics/%s" />';

      if (!vacancy_check_status($vacancy)) {
         $vacancy_body .= "         ";
         $url_zip = elgg_add_action_tokens_to_url(elgg_get_site_url() . "action/vacancy/zip_all?vacancypost=$vacancypost");
         $text_zip = elgg_echo("vacancy:zips");
         $img_zip = sprintf($img_template, $text_zip, $text_zip, "zip_icon_grey.jpeg");
         $link_zip = "<a href=\"{$url_zip}\">{$img_zip}</a>";
         $vacancy_body .= $link_zip;
         $url_get_zip = elgg_add_action_tokens_to_url(elgg_get_site_url() . "action/vacancy/get_zips?vacancypost=$vacancypost");
	 $text_get_zip = elgg_echo("vacancy:get_zips");
         $img_get_zip = sprintf($img_template, $text_get_zip, $text_get_zip, "zip_icon.jpeg");
         $link_get_zip = "<a href=\"{$url_get_zip}\">{$img_get_zip}</a>";
	 $vacancy_body .= $link_get_zip;
      }

      //General comments
      $num_comments = $vacancy->countComments();
      if ($num_comments > 0)
         $vacancy_general_comments_label = elgg_echo('vacancy:general_comments') . " (" . $num_comments . ")";
      else
         $vacancy_general_comments_label = elgg_echo('vacancy:general_comments');
      $vacancy_body .= "<div class=\"contentWrapper\">";
      $vacancy_body .= "<div class=\"vacancy_fields_frame\">";
      $vacancy_body .= "<p align=\"left\"><a onclick=\"vacancy_show_general_comments();\" style=\"cursor:hand;\">$vacancy_general_comments_label</a></p>";
      $vacancy_body .= "<div id=\"commentsDiv\" style=\"display:none;\">";
      $vacancy_body .= elgg_view_comments($vacancy);
      $vacancy_body .= "<p><br>";
      $vacancy_body .= "</div>";
      $vacancy_body .= "</div>";
      $vacancy_body .= "</div>";
      $vacancy_body .= "<br>";
      $vacancy_body .= elgg_view('forms/vacancy/show_vacancy_information',array('entity' => $vacancy));


      //Applications

      $vacancy_body .= "<div class=\"contentWrapper\">";
      $vacancy_body .= "<div class=\"vacancy_vacancy_frame\">";

      $application_type = $vacancy->application_type;
      if (strcmp($application_type,"vacancy_application_type_form")==0){
         $vacancy_form_guid = $vacancy->form_guid;
          $vacancy_form = get_entity($vacancy_form_guid);
          $options = array('relationship' => 'form_answer', 'relationship_guid' => $vacancy_form_guid,'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'form_answer', 'order_by' => 'e.time_created desc', 'limit' => 0, 'owner_guid' => $user_guid);
      } else {
         $options = array('relationship' => 'vacancy_application', 'relationship_guid' => $vacancypost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'vacancy_application', 'order_by' => 'e.time_created desc', 'limit' => 0, 'owner_guid' => $user_guid);
      }

      $user_applications = elgg_get_entities_from_relationship($options);
      $count = count($user_applications);

      if ($count > 0) {
         $vacancy_body .= elgg_echo('vacancy:applications') . " (" . $count . ")" . "<br>";  
         $i = 0;
         foreach ($user_applications as $one_application) {
            if (($i >= $offset) && ($i < $this_limit)) {
	       $member = $one_application->getOwnerEntity();
               $member_guid = $member->getGUID();
	       $url = elgg_add_action_tokens_to_url(elgg_get_site_url() . "vacancy/accept_reject/" . $vacancypost . "/" . $member_guid . "/" . $offset);
               $url_text = elgg_echo('vacancy:application') . " " . elgg_echo('vacancy:of') . " " . $member->name;
               $url_delete = elgg_add_action_tokens_to_url(elgg_get_site_url() . "action/vacancy/delete_application?vacancypost=" . $vacancypost . "&user_guid=" . $member_guid . "&offset=" . $offset);
	
	       $img_delete_msg = elgg_echo('vacancy:delete_application');
	       $confirm_delete_msg = elgg_echo('vacancy:delete_application_confirm');
	       $img_delete = sprintf($img_template, $img_delete_msg, $img_delete_msg, "delete.gif");
	       $link = "<a href=\"{$url}\">{$url_text}</a>";

	       if (!$opened) {
	          $link .= " <a onclick=\"return confirm('$confirm_delete_msg')\" href=\"{$url_delete}\">{$img_delete}</a>";
	       }
               $vacancy_body .= $link;
	       $vacancy_body .= "<br>";
            }
            $i = $i + 1;
    	}

  	$vacancy_body .= elgg_view("navigation/pagination", array('count' => $count, 'offset' => $offset, 'limit' => $limit));

      } else {
         $vacancy_body .= "<b>";
	 $vacancy_body .= elgg_echo('vacancy:applications') . "</b><br>";
	 $vacancy_body .= elgg_echo('vacancy:not_applications');
      }
		
      $vacancy_body .= "</div>";
      $vacancy_body .= "</div>";

      echo elgg_echo($vacancy_body);

   }
?>


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

