<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
</td>

</tr></table></td>
<?if($APPLICATION->GetProperty('right_inc_file')!='none') :?>
 <?$left_incl = $left_incl+1; ?>
 <td class="right_inc_file_td">
<?$APPLICATION->IncludeFile($APPLICATION->GetProperty('right_inc_file'), array(), array(
                        "MODE"      => "html",
                        "TEMPLATE"  => "page_inc.php"
                        ));
						?>

</td>
<?endif?>
</tr>
<tr><td colspan="<?=$left_incl?>">
<div  class="bottom_menu_div">
<?$APPLICATION->IncludeComponent("bitrix:menu", "second_bottom_menu", Array(
	"ROOT_MENU_TYPE"	=>	"top_two",
	"MAX_LEVEL"	=>	"2",
	"CHILD_MENU_TYPE"	=>	"part",
	"USE_EXT"	=>	"N"
	)
);?>
 </div>
 <div class="footer_copyr_td"><table width="700"><tr><td width="450" nowrap  valign="middle" align="right"><right>© 2009 Мебельная фабрика Авангард <br><a title="создание сайтов, разработка сайтов на 1С-Битрикс, поддержка сайтов" href="http://www.cava.su">Разработка и поддержка сайта - студия
«CA VA»</a></right></td><td nowrap align="right" valign="middle" style="padding-left:15px; padding-right: 30px;">
</td></tr></table></div>

</td></tr></table>
</div>
</center>
</div>

<table id="main_table" border="0" cellpadding="0" cellspacing="0">
  
  
<tr bgcolor="#E20A17"><td colspan="2" height="46">
<td align="center">
<table width="1000"><tr><td width="400"></td>
<td align="right">
<?$APPLICATION->IncludeComponent("bitrix:menu", "main_top_menu", Array(
        "ROOT_MENU_TYPE"        =>        "top_one",
        "MAX_LEVEL"        =>        "2",
        "CHILD_MENU_TYPE"        =>        "part",
        "USE_EXT"        =>        "N"
        )
);?>
</td>
<td width="100" align="right" valign="middle">
<?$APPLICATION->IncludeComponent("bitrix:search.form", "top_search", Array(
	"PAGE"	=>	"/redesign/catalog/search.php"
	)
);?>
</td><td width="16"></td>
</tr>
</table>
</td>
<td colspan="2">
</td></tr>

  
  
  
<tr id="logotype_tr"><td colspan="2" height="70">
<td align="center">
<table align="center"><tr><td align="left"><a href="/"><img border="0" src="/images/logotype.gif"></a></td>
<td></td>
<td  align="right"><font class="phonecode">(495)</font> <font class="phonenum"><span id="ya-phone-1">981-66-44</span></font><br><font class="phonetext">многоканальный телефон</font></td>
</tr>
</table>
</td>
<td colspan="2">
</td></tr>
 
 <tr id="top_menu_tr">
<td  class="lines" colspan="5" align="center">


<div  style="background: #ffffff; width: 1000px; height: 400px;">
<?$APPLICATION->IncludeComponent("bitrix:menu", "second_top_menu", Array(
	"ROOT_MENU_TYPE"	=>	"top_two",
	"MAX_LEVEL"	=>	"2",
	"CHILD_MENU_TYPE"	=>	"part",
	"USE_EXT"	=>	"N"
	)
);?>
<br><br><br><br>
 </div>

</td>
</tr>


 <tr id="slider_tr">
<td class="lines" colspan="5" align="center"  style="padding-top:-3px; margin:0px;">





</td>
</tr>


</table>
<!--****************************Счётчики************************************-->
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter11801303 = new Ya.Metrika({
                    id:11801303,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/11801303" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<!-- Yandex.Metrika Marked Phone -->
<script type="text/javascript" src="//mc.yandex.ru/metrika/phone.js?counter=11801303" defer="defer"></script>
<!-- /Yandex.Metrika Marked Phone -->
<!--****************************Счётчики************************************-->

</body>
</html>