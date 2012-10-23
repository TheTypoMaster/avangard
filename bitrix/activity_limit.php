<?if (php_sapi_name()=="cgi") header("Status: 503 Service Unavailable");  else header("HTTP/1.1 503 Service Unavailable");?>
<html>
<head>
<title>503 Service Temporarily Unavailable</title>
</head>
<body>
<h1>Service Temporarily Unavailable</h1>
You have made too many requests per second.
</body></html>
<?die();?>