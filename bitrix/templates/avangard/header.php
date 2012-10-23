<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<html>
<head>
<meta name='yandex-verification' content='462691a93b33875f' />
<?$APPLICATION->ShowMeta("robots")?>
<?$APPLICATION->ShowMeta("keywords")?>
<?$APPLICATION->ShowMeta("description")?>
<title><?$APPLICATION->ShowTitle()?></title>
<?$APPLICATION->ShowCSS();?>
<?$APPLICATION->ShowHeadStrings()?>

<link rel="icon" href="http://avangard.biz/favicon.ico" type="image/x-icon"> 
<link rel="shortcut icon" href="http://avangard.biz/favicon.ico" type="image/x-icon"> 

<?/*<script type="text/javascript" src="/flash/swfobject.js"></script>
        <script type="text/javascript">
            swfobject.embedSWF("/flash/188x134.swf?link1=/8days/", "flash_container_id", "188", "134", "6.0.0");


</script>*/?>

<script type="text/javascript"> 
massive_length=6;/*устанавливаешь длинну массива, т.е. сколько цветов будет*/ 
colors_= new Array(massive_length); 
colors_[0] = "#000000"; 
colors_[1] = "#cc0000";  
colors_[2] = "#f0b2b2"; 
colors_[3] = "#ffffff"; 
colors_[4] = "#f0b2b2"; 
colors_[5] = "#cc0000"; 
var next_ = 0; 
function Changehead() 
{ 
headcolor= colors_[next_];/*headcolor - переменной устанавливаешь новый цвет*/ 

document.getElementById("head1").style.color=headcolor;/*присваеваешь этот цвет элементу в документе*/ 
next_++; 
if(next_>massive_length-1) next_=0; 
document.getElementById("head2").style.color=headcolor;/*присваеваешь этот цвет элементу в документе*/ 
window.setTimeout("Changehead()",250); /*спустя 1 секунду, меняешь цвет на новый, если нужно дольше, то ставишь число больше*/ 
} 
</script> 
<script type="text/javascript">document.write(unescape("%3Cscript src='" + (("https:" == document.location.protocol) ? "https" : "http") + "://a.mouseflow.com/projects/7ccdfd23-7475-49c4-b767-dcf9da14fd83.js' type='text/javascript'%3E%3C/script%3E"));</script>
</head>


<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="Changehead();/*скрипт запускается когда документ загрузился полностью*/">
<div id="panel"><?$APPLICATION->ShowPanel();?></div>


<table width="100%" height="" border="0" cellpadding="0" cellspacing="0"  >
  <tr>
    <td height="45" align="center" bgcolor="#E20A17">


        <table width="770" height="45" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td  style="padding-left: 20px" >


<?$APPLICATION->IncludeComponent("bitrix:menu", "main_top_menu", Array(
        "ROOT_MENU_TYPE"        =>        "top",
        "MAX_LEVEL"        =>        "2",
        "CHILD_MENU_TYPE"        =>        "part",
        "USE_EXT"        =>        "N"
        )
);?>


</td>
        </tr>
      </table>


          </td>
  </tr>
  <tr>
    <td height="75" align="center">


        <table width="770" height="75" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td background="/bitrix/templates/avangard/images/bg_line1.gif" style="padding-left: 25px"><a href="/"><img src="/bitrix/templates/avangard/images/logo1.gif" width="240" height="41"></a></td>
          <td>
<div align="center" style="position:relative; top:285px; width:44px; height:44px; left: -158px">
<a href="/news/news_478.html"><img src="/bitrix/templates/avangard/images/eko.gif" width="44" height="44"></a></div>
          </td>
          <td></td>
        </tr>
      </table>


          </td>
  </tr>
  <tr>
    <td height="270" align="center" bgcolor="#e1e1e1">

        <table width="770" height="275" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td valign="top" background="/bitrix/templates/avangard/images/bg_line1.gif">

                  <table width="100%" style="margin-bottom: -3px;" height="275" border="0" cellpadding="0" cellspacing="0">
              <tr style="background: url(/bitrix/templates/avangard/images/bg_h_line1.gif) repeat-x  left bottom">
                <td  width="555" align="right">
                  <table width="530"  height="275" border="0" cellspacing="1" cellpadding="0">
                    <tr class="catmenu" valign="center">
                      <td width="33%" height="20" bgcolor="e1e1e1"><a href="/catalogue/"><p id="head1">каталог товаров</p></a></td>
                      <td width="33%"  height="20"  bgcolor="e1e1e1"><a href="/wharetobuy/">где купить диван</a></td>
                      <td width="33%"  height="20" bgcolor="e1e1e1"><a href="/information/"><p id="head2">полезная информация</p></a></td>                      
                    </tr>
                    <tr align="center" valign="bottom">
                      <td colspan="3">


<?$APPLICATION->IncludeComponent(
        "bitrix:main.include",
        "",
        Array(
                "AREA_FILE_SHOW" => "sect",
                "AREA_FILE_SUFFIX" => "mainimg",
                "AREA_FILE_RECURSIVE" => "Y",
                "EDIT_MODE" => "html",
                "EDIT_TEMPLATE" => ""
        )
);?>


</td>
                    </tr>
                  </table></td>
                <td align="center">

                <table width="100%" height="275" border="0" cellpadding="0" cellspacing="0">
                    <tr>

                      <td align="center" valign="top"  style="background:url('/flash/188x134.gif') top center no-repeat;">
                              <a href="/8days/"><div id="flash_container_id">
