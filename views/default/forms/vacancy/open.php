<div class="contentWrapper">

<?php
$user_guid = elgg_get_logged_in_user_guid();
$url = elgg_get_site_url() . "vacancy/open";

$sectors_settings = elgg_get_plugin_setting('sectors','vacancy');
$sectors = explode(';',$sectors_settings);

$selected_sector = get_input('selected_sector');
if ((!$selected_sector)&&($sectors))
   $selected_sector = $sectors[0];
 
?>
<p>
<b><?php echo elgg_echo('vacancy:select_sector'); ?></b>
</p>   

<p>
<select name="selected_sector" onchange="vacancy_reload_open(this)">  
<?php
foreach ($sectors as $one_sector) {
   ?>
   <option value="<?php echo $one_sector; ?>" <?php if ($one_sector == $selected_sector) echo "selected=\"selected\""; ?>> <?php echo $one_sector; ?> </option>
    <?php
}
?>
</select>
</p>
<?php

//Search all the vacancies
$options = array(
    'types' => 'object',
    'subtypes' => 'vacancy',
     'metadata_name_value_pairs' => array(
        array('name' => 'sector', 'value' => $selected_sector)),
);
$vacancies = elgg_get_entities_from_metadata($options);

$limit = 10;
$i = 0;
foreach ($vacancies as $one_vacancy){
    if (vacancy_check_status($one_vacancy)){
      echo elgg_view("object/vacancy",array('full_view' => false,'entity'=>$one_vacancy));
      $i=$i+1;
   }
   if ($i==$limit)
      break;
}

//If there is not results
if ($i==0) {
    echo '<p>' . elgg_echo('vacancy:none') . '</p>';
}

?>
</div>

<script language="javascript">

function vacancy_reload_open(select) {  
   location.href = "<?php echo $url; ?>" + "&selected_sector=" + select.options[select.selectedIndex].value;
}

</script>