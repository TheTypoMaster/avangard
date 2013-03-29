<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html  xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="shortcut icon" href="http://www.avangard.biz/favicon.ico" type="image/x-icon">
<meta name='yandex-verification' content='462691a93b33875f' />
<?$APPLICATION->ShowMeta("robots")?>
<?$APPLICATION->ShowMeta("keywords")?>
<?$APPLICATION->ShowMeta("description")?>
<title><?$APPLICATION->ShowTitle()?></title>
<?$APPLICATION->ShowCSS();?>
<?$APPLICATION->ShowHeadStrings()?>
<?$APPLICATION->ShowHeadScripts()?>
<script type="text/javascript">


var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www."); document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-9600751-1"); pageTracker._trackPageview(); } catch(err) {}
</script>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/bitrix/templates/second/js/jquery.mousewheel-3.0.2.pack.js"></script>
<script type="text/javascript" src="/libs/shift.js"></script>
<script type="text/javascript" src="/flash/swfobject.js"></script>
        <script type="text/javascript">
            swfobject.embedSWF("/flash/188x134.swf?link1=/8days/", "flash_container_id", "188", "134", "6.0.0");
</script>
</head>
<body>
 <?$APPLICATION->ShowPanel();?>
 <? $APPLICATION->AddHeadScript("/basket/basket.js"); ?>
 <? if(CModule::IncludeModule('iblock')) $incl="Y"; ?>


<div id="main_text"><center><div id="main_text_div">

  
  
<table width="100%" cellspacing="0" cellpadding="0" border="0"><tr> 
<?$left_incl = 1;?>

 <?if($APPLICATION->GetProperty('left_inc_file')!='none') :?>

 <?$left_incl = $left_incl+1;?>
<td class="left_inc_file_td">
<?$APPLICATION->IncludeFile($APPLICATION->GetProperty('left_inc_file'), array(), array(
                        "MODE"      => "html",
                        "TEMPLATE"  => "page_inc.php"
                        ));
?>
</td>

<?else:?>
 <?$left_incl = $left_incl+1;?>
<td style="padding-right: 7px;"></td>
<?endif?>
<td class="work_and_top_td">
<table cellspacing="0" cellpadding="0">
<tr><td>
<?if($APPLICATION->GetProperty('up_inc_file')!='none') :?>
<?$APPLICATION->IncludeFile($APPLICATION->GetProperty('up_inc_file'), array(), array(
                        "MODE"      => "html",
                        "TEMPLATE"  => "page_inc.php"
                        ));
?>
<?endif?>
</td></tr>

<tr>
<td class="workarea_td">
