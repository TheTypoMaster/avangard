<?
if(substr($url,0,4)!="http" && substr($url,0,1)!="/")
	$url = "/".$url;
//This function will protect against utf-7 xss
//on page with no character setting
function htmlspecialchars_plus($str)
{
 	return str_replace("+","&#43;",htmlspecialchars($str));
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=htmlspecialchars_plus($charset)?>">
<meta http-equiv="Refresh" content="3;URL=<?=htmlspecialchars_plus($url)?>">
</head>
<body>
<div align="center"><h3><?=htmlspecialchars_plus($mess)?></h3></div>
</body>
</html>