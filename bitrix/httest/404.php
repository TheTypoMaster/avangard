<?php 
if(@php_sapi_name()=="cgi") 
	header("Status: 200 OK"); 
else 
	header("HTTP/1.1 200 OK");
echo "SUCCESS"; 
?>
