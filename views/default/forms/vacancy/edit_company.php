<div class="contentWrapper">
    <?php  
    $action = "vacancy/edit_company";
    $user_guid = elgg_get_logged_in_user_guid();

    $conf_representative = elgg_echo("vacancy:conf_representative");   

    $sectors_settings = elgg_get_plugin_setting('sectors','vacancy');
    $sectors = explode(';',$sectors_settings);


    $container_guid = $vars['container_guid'];
    
    $url = elgg_get_site_url() . "vacancy/edit_company/$container_guid";

    $companies = elgg_get_entities(array('type' => 'object', 'subtype' => 'company', 'limit' => false, 'owner_guid' => $user_guid));

    if (!elgg_is_sticky_form('edit_company')) {
       $company_guid = get_input('selected_company_guid');
       if ((!$company_guid)&&($companies))
          $company_guid = $companies[0]->getGUID();
    } else {
       $company_guid = elgg_get_sticky_value('edit_company', 'company_guid');
    }

    ?>
    <p>
    <b><?php echo elgg_echo('vacancy:select_company'); ?></b>
    </p>   

    <p>
    <select name="company_guid" onchange="vacancy_reload_edit_company_form(this)">  
    <?php
    foreach ($companies as $one_company) {
       $one_company_guid = $one_company->getGUID();
       $one_company_title = $one_company->title;
       $one_company_NIF = $one_company->NIF;  
       $one_company_URL_comp = $one_company->URL_comp;
       $representative_name = ""; 
       $representative_liability = ""; 
       $representative_email = ""; 
       $representative_tel = ""; 
       ?>
       <option value="<?php echo $one_company_guid; ?>" <?php if ($one_company_guid == $company_guid) echo "selected=\"selected\""; ?>> <?php echo $one_company_title; ?> </option>
       <?php
    }
    ?>
    </select>
    </p>
    <?php

    if ($company_guid) {
       $company = get_entity($company_guid);
       $hidden_company_guid = elgg_view('input/hidden', array('name' => 'company_guid', 'value' => $company_guid));
       if (!elgg_is_sticky_form('edit_company')) {
          $title = $company->title;
          $NIF = $company->NIF;
          $URL_comp = $company->URL_comp;
          $representative_name = $company->representative_name;  
          $representative_liability = $company->representative_liability;  
          $representative_email = $company->representative_email;  
          $representative_tel = $company->representative_tel;  
          $tags = $company->tags;  
          $access_id = $company->access_id;
          $company_sectors = $company->sectors;
          $company_sectors_array = explode(';',$company_sectors);

       } else {
          $title = elgg_get_sticky_value('edit_company', 'title');
          $NIF = elgg_get_sticky_value('edit_company', 'NIF');
          $URL_comp = elgg_get_sticky_value('edit_company', 'URL_comp');  
          $representative_name = elgg_get_sticky_value('edit_company', 'representative_name'); 
          $representative_liability = elgg_get_sticky_value('edit_company

          ', 'representative_liability'); 
          $representative_email = elgg_get_sticky_value('edit_company', 'representative_email'); 
          $representative_tel = elgg_get_sticky_value('edit_company', 'representative_tel'); 
          $tags = elgg_get_sticky_value('edit_company', 'companytags');  
          $access_id = elgg_get_sticky_value('edit_company', 'access_id');
	        $company_sectors_array =  elgg_get_sticky_value('edit_company', 'company_sectors');
          $company_sectors = implode(';',$company_sectors_array);
       }
       
       elgg_clear_sticky_form('edit_company');

       //Prepare fields to show

       
        $tag_label = elgg_echo('tags');
        $tag_input = elgg_view('input/tags', array('name' => 'companytags', 'value' => $tags));
  
       $access_label = elgg_echo('access:read');
       $access_input = elgg_view('input/access', array('name' => 'access_id', 'value' => $access_id));

       $submit_input_save = elgg_view('input/submit', array('name' => 'submit', 'value' => elgg_echo('vacancy:save')));
       $submit_input_delete = elgg_view('input/submit', array('name' => 'submit', 'value' => elgg_echo('vacancy:delete_company')));
       ?>

       <form action="<?php echo $vars['url'] . "action/" . $action ?>" name="edit_company" enctype="multipart/form-data" method="post">

       <?php echo elgg_view('input/securitytoken'); ?>

       <p>
            <b><?php echo elgg_echo("vacancy:company_title"); ?></b><br>
            <?php echo elgg_view("input/text", array('name' => 'title', 'value' => $title)); ?>
       </p>

       

        <p>
            <b><?php echo elgg_echo("vacancy:company_NIF"); ?></b><br>
            <?php echo elgg_view("input/text", array('name' => 'NIF', 'value' => $NIF)); ?>
       </p>

      <p>
            <b><?php echo elgg_echo("vacancy:company_URL"); ?></b><br>
            <?php echo elgg_view("input/text", array('name' => 'URL_comp', 'value' => $URL_comp)); ?>
       </p>


      <p>
       <b><?php echo elgg_echo("vacancy:sector"); ?></b><br>
       <select multiple name="company_sectors[]">
       <?php
      foreach ($sectors as $one_sector) {   
           ?>
           <option value="<?php echo $one_sector; ?>" <?php if (in_array($one_sector,$company_sectors_array)) echo "selected=\"selected\""; ?>> <?php echo $one_sector; ?> </option>
           <?php
       }
       ?>
       </select>
       </p>


  
  <p><a onclick="vacancy_show_representative();" style="cursor:hand;"><?php echo elgg_echo("vacancy:company_conf_representative"); ?></a></p>     
      <div id="resultsDiv_representative" style="display:none;">     
  <p>     


       
        <p>
            <b><?php echo elgg_echo("vacancy:company_representative_name"); ?></b><br>
            <?php echo elgg_view("input/text", array('name' => 'representative_name', 'value' => $representative_name)); ?>
        </p>

        
        <p>
            <b><?php echo elgg_echo("vacancy:company_representative_liability"); ?></b><br>
            <?php echo elgg_view("input/text", array('name' => 'representative_liability', 'value' => $representative_liability)); ?>
        </p>

          
        <p>
            <b><?php echo elgg_echo("vacancy:company_representative_email"); ?></b><br>
            <?php echo elgg_view("input/text", array('name' => 'representative_email', 'value' => $representative_email)); ?>
        </p>

          
        <p>
            <b><?php echo elgg_echo("vacancy:company_representative_tel"); ?></b><br>
            <?php echo elgg_view("input/number", array('name' => 'representative_tel', 'value' => $representative_tel)); ?>
        </p>

</div>



    
       <p>
            <b><br/><?php echo $tag_label; ?></b><br>
            <?php echo $tag_input; ?>
	    </p><br>

       <p>
            <b><?php echo $access_label; ?></b><br>
            <?php echo $access_input; ?>
       </p>

        <?php echo "$submit_input_save  $submit_input_delete"; ?>

        <?php echo $hidden_company_guid; ?>

        </form>

    <?php
    }
    ?>

</div>

<script language="javascript">

function vacancy_reload_edit_company_form(select) {  
   location.href = "<?php echo $url; ?>" + "&selected_company_guid=" + select.options[select.selectedIndex].value;
}

</script>



<script type="text/javascript">
   function vacancy_show_representative(){
      var resultsDiv_representative = document.getElementById('resultsDiv_representative');
      if (resultsDiv_representative.style.display == 'none') {
         resultsDiv_representative.style.display = 'block';
      } else {  
         resultsDiv_representative.style.display = 'none';
      }
   }    
</script>
