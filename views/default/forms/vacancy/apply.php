<div class="contentWrapper">

   <?php


   if (isset($vars['entity'])) {

      $vacancy = $vars['entity'];
      $company_guid = $vacancy->company_guid;  //Caetan
      $company = get_entity($company_guid);  //Caetan
      $vacancypost = $vacancy->getGUID(); 
      $action = "vacancy/apply";
      $user_guid = $vars['user_guid'];
      $user = get_entity($user_guid);
      $container_guid = $vacancy->container_guid;
      $container = get_entity($container_guid);

      $conf_show_vacancy = elgg_echo("vacancy:conf_show_vacancy");
      $conf_show_company_information = elgg_echo("vacancy:conf_show_company_information");   
   

      ?>
      <br>
      <div class="vacancy_frame">
      <?php
         //General comments
         $num_comments = $vacancy->countComments();
         if ($num_comments > 0) {
            $vacancy_general_comments_label = elgg_echo('vacancy:general_comments') . " (" . $num_comments . ")";
         } else {
             $vacancy_general_comments_label = elgg_echo('vacancy:general_comments');
         }
         ?>
         <p align="left"><a onclick="vacancy_show_general_comments();"style="cursor:hand;"><?php echo $vacancy_general_comments_label; ?></a></p>
            <div id="commentsDiv" style="display:none;">
                <?php echo elgg_view_comments($vacancy); ?>
            </div>
      </div><br>

      <?php
      ////////////////////////////////////////////////////////////////////////aaa
      //Form
      ?>

      <form action="<?php echo elgg_get_site_url() . "action/" . $action ?>" name="apply_vacancy"
          enctype="multipart/form-data" method="post">

      <?php
      echo elgg_view('input/securitytoken');


      /////////////////////////////////////////////
      //Applications
      $application_type = $vacancy->application_type;
      if (strcmp($application_type,"vacancy_application_type_form")==0){
         $vacancy_form_guid = $vacancy->form_guid;
	 $vacancy_form = get_entity($vacancy_form_guid);
	 $options = array('relationship' => 'form_answer', 'relationship_guid' => $vacancy_form_guid,'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'form_answer', 'order_by' => 'e.time_created desc', 'limit' => 0, 'owner_guid' => $user_guid);
      } else {
         $options = array('relationship' => 'vacancy_application', 'relationship_guid' => $vacancypost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'vacancy_application', 'order_by' => 'e.time_created desc', 'limit' => 0, 'owner_guid' => $user_guid);
      }

      $user_applications = elgg_get_entities_from_relationship($options);

      if ($user_applications) {
         $user_application = $user_applications[0];
         $user_application_guid = $user_application->getGUID();
	 $wwwroot = elgg_get_config('wwwroot');
         $img_template = '<img border="0" width="20" height="20" alt="%s" title="%s" src="' . $wwwroot . 'mod/vacancy/graphics/%s" />';
	 $url_delete = elgg_add_action_tokens_to_url(elgg_get_site_url() . "action/vacancy/delete_application?vacancypost=" . $vacancypost . "&user_guid=" . $user_guid);
	 $img_delete_msg = elgg_echo('vacancy:delete_application');
	 $confirm_delete_msg = elgg_echo('vacancy:delete_application_confirm');
	 $img_delete = sprintf($img_template, $img_delete_msg, $img_delete_msg, "delete.gif");
      }

      //Show vacancy 
      $vacancy_body = "";
      $vacancy_body .= elgg_view('forms/vacancy/show_vacancy_information',array('entity' => $vacancy));

      echo elgg_echo($vacancy_body);

      if ($user_applications) { 
         $link_delete_application = " <a onclick=\"return confirm('$confirm_delete_msg')\" href=\"{$url_delete}\">{$img_delete}</a>";
         echo $link_delete_application . "<br>";
      }

      if (strcmp($application_type,"vacancy_application_type_form")==0){
         $application_form_label = "<b>".elgg_echo("vacancy:applications_form_label")."</b><br>";
         $link_form="<a href=\"{$vacancy_form->getURL()}\">{$vacancy_form->title}</a>";
	 echo $application_form_label;
	 echo $link_form;
	 echo "<br>";
      } else {
         if (elgg_is_sticky_form('apply_vacancy')){
            $application_description = elgg_get_sticky_value('apply_vacancy','application_description');
         } else {
            if (!empty($user_application)) {
               $application_description = $user_application->desc;
            } else {
               $application_description = "";
            }
         }

         $application_description_label = "<b>".elgg_echo("vacancy:application_description_label")."</b>";
         $application_description_input = elgg_view('input/longtext', array('name' => 'application_description', 'value' => $application_description));

         if (!empty($user_application)) {
            $application_files = elgg_get_entities_from_relationship(array('relationship' => 'application_file_link', 'relationship_guid' => $user_application->getGUID(), 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'vacancy_application_file', 'owner_guid' => $user_guid, 'limit' => 0));
         } else {
            $application_files = "";
         }

         $name_application = "upload_application_file" . "[]";
         $application_file_input = elgg_view("input/file", array('name' => $name_application, 'class' => 'multi'));

         elgg_clear_sticky_form('apply_vacancy');
       
         echo $application_description_label; 
         echo $application_description_input;
              
         ?>
         <p><br><b><?php echo elgg_echo("vacancy:application_files_label"); ?></b></p>
         <p><?php echo $application_file_input; ?></p>
         <?php
         if ($application_files) {
            if ((count($application_files) > 0) && (strcmp($application_files[0]->title, "") != 0)) {
               foreach ($application_files as $file) {
                  ?>
                  <div class="file_wrapper">
                  <a class="bold"
                  onclick="changeFormValue(<?php echo $file->getGUID(); ?>), changeImage(<?php echo $file->getGUID(); ?>)">
                  <img id="image_<?php echo $file->getGUID(); ?>"
                  src="<?php echo elgg_get_site_url(); ?>mod/vacancy/graphics/tick.jpeg">
                  </a>
                  <span><?php echo $file->title ?></span>
                  <?php
                  echo elgg_view("input/hidden", array('name' => $file->getGUID(), 'internalid' => $file->getGUID(), 'value' => '0'));
                  ?>
                  </div>
                  <br>
                  <?php
               }
            }
         }

         $vacancy_application = elgg_echo('vacancy:apply');
         $submit_input_application = elgg_view('input/submit', array('name' => 'submit', 'value' => $vacancy_application));
         $entity_hidden = elgg_view('input/hidden', array('name' => 'vacancypost', 'value' => $vacancypost));
         $entity_hidden .= elgg_view('input/hidden', array('name' => 'user_guid', 'value' => $user_guid));

         ?>
         <p><?php echo $submit_input_application . $entity_hidden;
         ?></p><br>

      <?php
      }
      ?>

      </form>
      <?php 

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


<script type="text/javascript">

    function changeImage(num) {
        if (document.getElementById('image_' + num).src == "<?php echo elgg_get_site_url(); ?>mod/vacancy/graphics/tick.jpeg")
            document.getElementById('image_' + num).src = "<?php echo elgg_get_site_url(); ?>mod/vacancy/graphics/delete_file.jpeg";
        else
            document.getElementById('image_' + num).src = "<?php echo elgg_get_site_url(); ?>mod/vacancy/graphics/tick.jpeg";
    }
</script>

<script>

      function vacancy_show_company_information(){
      var resultsDiv_company = document.getElementById('resultsDiv_company');
      if (resultsDiv_company.style.display == 'none') {
         resultsDiv_company.style.display = 'block';
      } else {  
         resultsDiv_company.style.display = 'none';
      }
   }  

</script>  


<script type="text/javascript">

  function vacancy_show_general_comments(){
      var commentsDiv = document.getElementById('commentsDiv');
      if (commentsDiv.style.display == 'none') {
         commentsDiv.style.display = 'block';
      } else {  
         commentsDiv.style.display = 'none';
      }
   }    
 
</script>


<script type="text/javascript"
        src="<?php echo elgg_get_site_url(); ?>mod/vacancy/lib/jquery.MultiFile.js"></script><!-- multi file jquery plugin -->
<script type="text/javascript"
        src="<?php echo elgg_get_site_url(); ?>mod/vacancy/lib/reCopy.js"></script><!-- copy field jquery plugin -->
<script type="text/javascript" src="<?php echo elgg_get_site_url(); ?>mod/vacancy/lib/js_functions.js"></script>
