<?php

$vacancypost = get_input('vacancypost');
$vacancy = get_entity($vacancypost);

if ($vacancy) {

    $user_guid = elgg_get_logged_in_user_guid();
    $container = get_entity($vacancy->container_guid);

    $owner = $vacancy->getOwnerEntity();
    $owner_guid = $owner->getGUID();

    set_time_limit(0);
    ini_set('memory_limit', '256M');
    $name_zip = tempnam(sys_get_temp_dir(), "zip");
    $zip = new ZipArchive();
    $zip->open($name_zip, ZIPARCHIVE::OVERWRITE);

    $some_application = false;

    $application_type = $vacancy->application_type;

    if (strcmp($application_type,"vacancy_application_type_form")==0){
       $vacancy_form_guid = $vacancy->form_guid;
       $vacancy_form = get_entity($vacancy_form_guid);
       $options = array('relationship' => 'form_answer', 'relationship_guid' => $vacancy_form_guid,'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'form_answer', 'order_by' => 'e.time_created desc', 'limit' => 0);
    } else {
       $options = array('relationship' => 'vacancy_application', 'relationship_guid' => $vacancypost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'vacancy_application', 'order_by' => 'e.time_created desc', 'limit' => 0);
    }

    $user_applications = elgg_get_entities_from_relationship($options);
    foreach ($user_applications as $one_application) {
       $member = $one_application->getOwnerEntity();
       $member_guid = $member->getGUID();
       $one_application_guid = $one_application->getGUID();

       if (strcmp($application_type,"vacancy_application_type_form")==0){
          //Questions of the form
          $options = array('relationship' => 'form_question', 'relationship_guid' => $vacancy_form_guid,'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'form_question','limit'=>0);
          $questions=elgg_get_entities_from_relationship($options);
          if (empty($questions)) {
             $num_questions=0;
          } else {
             $num_questions=count($questions);
          }
          $application_files = array();
          if ($num_questions > 0) {
             foreach($questions as $one_question){ 
                $one_question_guid = $one_question->getGUID();
                $application_files = array_merge($application_files,elgg_get_entities_from_relationship(array('relationship' => 'response_file_link', 'relationship_guid' => $one_question_guid, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'form_response_file')));
             }
          }
       } else {
          $application_files = elgg_get_entities_from_relationship(array('relationship' => 'application_file_link', 'relationship_guid' => $one_application_guid, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'vacancy_application_file'));
       }

       if ((count($application_files) > 0) && (strcmp($application_files[0]->title, "") != 0)) {
          if (!$some_application)
             $some_application = true;
          foreach ($application_files as $file) {
             $this_filename = $file->getFilenameOnFilestore();
             if (is_readable($this_filename)) {
                $zip->addFile($this_filename, $member->username . "/" . $one_application_guid . "/" . $file->title);
             } else {
                register_error(elgg_echo("vacancy:file_not_readable") . " (" . $this_filename . ")");
             }
          }
       } 
    }
    $zip->close();

    if ($some_application) {
        $options = array('relationship' => 'zips_file_link', 'relationship_guid' => $vacancypost, 'inverse_relationship' => false, 'type' => 'object', 'limit' => 0);
        $previous_files_zips = elgg_get_entities_from_relationship($options);
        foreach ($previous_files_zips as $one_file) {
            $deleted = $one_file->delete();
            if (!$deleted) {
                register_error(elgg_echo("vacancy:filenotdeleted"));
                forward($_SERVER['HTTP_REFERER']);
            }
        }

        $file_zips = new ZipsVacancyPluginFile();
        $file_zips->subtype = "vacancy_zips_file";
        $prefix = "file/";
        $name = "zips_vacancies_" . $vacancypost;
        $filestorename = elgg_strtolower(time() . $name);
        $file_zips->setFilename($prefix . $filestorename);
        $file_zips->originalfilename = $name;
        $file_zips->open("write");
        $file_zips->close();
	rename($name_zip, $file_zips->getFilenameOnFilestore());
        $file_zips->title = $name;
        $file_zips->owner_guid = $user_guid;
	if ($container instanceof ElggGroup) {
           $file_zips->container_guid = $vacancy->container_guid;
	} else {
           $file_zips->container_guid = $user_guid;
	}
        $file_zips->access_id = $vacancy->access_id;
        $file_zips->save();

        add_entity_relationship($vacancypost, 'zips_file_link', $file_zips->getGUID());
    }


    if (!$some_application) {
        register_error(elgg_echo("vacancy:application_notfound"));
        forward($_SERVER['HTTP_REFERER']);
    }

 } else {
    register_error(elgg_echo("vacancy:notfound"));
    forward($_SERVER['HTTP_REFERER']);
}
