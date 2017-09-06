<div class="contentWrapper">
    <?php
    $action = "vacancy/add_company";
    $user_guid = elgg_get_logged_in_user_guid();

    $conf_representative = elgg_echo("vacancy:conf_representative");   

    $sectors_settings = elgg_get_plugin_setting('sectors','vacancy');
    $sectors = explode(';',$sectors_settings);

    $container_guid = $vars['container_guid'];
    $hidden_container_guid = elgg_view('input/hidden', array('name' => 'container_guid', 'value' => $container_guid));
    if (!elgg_is_sticky_form('add_company')) {
       $title = "";
       $NIF = "";
       $URL_comp = "";  
       $representative_name = ""; 
       $representative_liability = ""; 
       $representative_email = ""; 
       $representative_tel = ""; 
       $access_id = "";
       $company_sectors_array = array(); 
       $company_sectors = ""; 
       $tags = "";   
    } else {
       $title = elgg_get_sticky_value('add_company', 'title');
       $NIF = elgg_get_sticky_value('add_company', 'NIF'); 
       $URL_comp = elgg_get_sticky_value('add_company', 'URL_comp');
       $representative_name = elgg_get_sticky_value('add_company', 'representative_name'); 
       $representative_liability = elgg_get_sticky_value('add_company', 'representative_liability'); 
       $representative_email = elgg_get_sticky_value('add_company', 'representative_email'); 
       $representative_tel = elgg_get_sticky_value('add_company', 'representative_tel'); 
       $access_id = elgg_get_sticky_value('add_company', 'access_id');
       $company_sectors_array =  elgg_get_sticky_value('add_company', 'company_sectors');  
       $company_sectors = implode(';',$company_sectors_array);  
       $tags = elgg_get_sticky_value('add_company', 'companytags');  
    }

    elgg_clear_sticky_form('add_company');

    $tag_label = elgg_echo('tags');  
    $tag_input = elgg_view('input/tags', array('name' => 'companytags', 'value' => $tags));  
  
    $access_label = elgg_echo('access:read');
    $access_input = elgg_view('input/access', array('name' => 'access_id', 'value' => $access_id));

    $submit_input = elgg_view('input/submit', array('name' => 'submit', 'value' => elgg_echo('vacancy:save')));
    ?>

    <form action="<?php echo $vars['url'] . "action/" . $action ?>" name="add_company" enctype="multipart/form-data" method="post">

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

        <?php echo "$submit_input";?>

        <?php echo $hidden_container_guid; ?>
   
    </form>
</div>



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

