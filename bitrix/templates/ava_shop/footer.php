<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>

		</div>
		<!--/RIGHT COLUMN-->

		<div class="clearall"></div>
		<div id="footer_menu">
			<?$APPLICATION->IncludeComponent("bitrix:menu", "second_bottom_menu", Array(
				"ROOT_MENU_TYPE"=> "top_two",
				"MAX_LEVEL"=> "2",
				"CHILD_MENU_TYPE"=> "part",
				"USE_EXT"=> "N"
				)
			);?>
		</div>
		<div id="footer">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td id="f_1">&copy; 2009-2015 ћебельна€ фабрика јвангард</td>
					<td id="f_2">
						
					</td>
				</tr>
			</table>
		</div>

			</div>
		
		<!--****************************—чЄтчики************************************-->
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
		<!--****************************—чЄтчики************************************-->

	</body>
</html>