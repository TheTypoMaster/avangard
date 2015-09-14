<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
</div>
<!--/LEFT COLUMN-->

<!--RIGHT COLUMN-->
<div id="rc">
<?if($APPLICATION->GetProperty('right_inc_file')!='none') :?>
<?$left_incl = $left_incl+1; ?>
<?$APPLICATION->IncludeFile($APPLICATION->GetProperty('right_inc_file'), array(), array(
                        "MODE"      => "html",
                        "TEMPLATE"  => "page_inc.php"
                        ));
						?>
<?endif?>

</div>   
<!--/RIGHT COLUMN-->
<div class="clearall"></div>
<?$APPLICATION->IncludeComponent("bitrix:menu", "second_bottom_menu", Array(
	"ROOT_MENU_TYPE" => "bottom",
	"MAX_LEVEL" => "2",
	"CHILD_MENU_TYPE" => "part",
	"USE_EXT" => "N"
		)
);?>
<div id="footer">
<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
<td id="f_1">&copy; 2009-2012 Мебельная фабрика Авангард<br></td>
<td id="f_2">Копирование, публикация и использование материалов сайта ЗАПРЕЩЕНЫ!

</td>
</tr></table>
</div>

    </div>
    


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