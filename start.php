<?php

// Make sure the vacancy initialisation function is called on initialisation
elgg_register_event_handler('init', 'system', 'vacancy_init');

class ApplicationsVacancyPluginFile extends ElggFile
{
    protected function initialiseAttributes()
    {
        parent::initialise_attributes();
        $this->attributes['subtype'] = "vacancy_application_file";
        $this->attributes['class'] = "ElggFile";
    }

    public function __construct($guid = null)
    {
        if ($guid && !is_object($guid)) {
            // Loading entities via __construct(GUID) is deprecated, so we give it the entity row and the
            // attribute loader will finish the job. This is necessary due to not using a custom
            // subtype (see above).
            $guid = get_entity_as_row($guid);
        }
        parent::__construct($guid);
    }
}


class ZipsVacancyPluginFile extends ElggFile   
{
    protected function initialiseAttributes()
    {
        parent::initialise_attributes();
        $this->attributes['subtype'] = "vacancy_zips_file";
        $this->attributes['class'] = "ElggFile";
    }

    public function __construct($guid = null)
    {
        if ($guid && !is_object($guid)) {
            // Loading entities via __construct(GUID) is deprecated, so we give it the entity row and the
            // attribute loader will finish the job. This is necessary due to not using a custom
            // subtype (see above).
            $guid = get_entity_as_row($guid);
        }
        parent::__construct($guid);
    }
}

/**
 * Vacancy initialization
 */
function vacancy_init() {

// Set up menu for logged in users
    $item = new ElggMenuItem('vacancy', elgg_echo('vacancies'), 'vacancy/all');
    elgg_register_menu_item('site', $item);

// Add a menu item to the user ownerblock
    elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'vacancy_owner_block_menu');

// Extend system CSS with our own styles, which are defined in the vacancy/css view
    elgg_extend_view('css/elgg', 'vacancy/css');

// Register a page handler, so we can have nice URLs
    elgg_register_page_handler('vacancy', 'vacancy_page_handler');

// Register a URL handler for vacancies posts
    elgg_register_plugin_hook_handler('entity:url', 'object', 'vacancy_url');

// Register entity types
    elgg_register_entity_type('object', 'company');
    elgg_register_entity_type('object', 'vacancy');
    elgg_register_entity_type('object', 'vacancy_applications');
    elgg_register_entity_type('object', 'vacancy_application_file');
    elgg_register_entity_type('object', 'vacancy_zips_file');

// Add group menu option
    add_group_tool_option('vacancy', elgg_echo('vacancy:enable_group_vacancies'), true);
    elgg_extend_view('groups/tool_latest', 'vacancy/group_module');

// Advanced permissions
    elgg_register_plugin_hook_handler('permissions_check', 'object', 'vacancy_permissions_check');

// Add items to menu filter
    elgg_register_plugin_hook_handler('register', 'menu:filter', 'vacancy_add_groups');
    elgg_register_plugin_hook_handler('register', 'menu:filter', 'vacancy_add_opens');

// Remove items from menu filter
    elgg_register_plugin_hook_handler('register', 'menu:filter', 'vacancy_delete_friends');

// Register a plugin hook handler for the entity menu
    elgg_register_plugin_hook_handler('register', 'menu:entity', 'register_my_entity_menu_handler');

// Add a widget
   elgg_register_widget_type('vacancy', elgg_echo('vacancies'), elgg_echo('vacancy:widget:description'));

// Register library
   elgg_register_library('vacancy', elgg_get_plugins_path() . 'vacancy/lib/vacancy_lib.php');

run_function_once("vacancy_application_file_add_subtype_run_once");
run_function_once("vacancy_zips_file_add_subtype_run_once"); 

}

function vacancy_application_file_add_subtype_run_once() 
{ 
    add_subtype("object", "vacancy_application_file", "ApplicationsVacancyPluginFile"); 
}

function vacancy_zips_file_add_subtype_run_once()
{ 
    add_subtype("object", "vacancy_zips_file", "ZipsVacancyPluginFile");
}


/**
 * Advanced permissions
 */
function vacancy_permissions_check($hook, $type, $return, $params) {
    if (($params['entity']->getSubtype() == 'vacancy')||($params['entity']->getSubtype() == 'vacancy_application')||($params['entity']->getSubtype() == 'vacancy_application_file')||($params['entity']->getSubtype() == 'form_answer')||($params['entity']->getSubtype() == 'form_response_file')) {
      $user_guid = elgg_get_logged_in_user_guid();
      $container_guid = $params['entity']->container_guid;
      $container = get_entity($container_guid);
      if ($container instanceof ElggGroup) {
         $group_owner_guid = $container->owner_guid;
         $operator=false;
         if (($group_owner_guid==$user_guid)||(check_entity_relationship($user_guid,'group_admin',$container_guid))){
            $operator=true;
         }
         if ($operator){
            return true;
	 }   
      } else {
         if (($params['entity']->getSubtype() == 'vacancy_application')||($params['entity']->getSubtype() == 'vacancy_application_file')) { 
            $vacancy_guid = $params['entity']->vacancy_guid;
	    $vacancy = get_entity($vacancy_guid);
	    if ($vacancy->owner_guid == $user_guid)
	       return true;
         }
      }
   }	
}

/**
 * Add a menu item to the user ownerblock
 */
