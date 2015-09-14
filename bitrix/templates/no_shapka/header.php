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
<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/bitrix/templates/second/js/jquery.mousewheel-3.0.2.pack.js"></script>
<script type="text/javascript" src="/libs/shift.js"></script>
<script type="text/javascript" src="/flash/swfobject.js"></script>
        <script type="text/javascript">
            swfobject.embedSWF("/flash/188x134.swf?link1=/8days/", "flash_container_id", "188", "134", "6.0.0");
</script>

<link href="/libs/scroll.css" type="text/css" rel="stylesheet" />
<script src="/libs/js/dw_event.js" type="text/javascript"></script>
<script src="/libs/js/dw_scroll.js" type="text/javascript"></script>
<script src="/libs/js/dw_scrollbar.js" type="text/javascript"></script>
<script src="/libs/js/scroll_controls.js" type="text/javascript"></script>
<script src="/libs/js/html_att_ev.js" type="text/javascript"></script>

<script type="text/javascript">

window.onload = scroll_start;

function scroll_start()
{
t = setTimeout("dw_scrollObj.initScroll('wn','right', 100)",1);
}        

function init_dw_Scroll() {
    var wndo = new dw_scrollObj('wn', 'lyr1', 't1');

   wndo.setUpScrollbar("dragBar", "track", "h", 1, 1);
   wndo.setUpScrollControls('scrollbar'); 
     <?if($_GET[id]) {?> 

 var wndo = dw_scrollObj.col['wn'];
    var el = document.getElementById('divan<?echo $_GET[id];?>');
    if (el) {
        
        var lyr = document.getElementById('lyr1');
        var x = dw_getLayerOffset(el, lyr, 'left');
        var y = dw_getLayerOffset(el, lyr, 'top');
        wndo.initScrollToVals(x, y, 500);
    }
<?}?>
wndo.on_scroll_end = function () {
dw_scrollObj.scrollToId('wn', 'n_1', 'lyr1', 200);
clearTimeout(t);
}
wndo.on_glidescroll_end = function() {
scroll_start()
}

}

// if code supported, link in the style sheet and call the init function onload
if ( dw_scrollObj.isSupported() ) {
    //dw_writeStyleSheet('css/scroll.css');
    dw_Event.add( window, 'load', init_dw_Scroll);
}


</script>


</head>
<body>
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-W86TKX"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-W86TKX');</script>
<!-- End Google Tag Manager -->

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
