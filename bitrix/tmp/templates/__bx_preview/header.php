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

		<!--<script src="/libs/jquery.js" type="text/javascript"></script>-->
		<script type="text/javascript" src="/libs/shift.js"></script>
		<script type="text/javascript" src="/flash/swfobject.js"></script>
		<script src="/yescreditservice/yescredit/js/jquery-1.7.1.min.js" type="text/javascript"></script>
		<script src="/yescreditservice/yescredit/js/jquery-ui-1.8.18.custom.min.js" type="text/javascript"></script>
		<script src="/yescreditservice/yescredit/js/yescredit.js" type="text/javascript"></script>
		<script type="text/javascript" src="http://cdn.dev.skype.com/uri/skype-uri.js"></script>
		<link href="/yescreditservice/yescredit/css/cupertino/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css" />

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-9600751-1', 'avangard.biz');
  ga('send', 'pageview');

</script>
	</head>
	<body>
		<? $APPLICATION->ShowPanel(); ?>
		<? $APPLICATION->AddHeadScript("/shop/basket/basket.js?" . rand(99, 9999)); ?>
		<? if (CModule::IncludeModule('iblock')) $incl = "Y"; ?>
		<!--TOP MENU-->
		<div id="top_menu"><div id="t_m_content">
				<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
						<td class="top_td_left"></td>
						<td>
							<?
							$APPLICATION->IncludeComponent("bitrix:search.form", "top_search", Array(
								"PAGE" => "/redesign/catalog/search.php"
									)
							);
							?>
						</td>
					</tr></table>
			</div></div>
		<!--/TOP MENU-->

		<div id="top_line_4"><div id="t_l_content">
				<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
						<td id="t_l_left">
							<div id="t_l_logo_4"><a href="/"><img src="/images/logo.gif" alt="" border=0></a></div>
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<tr>
									<td><div><p class="p_fone"><span>(495)</span> 357-13-00 доб.133</p><p class="p_fone"><span>(916)</span> 406-57-05</p></div></td>
									<td><p>Прием заявок<br>понедельник - пятница<br>с 9.00 до 18.00</p></td>
								</tr>
							</table>
						</td>
						<td id="t_l_center0">
							<table cellpadding="0" cellspacing="0" border="0" width="459">
								<tr>
									<td><div align="left"><a href="/redesign/where_buy/detail.php?id=329"><img src="/upload/medialibrary/444/showroom_im_245x135.png" border="0" width="245" alt="" ></a></div></td>
									<td id="t_l_center2">
										<div style="display:none;" id="mobmen">- Консультация по модельному ряду (габариты, механизмы и др.)<br>- Предложения по обивочным материалам (ткани, кожа)<br>- Оформление договора (спецификации)</div>
									</td>
								</tr>
							</table>
						</td>
						<td>
							<div id="t_l_right">
								<script type="text/javascript" src="/shop/basket/min.js"></script> 
								<script type="text/javascript">
									$(document).ready(function(){
										$("a.bskype").hover(function () { $("#skype").slideToggle(100); }, function () { $("#skype").slideToggle(100); });
										//$("#t_l_center2").hover(function () { $("#mobmen").slideToggle(100); }, function () { $("#mobmen").slideToggle(100); });										
										//$("#bzvonok").hover(function () { $("#zvonok").slideToggle(100); }, function () { $("#zvonok").slideToggle(100); });
										$("#bzvonok").hover(function () { 
											if (!$("#zvonok").hasClass("needclose")) {
												if ($("#zvonok").hasClass("odd")) {
													$("#zvonok").removeClass("odd").hide();
												} else {
													$("#zvonok").removeClass("odd").hide();
													$("#zvonok").addClass("odd").show();
												}
											}
											//alert($(this).find(".bigwindow").html());
										}, function () { 
											if (!$("#zvonok").hasClass("needclose")) {
												if ($("#zvonok").hasClass("odd")) {
													$("#zvonok").removeClass("odd").hide();
												} else {
													$("#zvonok").removeClass("odd").hide();
													$("#zvonok").addClass("odd").show();
												}
											}
										});
	
										$("#buttonzvonok").click(function () 
										{ 
											var val =$("#phonezvonok").val();
											if (val.length > 0) {
												$("#zvonok").html("<br><br><br>Please Wait...<br><br><br><br>");
												$.post("/shop/basket/zakazzvonok.php", { phone: encodeURIComponent(val) }, function(data){ 
													$("#zvonok").addClass("needclose").html(data);
													$("#clww").click(function () {
														$("#zvonok").removeClass("needclose").hide();
													});	
												});
											}
										});
	
										//$("#phonezvonok").mask("+7 999 9999999");
									});
									(function(a){var b=(a.browser.msie?"paste":"input")+".mask",c=window.orientation!=undefined;a.mask={definitions:{9:"[0-9]",a:"[A-Za-z]","*":"[A-Za-z0-9]"},dataName:"rawMaskFn"},a.fn.extend({caret:function(a,b){if(this.length!=0){if(typeof a=="number"){b=typeof b=="number"?b:a;return this.each(function(){if(this.setSelectionRange)this.setSelectionRange(a,b);else if(this.createTextRange){var c=this.createTextRange();c.collapse(!0),c.moveEnd("character",b),c.moveStart("character",a),c.select()}})}if(this[0].setSelectionRange)a=this[0].selectionStart,b=this[0].selectionEnd;else if(document.selection&&document.selection.createRange){var c=document.selection.createRange();a=0-c.duplicate().moveStart("character",-1e5),b=a+c.text.length}return{begin:a,end:b}}},unmask:function(){return this.trigger("unmask")},mask:function(d,e){if(!d&&this.length>0){var f=a(this[0]);return f.data(a.mask.dataName)()}e=a.extend({placeholder:"_",completed:null},e);var g=a.mask.definitions,h=[],i=d.length,j=null,k=d.length;a.each(d.split(""),function(a,b){b=="?"?(k--,i=a):g[b]?(h.push(new RegExp(g[b])),j==null&&(j=h.length-1)):h.push(null)});return this.trigger("unmask").each(function(){function v(a){var b=f.val(),c=-1;for(var d=0,g=0;d<k;d++)if(h[d]){l[d]=e.placeholder;while(g++<b.length){var m=b.charAt(g-1);if(h[d].test(m)){l[d]=m,c=d;break}}if(g>b.length)break}else l[d]==b.charAt(g)&&d!=i&&(g++,c=d);if(!a&&c+1<i)f.val(""),t(0,k);else if(a||c+1>=i)u(),a||f.val(f.val().substring(0,c+1));return i?d:j}function u(){return f.val(l.join("")).val()}function t(a,b){for(var c=a;c<b&&c<k;c++)h[c]&&(l[c]=e.placeholder)}function s(a){var b=a.which,c=f.caret();if(a.ctrlKey||a.altKey||a.metaKey||b<32)return!0;if(b){c.end-c.begin!=0&&(t(c.begin,c.end),p(c.begin,c.end-1));var d=n(c.begin-1);if(d<k){var g=String.fromCharCode(b);if(h[d].test(g)){q(d),l[d]=g,u();var i=n(d);f.caret(i),e.completed&&i>=k&&e.completed.call(f)}}return!1}}function r(a){var b=a.which;if(b==8||b==46||c&&b==127){var d=f.caret(),e=d.begin,g=d.end;g-e==0&&(e=b!=46?o(e):g=n(e-1),g=b==46?n(g):g),t(e,g),p(e,g-1);return!1}if(b==27){f.val(m),f.caret(0,v());return!1}}function q(a){for(var b=a,c=e.placeholder;b<k;b++)if(h[b]){var d=n(b),f=l[b];l[b]=c;if(d<k&&h[d].test(f))c=f;else break}}function p(a,b){if(!(a<0)){for(var c=a,d=n(b);c<k;c++)if(h[c]){if(d<k&&h[c].test(l[d]))l[c]=l[d],l[d]=e.placeholder;else break;d=n(d)}u(),f.caret(Math.max(j,a))}}function o(a){while(--a>=0&&!h[a]);return a}function n(a){while(++a<=k&&!h[a]);return a}var f=a(this),l=a.map(d.split(""),function(a,b){if(a!="?")return g[a]?e.placeholder:a}),m=f.val();f.data(a.mask.dataName,function(){return a.map(l,function(a,b){return h[b]&&a!=e.placeholder?a:null}).join("")}),f.attr("readonly")||f.one("unmask",function(){f.unbind(".mask").removeData(a.mask.dataName)}).bind("focus.mask",function(){m=f.val();var b=v();u();var c=function(){b==d.length?f.caret(0,b):f.caret(b)};(a.browser.msie?c:function(){setTimeout(c,0)})()}).bind("blur.mask",function(){v(),f.val()!=m&&f.change()}).bind("keydown.mask",r).bind("keypress.mask",s).bind(b,function(){setTimeout(function(){f.caret(v(!0))},0)}),v()})}})})(jQuery)
									<? /* (function(a){var c=(a.browser.msie?"paste":"input")+".mask";var b=(window.orientation!=undefined);a.mask={definitions:{"9":"[0-9]",a:"[A-Za-z]","*":"[A-Za-z0-9]"}};a.fn.extend({caret:function(e,f){if(this.length==0){return}if(typeof e=="number"){f=(typeof f=="number")?f:e;return this.each(function(){if(this.setSelectionRange){this.focus();this.setSelectionRange(e,f)}else{if(this.createTextRange){var g=this.createTextRange();g.collapse(true);g.moveEnd("character",f);g.moveStart("character",e);g.select()}}})}else{if(this[0].setSelectionRange){e=this[0].selectionStart;f=this[0].selectionEnd}else{if(document.selection&&document.selection.createRange){var d=document.selection.createRange();e=0-d.duplicate().moveStart("character",-100000);f=e+d.text.length}}return{begin:e,end:f}}},unmask:function(){return this.trigger("unmask")},mask:function(j,d){if(!j&&this.length>0){var f=a(this[0]);var g=f.data("tests");return a.map(f.data("buffer"),function(l,m){return g[m]?l:null}).join("")}d=a.extend({placeholder:"_",completed:null},d);var k=a.mask.definitions;var g=[];var e=j.length;var i=null;var h=j.length;a.each(j.split(""),function(m,l){if(l=="?"){h--;e=m}else{if(k[l]){g.push(new RegExp(k[l]));if(i==null){i=g.length-1}}else{g.push(null)}}});return this.each(function(){var r=a(this);var m=a.map(j.split(""),function(x,y){if(x!="?"){return k[x]?d.placeholder:x}});var n=false;var q=r.val();r.data("buffer",m).data("tests",g);function v(x){while(++x<=h&&!g[x]){}return x}function t(x){while(!g[x]&&--x>=0){}for(var y=x;y<h;y++){if(g[y]){m[y]=d.placeholder;var z=v(y);if(z<h&&g[y].test(m[z])){m[y]=m[z]}else{break}}}s();r.caret(Math.max(i,x))}function u(y){for(var A=y,z=d.placeholder;A<h;A++){if(g[A]){var B=v(A);var x=m[A];m[A]=z;if(B<h&&g[B].test(x)){z=x}else{break}}}}function l(y){var x=a(this).caret();var z=y.keyCode;n=(z<16||(z>16&&z<32)||(z>32&&z<41));if((x.begin-x.end)!=0&&(!n||z==8||z==46)){w(x.begin,x.end)}if(z==8||z==46||(b&&z==127)){t(x.begin+(z==46?0:-1));return false}else{if(z==27){r.val(q);r.caret(0,p());return false}}}function o(B){if(n){n=false;return(B.keyCode==8)?false:null}B=B||window.event;var C=B.charCode||B.keyCode||B.which;var z=a(this).caret();if(B.ctrlKey||B.altKey||B.metaKey){return true}else{if((C>=32&&C<=125)||C>186){var x=v(z.begin-1);if(x<h){var A=String.fromCharCode(C);if(g[x].test(A)){u(x);m[x]=A;s();var y=v(x);a(this).caret(y);if(d.completed&&y==h){d.completed.call(r)}}}}}return false}function w(x,y){for(var z=x;z<y&&z<h;z++){if(g[z]){m[z]=d.placeholder}}}function s(){return r.val(m.join("")).val()}function p(y){var z=r.val();var C=-1;for(var B=0,x=0;B<h;B++){if(g[B]){m[B]=d.placeholder;while(x++<z.length){var A=z.charAt(x-1);if(g[B].test(A)){m[B]=A;C=B;break}}if(x>z.length){break}}else{if(m[B]==z[x]&&B!=e){x++;C=B}}}if(!y&&C+1<e){r.val("");w(0,h)}else{if(y||C+1>=e){s();if(!y){r.val(r.val().substring(0,C+1))}}}return(e?B:i)}if(!r.attr("readonly")){r.one("unmask",function(){r.unbind(".mask").removeData("buffer").removeData("tests")}).bind("focus.mask",function(){q=r.val();var x=p();s();setTimeout(function(){if(x==j.length){r.caret(0,x)}else{r.caret(x)}},0)}).bind("blur.mask",function(){p();if(r.val()!=q){r.change()}}).bind("keydown.mask",l).bind("keypress.mask",o).bind(c,function(){setTimeout(function(){r.caret(p(true))},0)})}p()})}})})(jQuery); */ ?>
								</script>
								<table cellspacing="4" border="0" width="100%">
									<tr>
										<td>
											<div id="bzvonok"><img src="/images/telefon.jpg" alt="" border="0">
												<div id="zvonok" class="" style="display: none;">Введите свой номер телефона c кодом города по шаблону для заказа звонка<br>
													<input type="text" style="width:150px;" class="RegisterInput" id="phonezvonok" name="phonezvonok"><input type="button" id="buttonzvonok" value="Заказать">
												</div></div>
										</td>
										<td id="zvonok_link"><a class="zvonok" href="#">Заказать звонок</a></td>
										<td><div align="right"><a href="/shop/basket/"><img src="/images/basket2.jpg" alt="" border="0"></a></div></td>
									</tr>
									<tr>
										<td>
											<div style="margin: 0px; padding: 0px; position: absolute; z-index: 100;">
											<div style="position: relative; left: -23px; top: -45px; height: 50px; width: 50px;">
											<div id="SkypeButton_Call_av-internet_1">
											  <script type="text/javascript">
											    Skype.ui({
											      "name": "call",
											      "element": "SkypeButton_Call_av-internet_1",
											      "participants": ["av-internet"],
											      "imageSize": 32
											    });
											  </script>
											</div>
											</div>
											</div>
										</td>
										<td colspan="2">
											<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
													<td><div style="text-align: right;"><p class="p_fone" ><span>(916)</span> 406-51-88</p><p>9.00 - 20.00, без выходных</p></div></td>
												</tr></table>
										</td>
									</tr>
									<tr>
										<td><img src="/images/mail.jpg" alt="" border="0"></td>
										<td colspan="2" id="mail_link"><a href="mailto:av-shop@avangard.biz">av-shop@avangard.biz</a></td>
									</tr></table>

							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div style="background: #ffffff; width: 1000px; height: 50px; margin:0 auto;">
			<?
			$APPLICATION->IncludeComponent("bitrix:menu", "second_top_menu", array(
					"ROOT_MENU_TYPE" => "top_two",
					"MAX_LEVEL" => "2",
					"CHILD_MENU_TYPE" => "part",
					"USE_EXT" => "N"
				), false, array(
					"ACTIVE_COMPONENT" => "Y"
				)
			);
			?>
		</div>

		<div id="all_4">
			<!--LEFT COLUMN-->
			<div id="lc">
				<div id="lc_menu_4">
					<?
					$APPLICATION->IncludeComponent("bitrix:menu", "shop_left_menu", array(
						"ROOT_MENU_TYPE" => "part",
						"MENU_CACHE_TYPE" => "Y",
						"MENU_CACHE_TIME" => "36000000",
						"MENU_CACHE_USE_GROUPS" => "Y",
						"MENU_CACHE_GET_VARS" => array(
						),
						"MAX_LEVEL" => "1",
						"CHILD_MENU_TYPE" => "left",
						"USE_EXT" => "N",
						"DELAY" => "N",
						"ALLOW_MULTI_SELECT" => "N"
							), false
					);
					?>
				</div>
				<div id="lc_img_link">
					<?
					$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
						"AREA_FILE_SHOW" => "sect",
						"AREA_FILE_SUFFIX" => "/shop.php",
						"AREA_FILE_RECURSIVE" => "Y",
						"EDIT_TEMPLATE" => ""
							), false
					);
					?>
				</div>
			</div>

			<!--/LEFT COLUMN-->

			<!--RIGHT COLUMN-->

			<div id="rc_4">
				<?
				$APPLICATION->IncludeComponent("bitrix:subscribe.form", "new", array(
	"USE_PERSONALIZATION" => "Y",
	"SHOW_HIDDEN" => "Y",
	"PAGE" => "#SITE_DIR#shop/subscr_edit.php",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600"
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "N"
	)
);
				?>
				