function vacancy_owner_block_menu($hook, $type, $return, $params) {
    if (elgg_instanceof($params['entity'], 'user')) {
        $url = "vacancy/owner/{$params['entity']->username}";
        $item = new ElggMenuItem('vacancy', elgg_echo('vacancies'), $url);
        $return[] = $item;
    } else {
        if ($params['entity']->vacancy_enable != "no") {
            $url = "vacancy/group/{$params['entity']->guid}/all";
            $item = new ElggMenuItem('vacancy', elgg_echo('vacancy:group'), $url);
            $return[] = $item;
        }
    }
    return $return;
}

/**
 * Add items to menu filter
 */
function vacancy_add_groups($hook, $type, $return, $params) {
    if ((elgg_get_context() === 'vacancy')) {
        $return[] = ElggMenuItem::factory(array(
                    'name' => 'groups',
                    'href' => "vacancy/groups",
                    'text' => elgg_echo('vacancy:my_groups'),
                    'priority' => 1000,
        ));
    }
    return $return;
}

function vacancy_add_opens($hook, $type, $return, $params) {
    if ((elgg_get_context() === 'vacancy')) {
        $return[] = ElggMenuItem::factory(array(
                    'name' => 'open',
                    'href' => "vacancy/open",
                    'text' => elgg_echo("vacancy:opened_vacancies"),
                    'priority' => 1050,
        ));
    }
    return $return;
}

/**
 * Remove items from menu filter
 */
function vacancy_delete_friends($hook, $type, $return, $params) {
    if ((elgg_get_context() === 'vacancy')) {
	$remove = array('friend');
        foreach($return as $key => $item) {
            if (in_array($item->getName(), $remove)) {
                unset($return[$key]);
            }
        }
    }
    return $return;	
}

/**
 * Vacancy page handler; allows the use of fancy URLs
 *
 * @param array $page From the page_handler function
 * @return true|false Depending on success
 */
function vacancy_page_handler($page) {
    if (isset($page[0])) {
        elgg_push_breadcrumb(elgg_echo('vacancies'));
        $base_dir = elgg_get_plugins_path() . 'vacancy/pages/vacancy';
        switch ($page[0]) {
            case "view":
                set_input('guid', $page[1]);
                include "$base_dir/read.php";
                break;
            case "owner":
                set_input('username', $page[1]);
                include "$base_dir/owner.php";
                break;
            case "group":
                set_input('container_guid', $page[1]);
                include "$base_dir/owner.php";
                break;
            case "all":
                include "$base_dir/everyone.php";
                break;
            case "add":
                set_input('container_guid', $page[1]);
                include "$base_dir/add.php";
                break;
            case "edit":
                set_input('guid', $page[1]);
                include "$base_dir/edit.php";
                break;
            case "filter":
                set_input('container_guid', $page[1]);
                include "$base_dir/filter.php";
                break;
	    case "show_filter":
                set_input('container_guid', $page[1]);
                include "$base_dir/show_filter.php";
                break;
            case "groups":
                include "$base_dir/groups.php";
                break;
            case "open":
                include "$base_dir/open.php";
                break;
            case 'add_company':
               set_input('container_guid',$page[1]);
               include "$base_dir/add_company.php";
               break;
	    case 'edit_company':
               set_input('container_guid',$page[1]);
               include "$base_dir/edit_company.php";
               break;
            case 'accept_reject':
	       set_input('guid', $page[1]);
	       set_input('member_guid', $page[2]);
	       set_input('offset', $page[3]);
	       include "$base_dir/accept_reject.php";	
	       break;
            default:
                return false;
        }
    } else {
        forward();
    }
    return true;
}

/**
 * Populates the ->getUrl() method for vacancy objects
 *
 * @param ElggEntity $vacancy Vacancy entity
 * @return string Vacancy URL
 */
function vacancy_url($hook, $type, $url, $params) {
    $entity = $params['entity'];
    // Check that the entity is a vacancy object
    if ($entity->getSubtype() !== 'vacancy') {
        return;
    }
    $title = elgg_get_friendly_title($entity->title);
    $url = elgg_get_config('url');
    return $url . "vacancy/view/" . $entity->getGUID() . "/" . $title;
}


// Vacancy opened or closed?
function vacancy_check_status($vacancy)
{
    if ((strcmp($vacancy->option_close_value, 'vacancy_close_date') == 0)) {
        $now = time();
        if (($now >= $vacancy->activate_time) && ($now < $vacancy->close_time)) {
            return true;
        } else {
            if ($vacancy->action == true) {
                $vacancy->option_close_value = '';
                $vacancy->action = false;
                $vacancy->opened = true;
                return true;
            }
            return false;
        }
    } else {
        $vacancy->action = false;
        return $vacancy->opened;
    }
}

// Register actions
$action_base = elgg_get_plugins_path() . 'vacancy/actions/vacancy';
elgg_register_action("vacancy/edit", "$action_base/edit.php", 'logged_in');
elgg_register_action("vacancy/delete", "$action_base/delete.php", 'logged_in');
elgg_register_action("vacancy/add_company", "$action_base/add_company.php", 'logged_in');
elgg_register_action("vacancy/edit_company", "$action_base/edit_company.php", 'logged_in');
elgg_register_action("vacancy/open", "$action_base/open.php");  
elgg_register_action("vacancy/close", "$action_base/close.php");  
elgg_register_action("vacancy/apply", "$action_base/apply.php");  
elgg_register_action("vacancy/delete_application", "$action_base/delete_application.php"); 
elgg_register_action("vacancy/get_zips", "$action_base/get_zips.php");  
elgg_register_action("vacancy/zip_all", "$action_base/zip_all.php");  
elgg_register_action("vacancy/filter", "$action_base/filter.php"); 
elgg_register_action("vacancy/accept_reject", "$action_base/accept_reject.php");