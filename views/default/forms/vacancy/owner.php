<div class="contentWrapper">

<?php

//Get variables
$owner = $vars['owner'];
$user_guid = elgg_get_logged_in_user_guid();
$url = elgg_get_site_url() . "vacancy/owner/$owner->username";

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
<select name="selected_sector" onchange="vacancy_reload_owner(this)">  
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
$limit = 10;
if (elgg_instanceof($owner, 'group')) {
    $options = array(
    'types' => 'object',
    'subtypes' => 'vacancy',
    'container_guid' => $owner->getGUID(),
     'metadata_name_value_pairs' => array(
        array('name' => 'sector', 'value' => $selected_sector)),
    'limit' => $limit,
    'full_view' => false);
} else {
    $options = array(
    'types' => 'object',
    'subtypes' => 'vacancy',
    'owner_guid' => $owner->getGUID(),
     'metadata_name_value_pairs' => array(
        array('name' => 'sector', 'value' => $selected_sector)),
    'limit' => $limit,
    'full_view' => false);
}
$vacancies = elgg_list_entities_from_metadata($options);
//If there is not results
if (!$vacancies) {
    $vacancies = '<p>' . elgg_echo('vacancy:none') . '</p>';
}
echo $vacancies;

?>
</div>

<script language="javascript">

function vacancy_reload_owner(select) {  
   location.href = "<?php echo $url; ?>" + "&selected_sector=" + select.options[select.selectedIndex].value;
}

</script>