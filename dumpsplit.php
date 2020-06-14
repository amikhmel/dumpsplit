<?php
if(!isset($argv[1])){
	exit("Source file not specified.\n");
}

$source_file = $argv[1];
$handle = fopen($source_file, "rb");
if (FALSE === $handle) {
    exit("Failed to open file ".$source_file."\n");
}

$line = '';
$new_db = false;
$i=0;
$file_name = 'header.sql';
$part_handle = fopen($file_name, 'a');
$db_name = 'header';
$header = '';
$copy_header = false;
$i=0;
while (!feof($handle)) {
    $line = fgets($handle);
	$i++;
	

    if((strpos($line, 'CREATE DATABASE ') === 0) || (strpos($line, 'USE ') === 0)){
    	$new_db = true;
    	$chunks = explode(' ', $line);
    	$idx = 6;
    	$info = 'Copying CREATE DATABASE';
    	if(substr($line, 0, 3) == 'USE'){
    		$idx = 1;
    		$info = 'Copying USE';

    	} 
    	$db_name = trim($chunks[$idx]);
    	echo 'Line '.$i.': '.$info." ".$db_name."\n";
    	$file_name = str_replace(array('`', '.', ';'), '', $db_name).'.sql';
    	fclose($part_handle);
    	if(!file_exists($file_name)){
    		$copy_header = true;

    	}
    	$part_handle = fopen($file_name, 'a');
    	if($copy_header){
    		fwrite($part_handle, $header);
    		$copy_header = false;
    	}


    } else {
    	if(!$new_db){
			$header .= $line;
		}
    }

    
    fwrite($part_handle, $line);
	


}
fclose($part_handle);
fclose($handle);