<?/*<a href="/8days/"><img src="/flash/188x134.gif" width="188" height="134" border="0"></a>*/?>
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="188" height="134" id="188x134" align="middle">
        <param name="allowScriptAccess" value="sameDomain" />
        <param name="allowFullScreen" value="false" />
        <param name="movie" value="/flash/188x134.swf?link1=/8days/" />
<param name="quality" value="high">
<param name="bgcolor" value="#FFFFFF">
<param name="wmode" value="opaque">
        <embed src="/flash/188x134.swf?link1=/8days/" wmode="opaque" quality="high" bgcolor="#ffffff" width="188" height="134" name="188x134" align="middle" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
        </object>
                                                </div></a>
                                        </td>
                    </tr>
                    <tr>
                      <td align="center" valign="bottom">

<?$APPLICATION->IncludeComponent(
        "bitrix:main.include",
        "",
        Array(
                "AREA_FILE_SHOW" => "sect",
                "AREA_FILE_SUFFIX" => "newhit",
                "AREA_FILE_RECURSIVE" => "Y",
                "EDIT_MODE" => "html",
                "EDIT_TEMPLATE" => ""
        )
);?>


           </td>
                    </tr>
                  </table>



</td>
              </tr>
            </table>

                        </td>
        </tr>
      </table>

          </td>
  </tr>
  <tr>
    <td height="40" align="center"><table width="770" height="40" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td valign="bottom" background="/bitrix/templates/avangard/images/bg_line1.gif"  style="padding-left: 25px" >

                  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="news">
              <tr>
                <td width="210">новости</td>
                 <td align="left" style="margin:0px; padding:0px;">


<?$APPLICATION->IncludeComponent(
        "bitrix:main.include",
        "",
        Array(
                "AREA_FILE_SHOW" => "sect",
                "AREA_FILE_SUFFIX" => "title",
                "AREA_FILE_RECURSIVE" => "Y",
                "EDIT_MODE" => "html",
                "EDIT_TEMPLATE" => ""
        )
);?>





</td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
  <tr  >
   <td align="center" valign="top" background="/bitrix/templates/avangard/images/back2.gif">


   <table width="100%" height="40"  border="0" cellpadding="0" cellspacing="0" >
        <tr style="background: url(/bitrix/templates/avangard/images/bg_h_line2.gif) repeat-x  left top">
          <td align="center" valign="top">


            <table width="770" height="300" border="0" cellpadding="0" cellspacing="0">
        <tr>
                <td valign="top" background="/bitrix/templates/avangard/images/bg_line2.gif" style="padding-left: 25px">

                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="175" valign="top">



<?$APPLICATION->IncludeComponent("bitrix:news.list", "news_main_list", Array(
	"IBLOCK_TYPE"	=>	"news",
	"IBLOCK_ID"	=>	"13",
	"NEWS_COUNT"	=>	"5",
	"SORT_BY1"	=>	"ACTIVE_FROM",
	"SORT_ORDER1"	=>	"DESC",
	"SORT_BY2"	=>	"SORT",
	"SORT_ORDER2"	=>	"ASC",
	"FILTER_NAME"	=>	"",
	"FIELD_CODE"	=>	array(
		0	=>	"",
		1	=>	"",
		2	=>	"",
	),
	"PROPERTY_CODE"	=>	array(
		0	=>	"",
		1	=>	"",
	),
	"DETAIL_URL"	=>	"/news/news_#ELEMENT_ID#.html",
	"AJAX_MODE"	=>	"N",
	"AJAX_OPTION_SHADOW"	=>	"Y",
	"AJAX_OPTION_JUMP"	=>	"N",
	"AJAX_OPTION_STYLE"	=>	"Y",
	"AJAX_OPTION_HISTORY"	=>	"N",
	"CACHE_TYPE"	=>	"A",
	"CACHE_TIME"	=>	"3600",
	"CACHE_FILTER"	=>	"N",
	"PREVIEW_TRUNCATE_LEN"	=>	"",
	"ACTIVE_DATE_FORMAT"	=>	"d.m.Y",
	"DISPLAY_PANEL"	=>	"N",
	"SET_TITLE"	=>	"N",
	"INCLUDE_IBLOCK_INTO_CHAIN"	=>	"N",
	"ADD_SECTIONS_CHAIN"	=>	"N",
	"HIDE_LINK_WHEN_NO_DETAIL"	=>	"N",
	"PARENT_SECTION"	=>	"",
	"DISPLAY_TOP_PAGER"	=>	"N",
	"DISPLAY_BOTTOM_PAGER"	=>	"N",
	"PAGER_TITLE"	=>	"Новости",
	"PAGER_SHOW_ALWAYS"	=>	"N",
	"PAGER_TEMPLATE"	=>	"",
	"PAGER_DESC_NUMBERING"	=>	"N",
	"PAGER_DESC_NUMBERING_CACHE_TIME"	=>	"36000",
	"DISPLAY_DATE"	=>	"N",
	"DISPLAY_NAME"	=>	"Y",
	"DISPLAY_PICTURE"	=>	"N",
	"DISPLAY_PREVIEW_TEXT"	=>	"Y"
	)
);?>





                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td class="newsblock" height="25"> <a href="/news/"><img src="/bitrix/templates/avangard/images/morenews.gif" width="178" height="23" border="0"></a> </td>
                          </tr>
                       </table><br />




                        </td>
                      <td width="30" >&nbsp;</td>
                      <td valign="top">

                                          <table width="100%" height="12" class="marktab2">
                          <tr>
                            <td>&nbsp;</td>
                          </tr>
                        </table>


<table cellpadding="0" cellspacing="0"><tr><td height="300" valign="top" class="content">
