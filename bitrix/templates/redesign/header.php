<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<link rel="shortcut icon" href="http://www.avangard.biz/favicon.ico" type="image/x-icon">
		<meta name='yandex-verification' content='462691a93b33875f' />
		<? $APPLICATION->ShowMeta("robots") ?>
		<? $APPLICATION->ShowMeta("keywords") ?>
		<? $APPLICATION->ShowMeta("description") ?>
		<title><? $APPLICATION->ShowTitle() ?></title>
		<? $APPLICATION->ShowCSS(); ?>
		<? $APPLICATION->ShowHeadStrings() ?>
		<? $APPLICATION->ShowHeadScripts() ?>

		<link href="/libs/scroll.css" type="text/css" rel="stylesheet" />
		<script src="/libs/js/dw_event.js" type="text/javascript"></script>
		<script src="/libs/js/dw_scroll.js" type="text/javascript"></script>
		<script src="/libs/js/dw_scrollbar.js" type="text/javascript"></script>
		<script src="/libs/js/scroll_controls.js" type="text/javascript"></script>
		<script src="/libs/js/html_att_ev.js" type="text/javascript"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
		<script type="text/javascript" src="<?= SITE_TEMPLATE_PATH ?>/jquery-ui/jquery-ui-1.8.23.custom.min.js"></script>
		<link type="text/css" href="<?= SITE_TEMPLATE_PATH ?>/jquery-ui/css/ui-avangard/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
		<script src="<?= SITE_TEMPLATE_PATH ?>/out.js" type="text/javascript"></script>
		<script src="<?=SITE_TEMPLATE_PATH?>/script.js" type="text/javascript"></script>

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
			<? if ($_GET[id]) { ?> 
				var wndo = dw_scrollObj.col['wn'];
				var el = document.getElementById('divan<? echo $_GET[id]; ?>');
				if (el) {
					var lyr = document.getElementById('lyr1');
					var x = dw_getLayerOffset(el, lyr, 'left');
					var y = dw_getLayerOffset(el, lyr, 'top');
					wndo.initScrollToVals(x, y, 500);
				}
			<? } ?>
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

<!-- RedHelper -->
<script id="rhlpscrtg" type="text/javascript" charset="utf-8" async="async" src="https://web.redhelper.ru/service/main.js?c=avshop2015"></script>
<!-- /RedHelper -->
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
		<? $APPLICATION->ShowPanel(); ?>
		<? $APPLICATION->AddHeadScript("/basket/basket.js"); ?>
		<? if (CModule::IncludeModule('iblock')) $incl = "Y"; ?>
		<!--TOP MENU-->
		<div id="top_menu">
			<div id="t_m_content">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
						<td class="top_td_left">
							<?$APPLICATION->IncludeComponent("bitrix:menu", "main_top_menu", array(
									"ROOT_MENU_TYPE" => "top_one",
									"MENU_CACHE_TYPE" => "N",
									"MENU_CACHE_TIME" => "3600",
									"MENU_CACHE_USE_GROUPS" => "Y",
									"MENU_CACHE_GET_VARS" => array(),
									"MAX_LEVEL" => "2",
									"CHILD_MENU_TYPE" => "part",
									"USE_EXT" => "N",
									"DELAY" => "N",
									"ALLOW_MULTI_SELECT" => "N"
								), false, array(
									"ACTIVE_COMPONENT" => "Y"
								)
							);?>
						</td>
						<td>
							<?$APPLICATION->IncludeComponent("bitrix:search.form", "top_search", Array(
									"PAGE" => "/redesign/catalog/search.php"
								)
							);?>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<!--/TOP MENU-->
		<div id="top_line">
			<div id="t_l_content">
				<div id="t_l_logo"><a href="/"><img src="/images/logo.gif" alt="" border=0></a></div>
				<div id="t_l_fone"><p class="p_fone"><span>(495)</span> <span id="ya-phone-1">357-13-00</span></p><p>многоканальный телефон</p></div>
				<div class="pluso-wrap">
					<span><a href="https://www.facebook.com/avangard.biz" title="Facebook" class="pluso-facebook"></a> <a href="http://vk.com/club57166671" title="ВКонтакте" class="pluso-vkontakte"></a> <a href="https://instagram.com/avangard_fm" title="Instagram" class="pluso-instagram"></a></span>
				</div>
			</div>
		</div>
		
		<?$APPLICATION->IncludeComponent("bitrix:menu", "second_top_menu", Array(
				"ROOT_MENU_TYPE" => "top_two",
				"MAX_LEVEL" => "2",
				"CHILD_MENU_TYPE" => "part",
				"USE_EXT" => "N"
			)
		);?>
		
		<div id="all">
			<!--LEFT COLUMN-->
			<div id="lc">
			<?include 'left_inc_file.php';?>
			</div>
			<!--/LEFT COLUMN-->

			<!--CENTER COLUMN-->
			<div id="cc">
