<div class="contentWrapper">
    <?php
    $action = "vacancy/filter";
    $user_guid = elgg_get_logged_in_user_guid();
    $user = get_entity($user_guid);  

    $container_guid = $vars['container_guid'];
    $container = get_entity($container_guid);
    $hidden_container_guid = elgg_view('input/hidden', array('name' => 'container_guid', 'value' => $container_guid));

    $url = elgg_get_site_url() . "vacancy/filter/$container_guid";

    $sectors_settings = elgg_get_plugin_setting('sectors','vacancy');
    $sectors = explode(';',$sectors_settings);

    $sex =  array('Male' => elgg_echo("vacancy:sex_Male"),'Female' => elgg_echo("vacancy:sex_Female"), 'Other' => elgg_echo("vacancy:sex_Other"),'Indifferent' => elgg_echo("vacancy:sex_Indifferent"));

    $language = array('EnglishBasic' => elgg_echo("vacancy:language_EnglishBasic"), 'EnglishIntermediate' => elgg_echo("vacancy:language_EnglishIntermediate"), 'EnglishHigh' => elgg_echo("vacancy:language_EnglishHigh"), 'SpanishBasic' => elgg_echo("vacancy:language_SpanishBasic"), 'SpanishIntermediate' => elgg_echo("vacancy:language_SpanishIntermediate"), 'SpanishHigh' => elgg_echo("vacancy:language_SpanishHigh"), 'FrenchBasic' => elgg_echo("vacancy:language_FrenchBasic"), 'FrenchIntermediate' => elgg_echo("vacancy:language_FrenchIntermediate"), 'FrenchHigh' => elgg_echo("vacancy:language_FrenchHigh"), 'GermanBasic' => elgg_echo("vacancy:language_GermanBasic"), 'GermanIntermediate' => elgg_echo("vacancy:language_GermanIntermediate"), 'GermanHigh' => elgg_echo("vacancy:language_GermanHigh"), 'PortugueseBasic' => elgg_echo("vacancy:language_PortugueseBasic"), 'PortugueseIntermediate' => elgg_echo("vacancy:language_PortugueseIntermediate"), 'PortugueseHigh' => elgg_echo("vacancy:language_PortugueseHigh"), 'ItalianBasic' => elgg_echo("vacancy:language_ItalianBasic"), 'ItalianIntermediate' => elgg_echo("vacancy:language_ItalianIntermediate"), 'ItalianHigh' => elgg_echo("vacancy:language_ItalianHigh"), 'GalicianBasic' => elgg_echo("vacancy:language_GalicianBasic"), 'GalicianIntermediate' => elgg_echo("vacancy:language_GalicianIntermediate"), 'GalicianHigh' => elgg_echo("vacancy:language_GalicianHigh"), 'Others' => elgg_echo("vacancy:language_Others"), 'NonRequired' => elgg_echo("vacancy:language_NonRequired"));  

      $tele = array('Yes' => elgg_echo("vacancy:teleworking_Yes"), 'Negotiable' => elgg_echo("vacancy:teleworking_Negotiable"), 'No' => elgg_echo("vacancy:teleworking_No"));  

      $driving_license = array('Required' => elgg_echo("vacancy:license_Required"),'Valued' => elgg_echo("vacancy:license_Valued"),'Convenient' => elgg_echo("vacancy:license_Convenient"),'NonRequired' => elgg_echo("vacancy:license_NonRequired"));  
    
     $vehicle = array('CarRequired' => elgg_echo("vacancy:vehicle_CarRequired"), 'MotoRequired' => elgg_echo("vacancy:vehicle_MotoRequired"), 'CarPreferably' => elgg_echo("vacancy:vehicle_CarPreferably"), 'MotoPreferably' => elgg_echo("vacancy:vehicle_MotoPreferably"), 'VehicleValuedConvenient' => elgg_echo("vacancy:vehicle_VehicleValuedConvenient"), 'VehicleNonRequired' => elgg_echo("vacancy:vehicle_VehicleNonRequired"));  

      $travelling = array('Yes' => elgg_echo("vacancy:travelling_Yes"), 'OnlyEU' => elgg_echo("vacancy:travelling_OnlyEU"), 'Valued' => elgg_echo("vacancy:travelling_Valued"), 'NonRequired' => elgg_echo("vacancy:travelling_NonRequired"));  


    if (!elgg_is_sticky_form('filter_vacancy')) {
       $preferential_sex_array = array();  
       $preferential_sex = "";

        $language_requirements_array = array();  
        $language_requirements = "";   
        $teleworking_array = array();  
        $teleworking = "";   
        $driving_license_requirements_array = array();  
        $driving_license_requirements = "";   
        $own_vehicle_requirements_array = array();  
        $own_vehicle_requirements = "";   
        $travelling_availability_array = array();  
        $travelling_availability = "";   

       $vacancy_sector = get_input('selected_vacancy_sector');
       if ((!$vacancy_sector)&&($sectors))
          $vacancy_sector = $sectors[0];

       $vacancy_company_guid_array = array();	  
       $vacancy_company_guid = "";

    } else {
       $preferential_sex_array = elgg_get_sticky_value('filter_vacancy', 'preferential_sex');  
       $preferential_sex = implode(',',$preferential_sex_array);  

       $language_requirements_array = elgg_get_sticky_value('filter_vacancy', 'language_requirements');  
       $language_requirements = implode(',',$language_requirements_array); 
       $teleworking_array = elgg_get_sticky_value('filter_vacancy', 'teleworking'); 
       $teleworking = implode(',',$teleworking_array);  
       $driving_license_requirements_array = elgg_get_sticky_value('filter_vacancy', 'driving_license_requirements');  
       $driving_license_requirements = implode(',',$driving_license_requirements_array);  
       $own_vehicle_requirements_array = elgg_get_sticky_value('filter_vacancy', 'own_vehicle_requirements');  
       $own_vehicle_requirements = implode(',',$own_vehicle_requirements_array); 
       $travelling_availability_array = elgg_get_sticky_value('filter_vacancy', 'travelling_availability');  
       $travelling_availability = implode(',',$travelling_availability_array); 

       $vacancy_sector = elgg_get_sticky_value('edit_vacancy', 'vacancy_sector');

       $vacancy_company_guid_array = elgg_get_sticky_value('edit_vacancy', 'vacancy_company_guid');
       $vacancy_company_guid = implode(',',$vacancy_company_guid_array);

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


    elgg_clear_sticky_form('filter_vacancy');

    $submit_input = elgg_view('input/submit', array('name' => 'submit', 'value' => elgg_echo('vacancy:filter')));
    ?>

    <form action="<?php echo $vars['url'] . "action/" . $action ?>" name="filter_vacancy" enctype="multipart/form-data" method="post">

    <?php echo elgg_view('input/securitytoken'); ?>

      <p>
        <b><?php echo elgg_echo("vacancy:sector"); ?></b><br>
            <select name="vacancy_sector" onchange="vacancy_reload_filter_form(this)">
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
            <select multiple name="vacancy_company_guid[]">  
            <?php
            foreach ($companies_sector as $one_company) {
               $one_company_guid = $one_company->getGUID();
               $one_company_title = $one_company->title;
               ?>
               <option value="<?php echo $one_company_guid; ?>" <?php if (in_array($one_company_guid,$vacancy_company_guid_array)) echo "selected=\"selected\""; ?>> <?php echo $one_company_title; ?> </option>
               <?php
            }
            ?>
            </select>
            </p>



     <p>
            <br><b><?php echo elgg_echo("vacancy:preferential_sex"); ?></b><br>
      
        <select multiple name="preferential_sex[]">
    <?php
        foreach ($sex as $one_sex) {   
           ?>
           <option value="<?php echo $one_sex; ?>" <?php if (in_array($one_sex,$preferential_sex_array)) echo "selected=\"selected\""; ?>> <?php echo $one_sex; ?> </option>
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
           <option value="<?php echo $one_vehicle_key ; ?>" <?php if (in_array($one_vehicle_key,$own_vehicle_requirements_array)) echo "selected=\"selected\""; ?>> <?php echo $one_vehicle; ?> </option>
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
            <b><?php echo elgg_echo('vacancy:tags_label'); ?></b>
        </p>
        <p>
            <?php
                vacancy_tagcloud($vacancy_sector);
            ?>
        </p>
        <p>
            <?php echo elgg_view('input/text', array('name' => 'tags', 'internalid' => 'tags', 'value' => $tags)); ?>
        </p>



    <br><br>

    <?php 
    echo "$submit_input";
    echo $hidden_container_guid; 
    ?>

</div>

<?php

function vacancy_tagcloud($vacancy_sector)
{
    $cloud = "";
    $max = 0;
    //Search all the vacancies of all the companies of this sector
    $options = array('types' => 'object','subtypes' => 'vacancy','metadata_name_value_pairs' => array(array('name' => 'sector', 'value' => $vacancy_sector)));
    $vacancies = elgg_get_entities_from_metadata($options);
    $my_tags = array();
    $my_tags_counts = array();
    $num_tags = 0;
    foreach ($vacancies as $one_vacancy) {
        $tags = $one_vacancy->tags;
        if (!empty($tags)) {
            if (is_array($tags)) {
                foreach ($tags as $tag) {
                    if (!in_array($tag, $my_tags)) {
                        $my_tags[] = $tag;
                        $my_tags_count[$tag] = 1;
                    } else {
                        $my_tags_count[$tag] = $my_tags_count[$tag] + 1;
                    }
                    $num_tags = $num_tags + 1;
                }
            } else {
                if (!in_array($tags, $my_tags)) {
                    $my_tags[] = $tags;
                    $my_tags_count[$tags] = 1;
                } else {
                    $my_tags_count[$tags] = $my_tags_count[$tags] + 1;
                }
                $num_tags = $num_tags + 1;
            }
        }
    }
    if (!empty($my_tags)) {
        foreach ($my_tags as $tag) {
            $total = $my_tags_count[$tag];
            if ($total > $max)
                $max = $total;
            if (!empty($cloud)) {
                $cloud .= ", ";
            }
            $tag_size = round((log($total) / log($max + .0001)) * 100) + 30;
            if ($tag_size < 60) {
                $tag_size = 60;
            }
            //Aquí es donde se deja de apuntar a alguna url al poner #
            $cloud .= "<a href=\"#\" onclick=\"vacancy_get_this_tag(this)\" style=\"font-size: {$tag_size}%\"title=\"" . addslashes($tag) . " ({$total})\"style=\"text-decoration:none;\">" . htmlentities($tag, ENT_QUOTES, 'UTF-8') . "</a>";
        }
        echo("<div class=\"vacancy_frame\">");
        echo $cloud;
        echo("</div>");
    }
}

?>

<script language="javascript">

    function vacancy_get_this_tag(link) {
        var this_tag = link.innerHTML;
        var previous_tags = document.getElementById('tags').value;
        if (previous_tags == "")
            document.filter_vacancy.tags.value = this_tag;
        else
            document.filter_vacancy.tags.value = previous_tags + "," + this_tag;
    }

</script>

<script language="javascript">

function vacancy_reload_filter_form(select) { 
   location.href = "<?php echo $url; ?>" + "&selected_vacancy_sector=" + select.options[select.selectedIndex].value;
}

</script>