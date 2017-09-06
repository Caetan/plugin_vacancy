
<?php

$container_guid = $vars['container_guid'];
$vacancy_sector = $vars['vacancy_sector'];
$vacancy_company_guid = $vars['vacancy_company_guid'];
$preferential_sex = $vars['preferential_sex'];

$language_requirements = $vars['language_requirements'];  
$teleworking = $vars['teleworking'];  
$driving_license_requirements = $vars['ldriving_license_requirementss'];  
$own_vehicle_requirements = $vars['own_vehicle_requirements'];  
$travelling_availability = $vars['travelling_availability'];  

$tags = $vars['tags'];
if (strcmp($tags, "") != 0)
   $tagsarray = string_to_tag_array($tags);

$container = get_entity($container_guid);

?>
<form>
<?php

//Search all the vacancies of all the companies of this sector
$options = array('types' => 'object','subtypes' => 'vacancy','metadata_name_value_pairs' => array(array('name' => 'sector', 'value' => $vacancy_sector)));
$vacancies = elgg_get_entities_from_metadata($options);

$selected_vacancies = array();
$i = 0;
foreach ($vacancies as $one_vacancy) {
    if (strcmp($tags, "") == 0) {
        $tags_found = true;
    } else {
        if (empty($one_vacancy->tags)) {
            $tags_found = false;
        } else {
            $tags_found = vacancy_comprobate_metadata($one_vacancy->tags, $tagsarray);
        }
    }
    $vacancy_company_guid_found =  vacancy_comprobate_metadata($one_vacancy->company_guid,$vacancy_company_guid);
    $preferential_sex_found = vacancy_comprobate_metadata(explode(',',$one_vacancy->preferential_sex),$preferential_sex);
   
    $language_requirements_found = vacancy_comprobate_metadata(explode(',',$one_vacancy->language_requirements),$language_requirements);  
  
    $teleworking_found = vacancy_comprobate_metadata(explode(',',$one_vacancy->teleworking),$teleworking);  
   
    $driving_license_requirements_found = vacancy_comprobate_metadata(explode(',',$one_vacancy->driving_license_requirements),$driving_license_requirements); 
  
    $own_vehicle_requirements_found = vacancy_comprobate_metadata(explode(',',$one_vacancy->own_vehicle_requirements),$own_vehicle_requirements);  
   
    $travelling_availability_found = vacancy_comprobate_metadata(explode(',',$one_vacancy->travelling_availability),$travelling_availability);  
  
   
    if (($tags_found) && ($vacancy_company_guid_found) && ($preferential_sex_found) && ($language_requirements_found) && ($teleworking_found) && ($driving_license_requirements_found) && ($own_vehicle_requirements_found)&& ($travelling_availability_found)){ 
        $selected_vacancies[$i] = $one_vacancy;
        $i = $i + 1;
    }
}

if (isset($vars['offset']))
   $offset = $vars['offset'];
else 
   $offset = 0;
$limit = 10;
$this_limit = $offset+$limit;
$count = count($selected_vacancies);

$i=0;
foreach($selected_vacancies as $one_vacancy) {
   if (($i >= $offset) && ($i < $this_limit)) 
      echo elgg_view("object/vacancy",array('full_view' => false,'entity'=>$selected_vacancies[$i]));
   $i=$i+1;
}
echo elgg_view("navigation/pagination", array('count' => $count, 'offset' => $offset, 'limit' => $limit));

?>
</form>
</div>

<?php
function vacancy_comprobate_metadata($vacancy_metadata, $selected_metadata)
{
    $found = false;
    if (is_array($selected_metadata)) {
        foreach ($selected_metadata as $one_selected_metadata) {
            if (is_array($vacancy_metadata)) {
                if (in_array($one_selected_metadata, $vacancy_metadata)) {
                    $found = true;
                    break;
                }
            } else {
                if (strcmp($one_selected_metadata, $vacancy_metadata) == 0) {
                    $found = true;
                    break;
                }
            }
        }
    } else {
        if (is_array($vacancy_metadata)) {
            if (in_array($selected_metadata, $vacancy_metadata)) {
                $found = true;
            }
        } else {
            if (strcmp($selected_metadata, $vacancy_metadata) == 0) {
                $found = true;
            }
        }
    }
    return $found;
}
?>

