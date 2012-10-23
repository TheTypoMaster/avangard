<html>
<head>
<?$APPLICATION->ShowMeta("keywords")?>
<?$APPLICATION->ShowMeta("description")?>
<title><?$APPLICATION->ShowTitle()?></title>
<?$APPLICATION->ShowCSS();?>
<?$APPLICATION->ShowHeadStrings()?>
<?$APPLICATION->ShowPanel();?>
<? if(CModule::IncludeModule('iblock')) $incl="Y"; ?>

<script type="text/javascript" src="/libs/highslide.js"></script>
<link rel="stylesheet" type="text/css" href="/libs/highslide.css" />
<script type="text/javascript">
	hs.graphicsDir = '/libs/graphics/';
	hs.wrapperClassName = 'wide-border';

var agt   = navigator.userAgent.toLowerCase();
var is_ie = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));

function hide_mine(elmnt) {
	if( !is_ie ) return;
	var a = elmnt.getElementsByTagName("div");
	var div = a[0];
	elmnt.style.zIndex = 1;
	div.style.display = "none";
}

function show_mine(elmnt) {
	if( !is_ie ) return;
	var a = elmnt.getElementsByTagName("div");
	var div = a[0];
	elmnt.style.zIndex = 100;
	div.style.display = "block";
}

</script>


</script>

</head>
<? if(CModule::IncludeModule('iblock')) $incl="Y"; ?>
<body>
