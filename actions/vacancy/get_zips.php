<?php

$vacancypost = get_input('vacancypost');
$vacancy = get_entity($vacancypost);

if ($vacancy) {

    $options = array('relationship' => 'zips_file_link', 'relationship_guid' => $vacancypost, 'inverse_relationship' => false, 'type' => 'object', 'limit' => 0);
    $files_zips = elgg_get_entities_from_relationship($options);
    if (empty($files_zips)) {
        register_error(elgg_echo("vacancy:zip_notfound"));
        forward($_SERVER['HTTP_REFERER']);
    } else {
        $file_zips = $files_zips[0];
        $filename=$file_zips->getFilenameOnFilestore();

        $all_users_filename = "vacancy_archives.zip";
        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=\"$all_users_filename\"");
        header("Content-Transfer-Encoding: binary");
        header('Content-Length: ' . filesize($filename));
        $well = readfile($filename);
        if (!$well) {
            register_error(elgg_echo("vacancy:zip_notfound"));
            forward($_SERVER['HTTP_REFERER']);
        }
    }
} else {
    register_error(elgg_echo("vacancy:notfound"));
    forward($_SERVER['HTTP_REFERER']);
}
