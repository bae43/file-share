<?php

include_once('zip.php');

$zip_file = 'my_files.zip'; // name for downloaded zip file

$ziper = new zipfile();
$ziper->prefix_name = 'folder/'; // here you create folder which will contain downloaded files
$ziper->addFiles($files_to_zip);  // array of files
$ziper->output($zip_file); 
$ziper->forceDownload($zip_file);
@unlink($zip_file);

?>