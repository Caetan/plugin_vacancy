<div class="contentWrapper">
    <?php
    $action = "vacancy/edit";
    $user_guid = elgg_get_logged_in_user_guid();
    $user = get_entity($user_guid);  

    if ((isset($vars['entity']))&& (vacancy_check_status($vars['entity']))) {
        $vacancy = $vars['entity'];
	$vacancy_guid = $vacancy->getGUID();
        $vacancy_opened = elgg_echo('vacancy:opened');
        $close_vacancy = elgg_echo('vacancy:close');
        $form_body = "";
        $form_body .= "<p>" . $vacancy_opened . "</p>";
        $entity_hidden = elgg_view('input/hidden', array('name' => 'vacancy_guid', 'value' => $vacancy_guid));
        $entity_hidden .= elgg_view('input/hidden', array('name' => 'close_vacancy', 'value' => "yes"));
        $submit_input = elgg_view('input/submit', array('name' => 'submit', 'value' => $close_vacancy));
        $form_body .= "<p>" . $submit_input . $entity_hidden . "</p>";
        echo elgg_view('input/form', array('action' => elgg_get_site_url() . "action/vacancy/edit", 'body' => $form_body));



    } else {

    $conf_requirements = elgg_echo("vacancy:conf_requirements");   
    $conf_conditions = elgg_echo("vacancy:conf_conditions");   

    $sectors_settings = elgg_get_plugin_setting('sectors','vacancy');
    $sectors = explode(';',$sectors_settings);

     
    $sex =  array('Male' => elgg_echo("vacancy:sex_Male"),'Female' => elgg_echo("vacancy:sex_Female"), 'Other' => elgg_echo("vacancy:sex_Other"),'Indifferent' => elgg_echo("vacancy:sex_Indifferent"));
    
    $driving_license = array('Required' => elgg_echo("vacancy:license_Required"),'Valued' => elgg_echo("vacancy:license_Valued"),'Convenient' => elgg_echo("vacancy:license_Convenient"),'NonRequired' => elgg_echo("vacancy:license_NonRequired"));  
    
    $vehicle = array('CarRequired' => elgg_echo("vacancy:vehicle_CarRequired"), 'MotoRequired' => elgg_echo("vacancy:vehicle_MotoRequired"), 'CarPreferably' => elgg_echo("vacancy:vehicle_CarPreferably"), 'MotoPreferably' => elgg_echo("vacancy:vehicle_MotoPreferably"), 'VehicleValuedConvenient' => elgg_echo("vacancy:vehicle_VehicleValuedConvenient"), 'VehicleNonRequired' => elgg_echo("vacancy:vehicle_VehicleNonRequired"));
    
    $travelling = array('Yes' => elgg_echo("vacancy:travelling_Yes"), 'OnlyEU' => elgg_echo("vacancy:travelling_OnlyEU"), 'Valued' => elgg_echo("vacancy:travelling_Valued"), 'NonRequired' => elgg_echo("vacancy:travelling_NonRequired"));  
    
    $language = array('EnglishBasic' => elgg_echo("vacancy:language_EnglishBasic"), 'EnglishIntermediate' => elgg_echo("vacancy:language_EnglishIntermediate"), 'EnglishHigh' => elgg_echo("vacancy:language_EnglishHigh"), 'SpanishBasic' => elgg_echo("vacancy:language_SpanishBasic"), 'SpanishIntermediate' => elgg_echo("vacancy:language_SpanishIntermediate"), 'SpanishHigh' => elgg_echo("vacancy:language_SpanishHigh"), 'FrenchBasic' => elgg_echo("vacancy:language_FrenchBasic"), 'FrenchIntermediate' => elgg_echo("vacancy:language_FrenchIntermediate"), 'FrenchHigh' => elgg_echo("vacancy:language_FrenchHigh"), 'GermanBasic' => elgg_echo("vacancy:language_GermanBasic"), 'GermanIntermediate' => elgg_echo("vacancy:language_GermanIntermediate"), 'GermanHigh' => elgg_echo("vacancy:language_GermanHigh"), 'PortugueseBasic' => elgg_echo("vacancy:language_PortugueseBasic"), 'PortugueseIntermediate' => elgg_echo("vacancy:language_PortugueseIntermediate"), 'PortugueseHigh' => elgg_echo("vacancy:language_PortugueseHigh"), 'ItalianBasic' => elgg_echo("vacancy:language_ItalianBasic"), 'ItalianIntermediate' => elgg_echo("vacancy:language_ItalianIntermediate"), 'ItalianHigh' => elgg_echo("vacancy:language_ItalianHigh"), 'GalicianBasic' => elgg_echo("vacancy:language_GalicianBasic"), 'GalicianIntermediate' => elgg_echo("vacancy:language_GalicianIntermediate"), 'GalicianHigh' => elgg_echo("vacancy:language_GalicianHigh"), 'Others' => elgg_echo("vacancy:language_Others"), 'NonRequired' => elgg_echo("vacancy:language_NonRequired"));  
    
    $tele = array('Yes' => elgg_echo("vacancy:teleworking_Yes"), 'Negotiable' => elgg_echo("vacancy:teleworking_Negotiable"), 'No' => elgg_echo("vacancy:teleworking_No"));  

    if (isset($vars['entity'])) {//Fill the fields with the vacancy's information
        $vacancy = $vars['entity'];
	$vacancy_guid = $vacancy->getGUID();
	$container_guid = $vacancy->container_guid;
	$container = get_entity($container_guid);
        $hidden_vacancy_guid = elgg_view('input/hidden', array('name' => 'vacancy_guid', 'value' => $vacancy_guid));
	$url = elgg_get_site_url() . "vacancy/edit/$vacancy_guid";
        if (!elgg_is_sticky_form('edit_vacancy')) {
            $title = $vacancy->title;
            $description = $vacancy->description; 
            $tasks = $vacancy->tasks; 
            $required_training = $vacancy->required_training; 
            $recommended_age = $vacancy->recommended_age; 
            $preferential_sex = $vacancy->preferential_sex; 
            $preferential_sex_array = explode(',',$preferential_sex);  
            $driving_license_requirements = $vacancy->driving_license_requirements; 
            $driving_license_requirements_array = explode(',', $driving_license_requirements); 
            $own_vehicle_requirements = $vacancy->own_vehicle_requirements; 
            $own_vehicle_requirements_array = explode(',', $own_vehicle_requirements); 
            $travelling_availability = $vacancy->travelling_availability; 
            $travelling_availability_array = explode(',', $travelling_availability); 
            $language_requirements = $vacancy->language_requirements; 
            $language_requirements_array = explode(',', $language_requirements); 
            $other_requirements = $vacancy->other_requirements; 
            $contract = $vacancy->contract; 
            $salary = $vacancy->salary; 
            $work_shift = $vacancy->work_shift; 
            $work_place = $vacancy->work_place; 
            $teleworking = $vacancy->teleworking; 
            $teleworking_array = explode(',', $teleworking); 
            $company_transport = $vacancy->company_transport; 
            $other_conditions = $vacancy->other_conditions; 

            $access_id = $vacancy->access_id;
            $tags = $vacancy->tags;  

            
            $opendate = $vacancy->form_activate_date;
            $opentime = $vacancy->form_activate_time;
            $closedate = $vacancy->form_close_date;
            $closetime = $vacancy->form_close_time;
            $option_activate_value = $vacancy->option_activate_value;
            $option_close_value = $vacancy->option_close_value;

	    $application_type = $vacancy->application_type;
	    if (strcmp($application_type,"vacancy_application_type_form")==0)
	       $vacancy_form_guid = $vacancy->form_guid;

	    $vacancy_sector = get_input('selected_vacancy_sector');
            if (!$vacancy_sector)
	       $vacancy_sector = $vacancy->sector;
	    $vacancy_company_guid = $vacancy->company_guid;

	    $rejection_reasons=explode(Chr(26),$vacancy->rejection_reasons);
            $rejection_reasons = array_map('trim', $rejection_reasons);

        } else {
            $title = elgg_get_sticky_value('edit_vacancy', 'title');
            $description = elgg_get_sticky_value('edit_vacancy', 'description');  
            $tasks = elgg_get_sticky_value('edit_vacancy', 'tasks');  
            $required_training = elgg_get_sticky_value('edit_vacancy', 'required_training');  
            $recommended_age = elgg_get_sticky_value('edit_vacancy', 'recommended_age');  
            $preferential_sex_array = elgg_get_sticky_value('edit_vacancy', 'preferential_sex');  
            $preferential_sex = implode(',', $preferential_sex_array);  
            $driving_license_requirements_array = elgg_get_sticky_value('edit_vacancy', 'driving_license_requirements');  
            $driving_license_requirements = implode(',', $driving_license_requirements); 
            $own_vehicle_requirements_array = elgg_get_sticky_value('edit_vacancy', 'own_vehicle_requirements');  
            $own_vehicle_requirements = implode(',', $own_vehicle_requirements_array);  
            $travelling_availability_array = elgg_get_sticky_value('edit_vacancy', 'travelling_availability');  
            $travelling_availability = implode(',', $travelling_availability_array);  
            $language_requirements_array = elgg_get_sticky_value('edit_vacancy', 'language_requirements');  
            $language_requirements = implode(',', $language_requirements_array);  
            $other_requirements = elgg_get_sticky_value('edit_vacancy', 'other_requirements');  
            $contract = elgg_get_sticky_value('edit_vacancy', 'contract');  
	    $salary = elgg_get_sticky_value('edit_vacancy', 'salary');  
            $work_shift = elgg_get_sticky_value('edit_vacancy', 'work_shift');  
            $work_place = elgg_get_sticky_value('edit_vacancy', 'work_place');  
            $teleworking_array = elgg_get_sticky_value('edit_vacancy', 'teleworking');  
            $teleworking = implode(',', $teleworking_array);  
            $company_transport = elgg_get_sticky_value('edit_vacancy', 'company_transport');  
            $other_conditions = elgg_get_sticky_value('edit_vacancy', 'other_conditions');  

            $access_id = elgg_get_sticky_value('edit_vacancy', 'access_id');
            $tags = elgg_get_sticky_value('edit_vacancy', 'vacancytags');  

            
            $opendate = elgg_get_sticky_value('edit_vacancy', 'opendate');
            $closedate = elgg_get_sticky_value('edit_vacancy', 'closedate');
            $opentime = elgg_get_sticky_value('edit_vacancy', 'opentime');
            $closetime = elgg_get_sticky_value('edit_vacancy', 'closetime');
            $option_activate_value = elgg_get_sticky_value('edit_vacancy', 'option_activate_value');
            $option_close_value = elgg_get_sticky_value('edit_vacancy', 'option_close_value');

	    $application_type = elgg_get_sticky_value('edit_vacancy','application_type');
	    if (strcmp($application_type,"vacancy_application_type_form")==0)
	       $vacancy_form_guid = elgg_get_sticky_value('edit_vacancy','vacancy_form_guid'); 
	    
	    $vacancy_sector = elgg_get_sticky_value('edit_vacancy', 'vacancy_sector');
	    $vacancy_company_guid = elgg_get_sticky_value('edit_vacancy', 'vacancy_company_guid');

	    $rejection_reasons = elgg_get_sticky_value('edit_vacancy', 'rejection_reasons');
        }
    } else {//New vacancy
        $container_guid = $vars['container_guid'];
        $container = get_entity($container_guid);
        $hidden_container_guid = elgg_view('input/hidden', array('name' => 'container_guid', 'value' => $container_guid));
	$url = elgg_get_site_url() . "vacancy/add/$container_guid";
        if (!elgg_is_sticky_form('edit_vacancy')) {
            $title = "";
            $description = ""; 
            $tasks = ""; 
            $required_training = ""; 
            $recommended_age = "";  
            $preferential_sex_array = array();  
            $preferential_sex = "";  
            $driving_license_requirements_array = array(); 
            $driving_license_requirements = ""; 
            $own_vehicle_requirements_array = array(); 
            $own_vehicle_requirements = ""; 
            $travelling_availability_array = array(); 
            $travelling_availability = ""; 
            $language_requirements_array = array(); 
            $language_requirements = ""; 
            $other_requirements = "";  
            $contract = ""; 
	    $salary = ""; 
            $work_shift = ""; 
            $work_place = ""; 
            $teleworking_array = array(); 
            $teleworking = ""; 
            $company_transport = false; 
            $other_conditions = ""; 

	    $access_id = "";
            $tags = "";
	
            
            //$opendate =  "";
            $opendate = date("Y-m-d", time());

            $closedate = "";
            //$closedate =  date("Y-m-d", time()+24*60*60);
            $opentime = "00:00";
            $closetime = "00:00";
            $option_activate_value = 'vacancy_activate_now';
            $option_close_value = 'vacancy_not_close'; 

	    $application_type = 'vacancy_application_type_form';
	    $vacancy_form_guid = "";

	    $vacancy_sector = get_input('selected_vacancy_sector');
            if ((!$vacancy_sector)&&($sectors))
               $vacancy_sector = $sectors[0];
	    $vacancy_company_guid = "";
 
	    $rejection_reasons = array();
	    
       } else {
            $title = elgg_get_sticky_value('edit_vacancy', 'title');
            $description = elgg_get_sticky_value('edit_vacancy', 'description');  
            $tasks = elgg_get_sticky_value('edit_vacancy', 'tasks');  
            $required_training = elgg_get_sticky_value('edit_vacancy', 'required_training');  
            $recommended_age = elgg_get_sticky_value('edit_vacancy', 'recommended_age');  
            $preferential_sex_array = elgg_get_sticky_value('edit_vacancy', 'preferential_sex');  
            $preferential_sex = implode(',',$preferential_sex_array);  
            $driving_license_requirements_array = elgg_get_sticky_value('edit_vacancy', 'driving_license_requirements');  
            $driving_license_requirements = implode(',', $driving_license_requirements_array); 
            $own_vehicle_requirements_array = elgg_get_sticky_value('edit_vacancy', 'own_vehicle_requirements');  
            $own_vehicle_requirements = implode(',', $own_vehicle_requirements_array);  
            $travelling_availability_array = elgg_get_sticky_value('edit_vacancy', 'travelling_availability');  
            $travelling_availability = implode(',', $travelling_availability_array);  
            $language_requirements_array = elgg_get_sticky_value('edit_vacancy', 'language_requirements');  
            $language_requirements = implode(',', $language_requirements_array);  
            $other_requirements = elgg_get_sticky_value('edit_vacancy', 'other_requirements');  
            $contract = elgg_get_sticky_value('edit_vacancy', 'contract');  
	    $salary = elgg_get_sticky_value('edit_vacancy', 'salary');  
            $work_shift = elgg_get_sticky_value('edit_vacancy', 'work_shift');  
            $work_place = elgg_get_sticky_value('edit_vacancy', 'work_place');  
            $teleworking_array = elgg_get_sticky_value('edit_vacancy', 'teleworking');  
            $teleworking = implode(',', $teleworking_array);  
            $company_transport = elgg_get_sticky_value('edit_vacancy', 'company_transport');  
            $other_conditions = elgg_get_sticky_value('edit_vacancy', 'other_conditions');  

	    $access_id = elgg_get_sticky_value('edit_vacancy', 'access_id');
	    $tags = elgg_get_sticky_value('edit_vacancy', 'vacancytags');  
            
            
            $opendate = elgg_get_sticky_value('edit_vacancy', 'opendate');
            $closedate = elgg_get_sticky_value('edit_vacancy', 'closedate');
            $opentime = elgg_get_sticky_value('edit_vacancy', 'opentime');
            $closetime = elgg_get_sticky_value('edit_vacancy', 'closetime');
            $option_activate_value = elgg_get_sticky_value('edit_vacancy', 'option_activate_value');
            $option_close_value = elgg_get_sticky_value('edit_vacancy', 'option_close_value');

	    $application_type = elgg_get_sticky_value('edit_vacancy','application_type'); 
            if (strcmp($application_type,"vacancy_application_type_form")==0)
               $vacancy_form_guid = elgg_get_sticky_value('edit_vacancy','vacancy_form_guid'); 

	    $vacancy_sector = elgg_get_sticky_value('edit_vacancy', 'vacancy_sector');
	    $vacancy_company_guid = elgg_get_sticky_value('edit_vacancy', 'vacancy_company_guid');

	    $rejection_reasons = elgg_get_sticky_value('edit_vacancy', 'rejection_reasons');
        }
    }

    //Search companies of this sector
    $options = array('types' => 'object','subtypes' => 'company','limit' => false);
    $companies = elgg_get_entities_from_metadata($options);
    $companies_sector = array();
    $i=0;
    foreach ($companies as $one_company) {
       $one_company_sectors = $one_company->sectors;
       $one_company_sectors_array = explode(';',$one_company_sectors);
       if (in_array($vacancy_sector,$one_company_sectors_array))
          $companies_sector[$i]=$one_company;
	  $i=$i+1;
    }

    //Search forms
    $forms_array=array();
    if ($container instanceof ElggGroup) {
       $options = array('types' => 'object', 'subtypes' => 'form', 'container_guid' => $container_guid);
       $forms = elgg_get_entities($options);
       $forms_array = array_merge($forms_array,$forms);
    } else {
       $options = array('types' => 'group','owner_guid' => $user->getGUID());
       $groups = elgg_get_entities($options);
       foreach ($groups as $one_group) {
          $options = array('types' => 'object', 'subtypes' => 'form', 'container_guid' => $one_group->getGUID());
          $forms = elgg_get_entities($options);
          $forms_array = array_merge($forms_array,$forms);
       }
    }

    elgg_clear_sticky_form('edit_vacancy');

    //Prepare fields to show
  
    if (strcmp($opentime, "") == 0)
       $opentime = "00:00";

    if (strcmp($closetime, "") == 0)
       $closetime = "00:00";

    $company_transport_label = elgg_echo("vacancy:company_transport");
    if ($company_transport) {
        $selected_company_transport = "checked = \"checked\"";
    } else {
        $selected_company_transport = "";
    }

    
    $tag_label = elgg_echo('tags');
    $tag_input = elgg_view('input/tags', array('name' => 'vacancytags', 'value' => $tags));

    $access_label = elgg_echo('access:read');
    $access_input = elgg_view('input/access', array('name' => 'access_id', 'value' => $access_id));

    
    $options_activate = array();
    $options_activate[0] = elgg_echo('vacancy:activate_now');
    $options_activate[1] = elgg_echo('vacancy:activate_date');
    $op_activate = array();
    $op_activate[0] = 'vacancy_activate_now';
    $op_activate[1] = 'vacancy_activate_date';
    if (strcmp($option_activate_value, $op_activate[0]) == 0) {
       $checked_radio_activate_0 = "checked = \"checked\"";
       $checked_radio_activate_1 = "";
       $style_display_activate = "display:none";
    } else {
       $checked_radio_activate_0 = "";
       $checked_radio_activate_1 = "checked = \"checked\"";
       $style_display_activate = "display:block";
    }
    $options_close = array();
    $options_close[0] = elgg_echo('vacancy:not_close');
    $options_close[1] = elgg_echo('vacancy:close_date');
    $op_close = array();
    $op_close[0] = 'vacancy_not_close';
    $op_close[1] = 'vacancy_close_date';
    if (strcmp($option_close_value, $op_close[0]) == 0) {
       $checked_radio_close_0 = "checked = \"checked\"";
       $checked_radio_close_1 = "";
       $style_display_close = "display:none";
    } else {
       $checked_radio_close_0 = "";
       $checked_radio_close_1 = "checked = \"checked\"";
       $style_display_close = "display:block";
    }
    $opendate_label = elgg_echo('vacancy:opendate');
    $closedate_label = elgg_echo('vacancy:closedate');
    $opentime_label = elgg_echo('vacancy:opentime');
    $closetime_label = elgg_echo('vacancy:closetime');

    //Type of application
    $options_application_type = array();
    $options_application_type[0] = elgg_echo('vacancy:application_type_form');
    $options_application_type[1] = elgg_echo('vacancy:application_type_files');
    $op_application_type = array();
    $op_application_type[0] = 'vacancy_application_type_form';
    $op_application_type[1] = 'vacancy_application_type_files';
    if (strcmp($application_type, $op_application_type[0]) == 0) {
       $checked_radio_application_type_0 = "checked = \"checked\"";
       $checked_radio_application_type_1 = "";
       $style_display_application_type = "display:block";
    } else {
       $checked_radio_application_type_0 = "";
       $checked_radio_application_type_1 = "checked = \"checked\"";
       $style_display_application_type = "display:none";
    }

    $submit_input = elgg_view('input/submit', array('name' => 'submit', 'value' => elgg_echo('vacancy:save')));
    ?>

    <form action="<?php echo $vars['url'] . "action/" . $action ?>" name="edit_vacancy" enctype="multipart/form-data" method="post">

    <?php echo elgg_view('input/securitytoken'); ?>


        <p>
        <b><?php echo elgg_echo("vacancy:sector"); ?></b><br>
            <select name="vacancy_sector" onchange="vacancy_reload_edit_form(this)">
        <?php
            foreach ($sectors as $one_sector) {   
               ?>
               <option value="<?php echo $one_sector; ?>" <?php if ($one_sector==$vacancy_sector) echo "selected=\"selected\""; ?>> <?php echo $one_sector; ?> </option>
               <?php
            }
            ?>
        </select>
            </p>

        <p>
            <b><?php echo elgg_echo("vacancy:company"); ?></b><br>
            <select name="vacancy_company_guid">  
            <?php
            foreach ($companies_sector as $one_company) {
               $one_company_guid = $one_company->getGUID();
               $one_company_title = $one_company->title;
               ?>
               <option value="<?php echo $one_company_guid; ?>" <?php if ($one_company_guid == $vacancy_company_guid) echo "selected=\"selected\""; ?>> <?php echo $one_company_title; ?> </option>
               <?php
            }
            ?>
            </select>
            </p>

            
             <table class="vacancy_dates_table">
                <tr>
                    <td>
                        <p>
                            <b><?php echo elgg_echo('vacancy:activate_label'); ?></b><br>
                            <?php echo "<input type=\"radio\" name=\"option_activate_value\" value=$op_activate[0] $checked_radio_activate_0 onChange=\"vacancy_show_activate_time()\">$options_activate[0]"; ?>
                            <br>
                            <?php echo "<input type=\"radio\" name=\"option_activate_value\" value=$op_activate[1] $checked_radio_activate_1 onChange=\"vacancy_show_activate_time()\">$options_activate[1]"; ?>
                            <br>
                        <div id="resultsDiv_activate" style="<?php echo $style_display_activate; ?>;">
                            <?php echo $opendate_label; ?><br>
                            <?php echo elgg_view('input/date', array('autocomplete' => 'off', 'class' => 'vacancy-compressed-date', 'name' => 'opendate', 'value' => $opendate)); ?>
                            <?php echo "<br>" . $opentime_label; ?> <br>
                            <?php echo "<input type = \"text\" name = \"opentime\" value = $opentime>"; ?>
                        </div>
                        </p><br>
                    </td>
                    <td>
                        <p>
                            <b><?php echo elgg_echo('vacancy:close_label'); ?></b><br>
                            <?php echo "<input type=\"radio\" name=\"option_close_value\" value=$op_close[0] $checked_radio_close_0 onChange=\"vacancy_show_close_time()\">$options_close[0]"; ?>
                            <br>
                            <?php echo "<input type=\"radio\" name=\"option_close_value\" value=$op_close[1] $checked_radio_close_1 onChange=\"vacancy_show_close_time()\">$options_close[1]"; ?>
                            <br>
                        <div id="resultsDiv_close" style="<?php echo $style_display_close; ?>;">
                            <?php echo $closedate_label; ?><br>
                            <?php echo elgg_view('input/date', array('autocomplete' => 'off', 'class' => 'vacancy-compressed-date', 'name' => 'closedate', 'value' => $closedate)); ?>
                            <?php echo "<br>" . $closetime_label; ?> <br>
                            <?php echo "<input type = \"text\" name = \"closetime\" value = $closetime>"; ?>
                        </div>
                        </p><br>
                    </td>
                </tr>
            </table>


        <p>
            <b><?php echo elgg_echo("vacancy:title"); ?></b><br>
            <?php echo elgg_view("input/text", array('name' => 'title', 'value' => $title)); ?>
        </p>

        
        <p>
            <br><b><?php echo elgg_echo("vacancy:description"); ?></b><br>
            <?php echo elgg_view("input/longtext", array('name' => 'description', 'value' => $description)); ?>
        </p>


        
        <p>
            <br><b><?php echo elgg_echo("vacancy:tasks"); ?></b><br>
            <?php echo elgg_view("input/longtext", array('name' => 'tasks', 'value' => $tasks)); ?>
        </p>


        
        <p>
            <br><b><?php echo elgg_echo("vacancy:required_training"); ?></b><br>
            <?php echo elgg_view("input/longtext", array('name' => 'required_training', 'value' => $required_training)); ?>
        </p>



<p><a onclick="vacancy_show_requirements();" style="cursor:hand;"><?php echo $conf_requirements; ?></a></p>     
    <div id="resultsDiv_requirements" style="display:none;">     
<p>


        
        <p>
            <br><b><?php echo elgg_echo("vacancy:recommended_age"); ?></b><br>
            <?php echo elgg_view("input/text", array('name' => 'recommended_age', 'value' => $recommended_age)); ?>
        </p>
        
        <p>
            <br><b><?php echo elgg_echo("vacancy:preferential_sex"); ?></b><br>
      
        <select multiple name="preferential_sex[]">
    <?php
        foreach ($sex as $one_sex_key => $one_sex) {   
           ?>
           <option value="<?php echo $one_sex_key; ?>" <?php if (in_array($one_sex_key,$preferential_sex_array)) echo "selected=\"selected\""; ?>> <?php echo $one_sex; ?> </option>
           <?php
        }
        ?>
    </select>
      
        <p>
            <br><b><?php echo elgg_echo("vacancy:driving_license_requirements"); ?></b><br>
      
        <select multiple name="driving_license_requirements[]">
    <?php
        foreach ($driving_license as $one_driving_license_key => $one_driving_license) {   
           ?>
           <option value="<?php echo $one_driving_license_key; ?>" <?php if (in_array($one_driving_license_key,$driving_license_requirements_array)) echo "selected=\"selected\""; ?>> <?php echo $one_driving_license; ?> </option>
           <?php
        }
        ?>
    </select>

        <p>
            <br><b><?php echo elgg_echo("vacancy:own_vehicle_requirements"); ?></b><br>
      
        <select multiple name="own_vehicle_requirements[]">
    <?php
        foreach ($vehicle as $one_vehicle_key => $one_vehicle) {   
           ?>
           <option value="<?php echo $one_vehicle_key; ?>" <?php if (in_array($one_vehicle_key,$own_vehicle_requirements_array)) echo "selected=\"selected\""; ?>> <?php echo $one_vehicle; ?> </option>
           <?php
        }
        ?>
    </select>

        <p>
            <br><b><?php echo elgg_echo("vacancy:travelling_availability"); ?></b><br>
      
        <select multiple name="travelling_availability[]">
    <?php
        foreach ($travelling as $one_travelling_key => $one_travelling) {   
           ?>
           <option value="<?php echo $one_travelling_key; ?>" <?php if (in_array($one_travelling_key,$travelling_availability_array)) echo "selected=\"selected\""; ?>> <?php echo $one_travelling; ?> </option>
           <?php
        }
        ?>
    </select>
        
        <p>
            <br><b><?php echo elgg_echo("vacancy:language_requirements"); ?></b><br>
      
        <select multiple name="language_requirements[]">
    <?php
        foreach ($language as $one_language_key => $one_language) {   
           ?>
           <option value="<?php echo $one_language_key; ?>" <?php if (in_array($one_language_key,$language_requirements_array)) echo "selected=\"selected\""; ?>> <?php echo $one_language; ?> </option>
           <?php
        }
        ?>
    </select>
        
        <p>
            <br><b><?php echo elgg_echo("vacancy:other_requirements"); ?></b><br>
            <?php echo elgg_view("input/longtext", array('name' => 'other_requirements', 'value' => $other_requirements)); ?>
        </p>

</div>

</br>


<p><a onclick="vacancy_show_conditions();" style="cursor:hand;"><?php echo $conf_conditions; ?></a></p>     
    <div id="resultsDiv_conditions" style="display:none;">     
<p>

        
        <p>
            <br><b><?php echo elgg_echo("vacancy:contract"); ?></b><br>
            <?php echo elgg_view("input/longtext", array('name' => 'contract', 'value' => $contract)); ?>
        </p>

	
        <p>
            <br><b><?php echo elgg_echo("vacancy:salary"); ?></b><br>
            <?php echo elgg_view("input/text", array('name' => 'salary', 'value' => $salary)); ?>
        </p>

        
        <p>
            <br><b><?php echo elgg_echo("vacancy:work_shift"); ?></b><br>
            <?php echo elgg_view("input/text", array('name' => 'work_shift', 'value' => $work_shift)); ?>
        </p>


        
        <p>
            <br><b><?php echo elgg_echo("vacancy:work_place"); ?></b><br>
            <?php echo elgg_view("input/text", array('name' => 'work_place', 'value' => $work_place)); ?>
        </p>

         
        <p>
            <br><b><?php echo elgg_echo("vacancy:teleworking"); ?></b><br>
      
        <select multiple name="teleworking[]">
    <?php
        foreach ($tele as $one_tele_key => $one_tele) {   
           ?>
           <option value="<?php echo $one_tele_key; ?>" <?php if (in_array($one_tele_key,$teleworking_array)) echo "selected=\"selected\""; ?>> <?php echo $one_tele; ?> </option>
           <?php
        }
        ?>
    </select>

        
        <p>
        <br><b>
      	 <?php echo "<input type = \"checkbox\" name = \"company_transport\" $selected_company_transport> $company_transport_label"; ?>
	</b><br>
 
        
        <p>
            <br><b><?php echo elgg_echo("vacancy:other_conditions"); ?></b><br>
            <?php echo elgg_view("input/longtext", array('name' => 'other_conditions', 'value' => $other_conditions)); ?>
        </p>

</div>

</br>
 

 	<p>
	   <b><?php echo elgg_echo('vacancy:application_type'); ?></b><br>
           <?php echo "<input type=\"radio\" name=\"application_type\" value=$op_application_type[0] $checked_radio_application_type_0 onChange=\"vacancy_show_application_type()\">$options_application_type[0]"; ?>
           <br>
           <?php echo "<input type=\"radio\" name=\"application_type\" value=$op_application_type[1] $checked_radio_application_type_1 onChange=\"vacancy_show_application_type()\">$options_application_type[1]"; ?>
           <br>
           <div id="resultsDiv_application_type" style="<?php echo $style_display_application_type; ?>;">
              <b><?php echo elgg_echo("vacancy:form"); ?></b><br>
              <select name="vacancy_form_guid">
              <?php
              foreach ($forms_array as $one_form) {  
                 $one_form_guid = $one_form->getGUID();
                 $one_form_title = $one_form->title;
                 ?>
                 <option value="<?php echo $one_form_guid; ?>" <?php if ($one_form_guid==$vacancy_form_guid) echo "selected=\"selected\""; ?>> <?php echo $one_form_title; ?> </option>
                 <?php
              }
              ?>
              </select>          
           </div>
        </p><br>
 
	  <p>
      <b><?php echo elgg_echo('vacancy:rejection_reasons'); ?></b><br>
      <?php
      if (count($rejection_reasons) > 0) {
         $i=0;
         foreach ($rejection_reasons as $one_reason) {
            ?>
            <p class="clone">
            <?php
            echo elgg_view("input/text", array("name" => "rejection_reasons[]","value" => $one_reason));
            if ($i>0){   
               ?>
               <!-- remove reason -->
               <a class="remove" href="#" onclick="$(this).parent().slideUp(function(){ $(this).remove() }); return false"><?php echo elgg_echo("delete"); ?></a>
               <?php
            }
            ?>
            </p>
            <?php
            $i=$i+1;
         } 
      } else {
         ?>
         <p class="clone">
         <?php
         echo elgg_view("input/text", array("name" => "rejection_reasons[]","value" => $rejection_reasons));
         ?>  
         </p>        
         <?php
      }
      ?>
      <!-- add link to add more reasons which triggers a jquery clone function -->
      <a href="#" class="add" rel=".clone"><?php echo elgg_echo("vacacancy:add_rejection_reason"); ?></a>
      <br /><br />
      </p>       
 
       
        
        <p>
            <b><br/><?php echo $tag_label; ?></b><br>
            <?php echo $tag_input; ?>
	</p><br>

        <p>
            <b><?php echo $access_label; ?></b><br>
            <?php echo $access_input; ?>
        </p>

        <?php echo "$submit_input";?>

        <?php if (isset($vars['entity'])) { ?>
            <?php echo $hidden_vacancy_guid; ?>
        <?php } else { ?>
            <?php echo $hidden_container_guid; ?>
        <?php } ?>

    </form>

<?php
}
?>

