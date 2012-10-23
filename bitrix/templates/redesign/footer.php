<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)die();
IncludeTemplateLangFile(__FILE__);
?>
				</p>
			</div>
			<!--/CENTER COLUMN-->
			<div class="clearall"></div>
			<?
			$APPLICATION->IncludeComponent("bitrix:menu", "second_bottom_menu", Array(
				"ROOT_MENU_TYPE" => "bottom",
				"MAX_LEVEL" => "2",
				"CHILD_MENU_TYPE" => "part",
				"USE_EXT" => "N"
					)
			);
			?>
			<div id="footer">
				<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
						<td id="f_1">&copy; 2009-2011 Мебельная фабрика Авангард&nbsp;<br><a target="_blank" href="http://www.cava.su/">Разработка сайта - студия «CA VA»</a><br><a target="_blank" href="http://www.media-bridge.ru/">Поддержка сайта - студия «Media-Bridge»</a></td>
						<td id="f_2">Копирование, публикация и использование материалов сайта ЗАПРЕЩЕНЫ!
						</td>
					</tr></table>
			</div>
		</div>


		<!--****************************Счётчики************************************-->
		<!-- Yandex.Metrika counter -->
		<div style="display:none;"><script type="text/javascript">
			(function(w, c) {
				(w[c] = w[c] || []).push(function() {
					try {
						w.yaCounter11801303 = new Ya.Metrika({id:11801303, enableAll: true, ut:"noindex", webvisor:true});
					}
					catch(e) { }
				});
			})(window, "yandex_metrika_callbacks");
			</script></div>
		<script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript" defer="defer"></script>
		<noscript><div><img src="//mc.yandex.ru/watch/11801303?ut=noindex" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
		<!-- /Yandex.Metrika counter -->
		<script type="text/javascript">
			var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www."); document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
			try{
				var pageTracker = _gat._getTracker("UA-9600751-1"); pageTracker._trackPageview(); 
			}catch(err){}
		</script>

		<!-- Yandex.Metrika Marked Phone -->
		<script type="text/javascript" src="//mc.yandex.ru/metrika/phone.js?counter=11801303" defer="defer"></script>
		<!-- /Yandex.Metrika Marked Phone -->
		<!--****************************Счётчики************************************-->
	</body>
</html>