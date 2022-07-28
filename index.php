<?php
require '_variables_.php';
require 'functions.php';

$con = mysqli_connect(HOST, USER, PASSWORD, DBNAME);
if(!$con){
    echo 'DATABASE Error: database not connected!<br/>';
}
CreatingSitemapsFolder();
if(count($tables) > 0){
    foreach($tables as $key=>$value){
        FetchingData($con, $key, $value);
    }
}else{
    AddStaticURLs();
    WriteOffFile($xml_url_tags);
}
PrintDetails();
CreatingIndexOfSitemaps();