</div>

<script type="text/javascript">
   function vacancy_show_requirements(){
      var resultsDiv_requirements = document.getElementById('resultsDiv_requirements');
      if (resultsDiv_requirements.style.display == 'none') {
         resultsDiv_requirements.style.display = 'block';
      } else {  
         resultsDiv_requirements.style.display = 'none';
      }
   }    
</script>



 <script language="javascript">
        function vacancy_show_activate_time() {
            var resultsDiv_activate = document.getElementById('resultsDiv_activate');
            if (resultsDiv_activate.style.display == 'none') {
                resultsDiv_activate.style.display = 'block';
            } else {
                resultsDiv_activate.style.display = 'none';
            }
        }
        function vacancy_show_close_time() {
            var resultsDiv_close = document.getElementById('resultsDiv_close');
            if (resultsDiv_close.style.display == 'none') {
                resultsDiv_close.style.display = 'block';
            } else {
                resultsDiv_close.style.display = 'none';
            }
        }
    </script>

<script language="javascript">
        function vacancy_show_application_type() {
            var resultsDiv_application_type = document.getElementById('resultsDiv_application_type');
            if (resultsDiv_application_type.style.display == 'none') {
                resultsDiv_application_type.style.display = 'block';
            } else {
                resultsDiv_application_type.style.display = 'none';
            }
        }
</script>

<script type="text/javascript">
   function vacancy_show_conditions(){
      var resultsDiv_conditions = document.getElementById('resultsDiv_conditions');
      if (resultsDiv_conditions.style.display == 'none') {
         resultsDiv_conditions.style.display = 'block';
      } else {  
         resultsDiv_conditions.style.display = 'none';
      }
   }    
</script>

<script language="javascript">

function vacancy_reload_edit_form(select) { 
   location.href = "<?php echo $url; ?>" + "&selected_vacancy_sector=" + select.options[select.selectedIndex].value;
}

</script>

<!-- add the add_rejection_reason/delete_rejection_reason functionality  -->
<script type="text/javascript">
// remove function for the jquery clone plugin
$(function(){
   var removeLink = '<a class="remove" href="#" onclick="$(this).parent().slideUp(function(){ $(this).remove() }); return false"><?php echo elgg_echo("delete");?></a>';
   $('a.add').relCopy({ append: removeLink});
});
</script>

<script type="text/javascript" src="<?php echo elgg_get_site_url(); ?>mod/vacancy/lib/reCopy.js"></script>
