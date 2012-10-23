<html>

<head>
  <title>Схема проезда</title>
<script>
		window.name='shema';

         function maplist(a)
         {
         	id=window.open(a,'list');
         	id.focus();
         }

</script>

<meta http-equiv="Content-Type" content="text/html; charset=windows-1251"></head>
<body>
<table style="width:100%; height:100%;"><tr><td style="width:100%; height:100%; vertical-align:middle; text-align:center;">
<img src="<?=$_REQUEST["shema"]?>" alt="Схема проезда" title="Схема проезда" border="0">
<br />
<br />
<a href="javascript:window.close();" style="font-size:11px; color:#666666; font-family:tahoma;">[ Закрыть ]</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:window.print();" style="font-size:11px; color:#666666; font-family:tahoma;">[ Распечатать ]</a>
</td></tr></table>
</body>
</html>
