<?php       
//Connection to MySQL database
mysql_connect("hostname", 
        "username", 
        "password"); 

mysql_select_db("database"); 

mysql_query("SET NAMES 'utf8'");

header('Content-Type: text/html; charset=utf-8');

$sites = array("site1.com", "example.com");
$sitesName = array("site1.com" => "Site 1",
"example.com" => "Example");
?>
