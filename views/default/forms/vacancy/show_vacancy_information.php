<div class="contentWrapper">

   <?php
   if (isset($vars['entity'])) {
      $vacancy = $vars['entity'];
      $vacancypost = $vacancy->getGUID();
      $company_guid = $vacancy->company_guid;  
      $company = get_entity($company_guid);  
      $container_guid = $vacancy->container_guid;  
      $container = get_entity($container_guid);  

      //Vacancy info
       $conf_show_vacancy = elgg_echo("vacancy:conf_show_vacancy");   

       $vacancy_body = "";

          ?>
      <p><a onclick="vacancy_show_vacancy();" style="cursor:hand;"><?php echo $conf_show_vacancy; ?></a></p>     
      <div id="resultsDiv_vacancy" style="display:none;">     
      <p>

      <?php
      $vacancy_body .= "</b><b>";
      $vacancy_body .= "<div class=\"vacancy_vacancy_frame\">";
      $description = $vacancy->description;
      if (!empty($description)){
         $vacancy_body .= "<div class=\"vacancy_fields_frame\">";
         $vacancy_body .= elgg_echo("vacancy:show_description");
         $vacancy_body .= "<p></b>";      
         $vacancy_body .= elgg_view('output/longtext', array('value' => $description));
         $vacancy_body .= "</div>";
         $vacancy_body .= "<p><br><b>";
      }
      $vacancy_body .= "<div class=\"vacancy_fields_frame\">";
      $vacancy_body .= elgg_echo("vacancy:tasks");
      $vacancy_body .= "<br></b>"; 
      $vacancy_body .= elgg_view('output/longtext', array('value' => $vacancy->tasks));
      $vacancy_body .= "</div>";
      $vacancy_body .= "<p><br><b>";
      $vacancy_body .= "<div class=\"vacancy_fields_frame\">";
      $vacancy_body .= elgg_echo("vacancy:required_training");
      $vacancy_body .= "<p></b>";
      $vacancy_body .= elgg_view('output/longtext', array('value' => $vacancy->required_training));
      $vacancy_body .= "</div>";
      $vacancy_body .= "<p><br><b>";

      $vacancy_body .= elgg_echo("vacancy:requirements_frame");    
      $vacancy_body .= "<div class=\"vacancy_requirements_frame\">";
      $vacancy_body .= "<div class=\"vacancy_fields_frame\">";
      $vacancy_body .= elgg_echo("vacancy:recommended_age");
      $vacancy_body .= "<p></b>";
      $vacancy_body .= elgg_view('output/text', array('value' => $vacancy->recommended_age));
      $vacancy_body .= "</div>";
      $vacancy_body .= "<p><br><b>";
      $vacancy_body .= "<div class=\"vacancy_fields_frame\">";
      $vacancy_body .= elgg_echo("vacancy:preferential_sex");
      $vacancy_body .= "<p></b>";
      $vacancy_body .= elgg_echo("$vacancy->preferential_sex");
      $vacancy_body .= "</div>";
      $vacancy_body .= "<p><br><b>";
      $vacancy_body .= "<div class=\"vacancy_fields_frame\">";
       $vacancy_body .= elgg_echo("vacancy:driving_license_requirements");
      $vacancy_body .= "<p></b>";
      $vacancy_body .= elgg_echo("$vacancy->driving_license_requirements");
      $vacancy_body .= "</div>";
      $vacancy_body .= "<p><br><b>";
      $vacancy_body .= "<div class=\"vacancy_fields_frame\">";
      $vacancy_body .= elgg_echo("vacancy:own_vehicle_requirements");
      $vacancy_body .= "<p></b>";
      $vacancy_body .= elgg_echo("$vacancy->own_vehicle_requirements");
      $vacancy_body .= "</div>";
      $vacancy_body .= "<p><br><b>";
      $vacancy_body .= "<div class=\"vacancy_fields_frame\">";
      $vacancy_body .= elgg_echo("vacancy:travelling_availability");
      $vacancy_body .= "<p></b>";
      $vacancy_body .= elgg_echo("$vacancy->travelling_availability");
      $vacancy_body .= "</div>";
      $vacancy_body .= "<p><br><b>";
      $vacancy_body .= "<div class=\"vacancy_fields_frame\">";
      $vacancy_body .= elgg_echo("vacancy:language_requirements");
      $vacancy_body .= "<p></b>";
      $vacancy_body .= elgg_echo("$vacancy->language_requirements");
      $vacancy_body .= "</div>";
      $vacancy_body .= "<p><br><b>";
      $other_requirements = $vacancy->other_requirements;
      if (!empty($other_requirements)){
         $vacancy_body .= "<div class=\"vacancy_fields_frame\">";
         $vacancy_body .= elgg_echo("vacancy:other_requirements");
         $vacancy_body .= "<br></b>";
         $vacancy_body .= elgg_view('output/longtext', array('value' => $other_requirements));
         $vacancy_body .= "</div>";
        
      }
      $vacancy_body .= "</div>";
      $vacancy_body .= "<p><br><b>";
      $vacancy_body .= elgg_echo("vacancy:conditions_frame");
      $vacancy_body .= "<div class=\"vacancy_conditions_frame\">";
      $contract = $vacancy->contract;
      if (!empty($contract)){
         $vacancy_body .= "<div class=\"vacancy_fields_frame\">";
         $vacancy_body .= elgg_echo("vacancy:contract");
         $vacancy_body .= "<br></b>";
         $vacancy_body .= elgg_view('output/longtext', array('value' => $contract));
         $vacancy_body .= "</div>";
         $vacancy_body .= "<p><br><b>";
      }
      $salary = $vacancy->salary;
      if (!empty($salary)){
         $vacancy_body .= "<div class=\"vacancy_fields_frame\">";
         $vacancy_body .= elgg_echo("vacancy:show_salary");
         $vacancy_body .= "<p></b>";
         $vacancy_body .= elgg_view('output/text', array('value' => $salary));
         $vacancy_body .= "</div>";
         $vacancy_body .= "<p><br><b>";
      }
      $vacancy_body .= "<div class=\"vacancy_fields_frame\">";
      $vacancy_body .= elgg_echo("vacancy:work_shift");
      $vacancy_body .= "<p></b>";
      $vacancy_body .= elgg_view('output/text', array('value' => $vacancy->work_shift));
      $vacancy_body .= "</div>";
      $vacancy_body .= "<p><br><b>";
      $vacancy_body .= "<div class=\"vacancy_fields_frame\">";
      $vacancy_body .= elgg_echo("vacancy:work_place"); 
      $vacancy_body .= "<p></b>";
      $vacancy_body .= elgg_view('output/text', array('value' => $vacancy->work_place));
      $vacancy_body .= "</div>";
      $vacancy_body .= "<p><br><b>";
      $vacancy_body .= "<div class=\"vacancy_fields_frame\">";
      $vacancy_body .= elgg_echo("vacancy:teleworking");
      $vacancy_body .= "<p></b>";
      $vacancy_body .= elgg_echo("$vacancy->teleworking");
      $vacancy_body .= "</div>";
      $vacancy_body .= "<p><br><b>";
   
      if($vacancy->company_transport) {
        $vacancy_body .= "<div class=\"vacancy_fields_frame\">";
        $vacancy_body .= elgg_echo("vacancy:company_transport_available");
        $vacancy_body .= "</div>";
      } else {
        $vacancy_body .= "<div class=\"vacancy_fields_frame\">";
        $vacancy_body .= elgg_echo("vacancy:company_transport_non_available");
        $vacancy_body .= "</div>";
      }

      $vacancy_body .= "</b><p><br><b>";
      $other_conditions = $vacancy->other_conditions;
      if (!empty($other_conditions)){
         $vacancy_body .= "<div class=\"vacancy_fields_frame\">";
         $vacancy_body .= elgg_echo("vacancy:other_conditions");
         $vacancy_body .= "<p></b>";     
         $vacancy_body .= elgg_view('output/longtext', array('value' => $other_conditions));
         $vacancy_body .= "</div>";
         
      }

      $vacancy_body .= "<p></b>";
      $vacancy_body .= "</b>";

      $vacancy_body .= "</div>";
      
      $rejection_reasons = $vacancy->rejection_reasons;
      
      if (!empty($rejection_reasons)){
         $rejection_reasons_array = explode(Chr(26),$rejection_reasons);
	 $rejection_reasons_array = array_map('trim', $rejection_reasons_array);
	 $rejection_reasons = "";
	 foreach ($rejection_reasons_array as $one_reason) {
	    $rejection_reasons .= $one_reason;
	    $rejection_reasons .= "\n";
	 }
         $vacancy_body .= "<p><br><b>";
         $vacancy_body .= "<div class=\"vacancy_fields_frame\">";
         $vacancy_body .= elgg_echo("vacancy:show_rejection_reasons");
         $vacancy_body .= "<p></b>";     
         $vacancy_body .= elgg_view('output/longtext', array('value' => $rejection_reasons));
         $vacancy_body .= "</div>";
	 $vacancy_body .= "<p></b>";  
      }

      $vacancy_body .= "</div>";
  
      $vacancy_body .= "</div></br>";

      echo elgg_echo($vacancy_body);


      //Company info
      $conf_show_company_information = elgg_echo("vacancy:conf_show_company_information");  
      $vacancy_body = "";

      ?>
      <p><a onclick="vacancy_show_company_information();" style="cursor:hand;"><?php echo $conf_show_company_information; ?></a></p>     
      <div id="resultsDiv_company" style="display:none;">     
      <p>

      <?php
      $vacancy_body .= "</b><b>";
      $vacancy_body .= "<div class=\"vacancy_vacancy_frame\">";
      $vacancy_body .= "<div class=\"company_fields_frame\">";
      $vacancy_body .= elgg_echo("vacancy:company_title");
      $vacancy_body .= "<br></b>"; 
      $vacancy_body .= elgg_view('output/text', array('value' => $company->title));
      $vacancy_body .= "</div>";
      $vacancy_body .= "<p><br><b>";
      $vacancy_body .= "<div class=\"company_fields_frame\">";
      $vacancy_body .= elgg_echo("vacancy:company_NIF");
      $vacancy_body .= "<p></b>";
      $vacancy_body .= elgg_view('output/text', array('value' => $company->NIF));
      $vacancy_body .= "</div>";
      $vacancy_body .= "<p><br><b>";
      $companyURL = $company->URL_comp;
      if (!empty($companyURL)) {
         $vacancy_body .= "<div class=\"company_fields_frame\">";
         $vacancy_body .= elgg_echo("vacancy:company_URL");
         $vacancy_body .= "<p></b>";
         $vacancy_body .= "<a href=\"".$companyURL."\">".$companyURL."</a>";
         $vacancy_body .= "</div>";
         $vacancy_body .= "<p><br><b>";
      }
      $vacancy_body .= "<div class=\"company_fields_frame\">";
      $vacancy_body .= elgg_echo("vacancy:sector/s");
      $vacancy_body .= "<p></b>";
      $vacancy_body .= elgg_echo("$company->sectors");
      $vacancy_body .= "</div>";
      $vacancy_body .= "<p><br><b>";
      $vacancy_body .= elgg_echo("vacancy:company_conf_representative");
      $vacancy_body .= "<div class=\"company_representative_frame\">";
      $vacancy_body .= "<div class=\"company_fields_frame\">";
      $vacancy_body .= elgg_echo("vacancy:company_representative_name");
      $vacancy_body .= "<p></b>";
      $vacancy_body .= elgg_view('output/text', array('value' => $company->representative_name));
      $vacancy_body .= "</div>";
      $vacancy_body .= "<p><br><b>";
      $vacancy_body .= "<div class=\"company_fields_frame\">";
      $vacancy_body .= elgg_echo("vacancy:company_representative_liability");
      $vacancy_body .= "<p></b>";
      $vacancy_body .= elgg_view('output/text', array('value' => $company->representative_liability));
      $vacancy_body .= "</div>";
      $vacancy_body .= "<p><br><b>";
      $vacancy_body .= "<div class=\"company_fields_frame\">";
      $vacancy_body .= elgg_echo("vacancy:company_representative_email");
      $vacancy_body .= "<p></b>";
      $vacancy_body .= elgg_view('output/text', array('value' => $company->representative_email));
      $vacancy_body .= "</div>";
      $vacancy_body .= "<p><br><b>";
      $representative_tel = $company->representative_tel;
      if (!empty($representative_tel)) {
         $vacancy_body .= "<div class=\"company_fields_frame\">";
         $vacancy_body .= elgg_echo("vacancy:company_representative_tel");
         $vacancy_body .= "<p></b>";
         $vacancy_body .= elgg_view('output/text', array('value' => $company->representative_tel));
         $vacancy_body .= "</div>";
         $vacancy_body .= "<p><br></b>";
      }
      $vacancy_body .= "</div>";
      $vacancy_body .= "</div>";
      $vacancy_body .= "</div></br>";

      echo elgg_echo($vacancy_body);


   /////////////////////   

   }
   ?>

</div>

 <script type="text/javascript">
   function vacancy_show_vacancy(){
      var resultsDiv_vacancy = document.getElementById('resultsDiv_vacancy');
      if (resultsDiv_vacancy.style.display == 'none') {
         resultsDiv_vacancy.style.display = 'block';
      } else {  
         resultsDiv_vacancy.style.display = 'none';
      }
   }

      function vacancy_show_company_information(){
      var resultsDiv_company = document.getElementById('resultsDiv_company');
      if (resultsDiv_company.style.display == 'none') {
         resultsDiv_company.style.display = 'block';
      } else {  
         resultsDiv_company.style.display = 'none';
      }
   }  

</script>
