<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?>


<style type="text/css">
/* hide from incapable browsers */
div#scrollbar { 
    display:none;
    }

#seriy_block {
 width:960px; height:267px; 
background: #e1e1e1;

}


/* below in external file */
div#wn	{ 
    position:relative; 
    width:960px; height:219px; 
     overflow:hidden;	
}
div#scrollbar { 
    position:relative; 
    width:920px; height:21px;
    display:block; /* display:none initially */
   border: solid black 1px;
    font-size:1px;  /* so no gap or misplacement due to image vertical alignment */
    }

div#track { 
    position:absolute; left:14px;
    width:886px; height:21px; 
    background: url('/images/slider_bg.gif') no-repeat;
   
  
  }
div#dragBar {
    position:absolute; 
    left:0;
    top:0;
   margin: 1px;
    width:90px; height:15px; 
      background: url('/images/polzunok_bg.gif') repeat-x #e1e1e1;
      border: solid #aeaeae 1px;
   font-size: 10px;
  }  
div#left { position:absolute; left:0; top:0; }  
div#right { position:absolute; right:7px; top:0;  }

/* for safari, to prevent selection problem  */
div#scrollbar, div#track, div#dragBar, div#left, div#right {
    -moz-user-select: none;
    -khtml-user-select: none;
}

/* so no gap or misplacement due to image vertical alignment
font-size:1px in scrollbar has same effect (less likely to be removed, resulting in support issues) */
div#scrollbar img {
    display:block; 
    } 

</style>
<script src="/libs/js/dw_event.js" type="text/javascript"></script>
<script src="/libs/js/dw_scroll.js" type="text/javascript"></script>
<script src="/libs/js/dw_scrollbar.js" type="text/javascript"></script>
<script src="/libs/js/scroll_controls.js" type="text/javascript"></script>
<script type="text/javascript">

function init_dw_Scroll() {
    var wndo = new dw_scrollObj('wn', 'lyr1', 't1');
    wndo.setUpScrollbar("dragBar", "track", "h", 1, 1);
    wndo.setUpScrollControls('scrollbar');
}

// if code supported, link in the style sheet and call the init function onload
if ( dw_scrollObj.isSupported() ) {
    //dw_writeStyleSheet('css/scroll.css');
    dw_Event.add( window, 'load', init_dw_Scroll);
}

</script>
</head>

<body>
<center>
<div id="seriy_block">
<div id="wn">
    <div id="lyr1">
          <table id="t1"  valign="bottom"  style="background: rgb(212, 213, 213) url(/images/fon.gif) repeat-x scroll 50% 2px; -moz-background-clip: -moz-initial; -moz-background-origin: -moz-initial; -moz-background-inline-policy: -moz-initial; vertical-align: bottom; height: 248px;" cellpadding="0" cellspacing="0"><tbody><tr>

                  <td valign="bottom"><table valign="bottom" class="slider_fon_45" cellpadding="0" cellspacing="0" height="100%"><tbody><tr><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"></div><div id="tov_143" class="sub_name"><a href="/redesign/catalog/byid.php?id=143">Анталия</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">
<a href="/redesign/catalog/byid.php?id=143"><img onmouseover="document.getElementById('tov_143').className='sub_name_on';  " onmouseout="document.getElementById('tov_143').className='sub_name';  " class="inslider_image" alt="Анталия" src="/upload/iblock/cd1/1antaliya.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"></div><div id="tov_144" class="sub_name"><a href="/redesign/catalog/byid.php?id=144">Неаполь</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">
<a href="/redesign/catalog/byid.php?id=144"><img onmouseover="document.getElementById('tov_144').className='sub_name_on';  " onmouseout="document.getElementById('tov_144').className='sub_name';  " class="inslider_image" alt="Неаполь" src="/upload/iblock/a49/2neapol.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="forie_otstup"></td></tr></tbody></table></td><td valign="bottom"><table valign="bottom" class="slider_fon_44" cellpadding="0" cellspacing="0" height="100%"><tbody><tr><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"><img id="hit_160" src="/images/hit_image_off.gif" alt="Хит продаж"></div><div id="tov_160" class="sub_name"><a href="/redesign/catalog/byid.php?id=160">Аризона</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">
<a href="/redesign/catalog/byid.php?id=160"><img onmouseover="document.getElementById('tov_160').className='sub_name_on';  document.getElementById('hit_160').src='/images/hit_image.gif';" onmouseout="document.getElementById('tov_160').className='sub_name';  document.getElementById('hit_160').src='/images/hit_image_off.gif';" class="inslider_image" alt="Аризона" src="/upload/iblock/a8e/3arizona.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"><img id="hit_162" src="/images/hit_image_off.gif" alt="Хит продаж"></div><div id="tov_162" class="sub_name"><a href="/redesign/catalog/byid.php?id=162">Кентукки</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">

<a href="/redesign/catalog/byid.php?id=162"><img onmouseover="document.getElementById('tov_162').className='sub_name_on';  document.getElementById('hit_162').src='/images/hit_image.gif';" onmouseout="document.getElementById('tov_162').className='sub_name';  document.getElementById('hit_162').src='/images/hit_image_off.gif';" class="inslider_image" alt="Кентукки" src="/upload/iblock/f09/4kentukki.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"></div><div id="tov_165" class="sub_name"><a href="/redesign/catalog/byid.php?id=165">Иллинойс</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">
<a href="/redesign/catalog/byid.php?id=165"><img onmouseover="document.getElementById('tov_165').className='sub_name_on';  " onmouseout="document.getElementById('tov_165').className='sub_name';  " class="inslider_image" alt="Иллинойс" src="/upload/iblock/55d/5illinois.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"></div><div id="tov_163" class="sub_name"><a href="/redesign/catalog/byid.php?id=163">Юта</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">
<a href="/redesign/catalog/byid.php?id=163"><img onmouseover="document.getElementById('tov_163').className='sub_name_on';  " onmouseout="document.getElementById('tov_163').className='sub_name';  " class="inslider_image" alt="Юта" src="/upload/iblock/83b/6uta.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"></div><div id="tov_166" class="sub_name"><a href="/redesign/catalog/byid.php?id=166">Денвер</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">
<a href="/redesign/catalog/byid.php?id=166"><img onmouseover="document.getElementById('tov_166').className='sub_name_on';  " onmouseout="document.getElementById('tov_166').className='sub_name';  " class="inslider_image" alt="Денвер" src="/upload/iblock/adf/7denver.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"></div><div id="tov_167" class="sub_name"><a href="/redesign/catalog/byid.php?id=167">Техас</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">

<a href="/redesign/catalog/byid.php?id=167"><img onmouseover="document.getElementById('tov_167').className='sub_name_on';  " onmouseout="document.getElementById('tov_167').className='sub_name';  " class="inslider_image" alt="Техас" src="/upload/iblock/962/8tehas.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"></div><div id="tov_164" class="sub_name"><a href="/redesign/catalog/byid.php?id=164">Невада</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">
<a href="/redesign/catalog/byid.php?id=164"><img onmouseover="document.getElementById('tov_164').className='sub_name_on';  " onmouseout="document.getElementById('tov_164').className='sub_name';  " class="inslider_image" alt="Невада" src="/upload/iblock/b17/9nevada.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"></div><div id="tov_477" class="sub_name"><a href="/redesign/catalog/byid.php?id=477">Монтана</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">
<a href="/redesign/catalog/byid.php?id=477"><img onmouseover="document.getElementById('tov_477').className='sub_name_on';  " onmouseout="document.getElementById('tov_477').className='sub_name';  " class="inslider_image" alt="Монтана" src="/upload/iblock/392/10montana.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"><img id="new_471" src="/images/new_image_off.gif" alt="Новинка"></div><div id="tov_471" class="sub_name"><a href="/redesign/catalog/byid.php?id=471">Ричмонд</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">
<a href="/redesign/catalog/byid.php?id=471"><img onmouseover="document.getElementById('tov_471').className='sub_name_on'; document.getElementById('new_471').src='/images/new_image.gif'; " onmouseout="document.getElementById('tov_471').className='sub_name'; document.getElementById('new_471').src='/images/new_image_off.gif'; " class="inslider_image" alt="Ричмонд" src="/upload/iblock/35f/11richmond.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"></div><div id="tov_475" class="sub_name"><a href="/redesign/catalog/byid.php?id=475">Орегон</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">
<a href="/redesign/catalog/byid.php?id=475"><img onmouseover="document.getElementById('tov_475').className='sub_name_on';  " onmouseout="document.getElementById('tov_475').className='sub_name';  " class="inslider_image" alt="Орегон" src="/upload/iblock/dfe/12oregon.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="forie_otstup"></td></tr></tbody></table></td><td valign="bottom"><table valign="bottom" class="slider_fon_42" cellpadding="0" cellspacing="0" height="100%"><tbody><tr><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"></div><div id="tov_190" class="sub_name"><a href="/redesign/catalog/byid.php?id=190">Эвазион</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">
<a href="/redesign/catalog/byid.php?id=190"><img onmouseover="document.getElementById('tov_190').className='sub_name_on';  " onmouseout="document.getElementById('tov_190').className='sub_name';  " class="inslider_image" alt="Эвазион" src="/upload/iblock/3c2/13evazion.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"></div><div id="tov_189" class="sub_name"><a href="/redesign/catalog/byid.php?id=189">Орион 5</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">
<a href="/redesign/catalog/byid.php?id=189"><img onmouseover="document.getElementById('tov_189').className='sub_name_on';  " onmouseout="document.getElementById('tov_189').className='sub_name';  " class="inslider_image" alt="Орион 5" src="/upload/iblock/c1c/14orion5.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"></div><div id="tov_191" class="sub_name"><a href="/redesign/catalog/byid.php?id=191">Флёри</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">
<a href="/redesign/catalog/byid.php?id=191"><img onmouseover="document.getElementById('tov_191').className='sub_name_on';  " onmouseout="document.getElementById('tov_191').className='sub_name';  " class="inslider_image" alt="Флёри" src="/upload/iblock/75d/15fleri.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="forie_otstup"></td></tr></tbody></table></td><td valign="bottom"><table valign="bottom" class="slider_fon_43" cellpadding="0" cellspacing="0" height="100%"><tbody><tr><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"></div><div id="tov_172" class="sub_name"><a href="/redesign/catalog/byid.php?id=172">Милан</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">
<a href="/redesign/catalog/byid.php?id=172"><img onmouseover="document.getElementById('tov_172').className='sub_name_on';  " onmouseout="document.getElementById('tov_172').className='sub_name';  " class="inslider_image" alt="Милан" src="/upload/iblock/593/16milan.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"><img id="hit_177" src="/images/hit_image_off.gif" alt="Хит продаж"></div><div id="tov_177" class="sub_name"><a href="/redesign/catalog/byid.php?id=177">Турин</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">
<a href="/redesign/catalog/byid.php?id=177"><img onmouseover="document.getElementById('tov_177').className='sub_name_on';  document.getElementById('hit_177').src='/images/hit_image.gif';" onmouseout="document.getElementById('tov_177').className='sub_name';  document.getElementById('hit_177').src='/images/hit_image_off.gif';" class="inslider_image" alt="Турин" src="/upload/iblock/809/17turin.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"><img id="new_474" src="/images/new_image_off.gif" alt="Новинка"></div><div id="tov_474" class="sub_name"><a href="/redesign/catalog/byid.php?id=474">Соренто</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">
<a href="/redesign/catalog/byid.php?id=474"><img onmouseover="document.getElementById('tov_474').className='sub_name_on'; document.getElementById('new_474').src='/images/new_image.gif'; " onmouseout="document.getElementById('tov_474').className='sub_name'; document.getElementById('new_474').src='/images/new_image_off.gif'; " class="inslider_image" alt="Соренто" src="/upload/iblock/e7c/18sorento.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"></div><div id="tov_170" class="sub_name"><a href="/redesign/catalog/byid.php?id=170">Лион</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">
<a href="/redesign/catalog/byid.php?id=170"><img onmouseover="document.getElementById('tov_170').className='sub_name_on';  " onmouseout="document.getElementById('tov_170').className='sub_name';  " class="inslider_image" alt="Лион" src="/upload/iblock/b7e/20lion.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"><img id="hit_188" src="/images/hit_image_off.gif" alt="Хит продаж"></div><div id="tov_188" class="sub_name"><a href="/redesign/catalog/byid.php?id=188">Романс</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">
<a href="/redesign/catalog/byid.php?id=188"><img onmouseover="document.getElementById('tov_188').className='sub_name_on';  document.getElementById('hit_188').src='/images/hit_image.gif';" onmouseout="document.getElementById('tov_188').className='sub_name';  document.getElementById('hit_188').src='/images/hit_image_off.gif';" class="inslider_image" alt="Романс" src="/upload/iblock/7dd/19romans.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"><img id="new_472" src="/images/new_image_off.gif" alt="Новинка"></div><div id="tov_472" class="sub_name"><a href="/redesign/catalog/byid.php?id=472">Матис</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">

<a href="/redesign/catalog/byid.php?id=472"><img onmouseover="document.getElementById('tov_472').className='sub_name_on'; document.getElementById('new_472').src='/images/new_image.gif'; " onmouseout="document.getElementById('tov_472').className='sub_name'; document.getElementById('new_472').src='/images/new_image_off.gif'; " class="inslider_image" alt="Матис" src="/upload/iblock/df1/21matis.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"></div><div id="tov_197" class="sub_name"><a href="/redesign/catalog/byid.php?id=197">Твиги</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">
<a href="/redesign/catalog/byid.php?id=197"><img onmouseover="document.getElementById('tov_197').className='sub_name_on';  " onmouseout="document.getElementById('tov_197').className='sub_name';  " class="inslider_image" alt="Твиги" src="/upload/iblock/264/22tvigi.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="td_inner" valign="bottom" height="100%"><table height="100%"><tbody><tr><td height="1">

<div class="sub_images"><img id="hit_178" src="/images/hit_image_off.gif" alt="Хит продаж"></div><div id="tov_178" class="sub_name"><a href="/redesign/catalog/byid.php?id=178">Виктория</a></div></td></tr><tr><td height="90%"></td></tr><tr><td class="td_for_slider_img" valign="bottom">
<a href="/redesign/catalog/byid.php?id=178"><img onmouseover="document.getElementById('tov_178').className='sub_name_on';  document.getElementById('hit_178').src='/images/hit_image.gif';" onmouseout="document.getElementById('tov_178').className='sub_name';  document.getElementById('hit_178').src='/images/hit_image_off.gif';" class="inslider_image" alt="Виктория" src="/upload/iblock/512/23viktoriya.gif" {$size[3]}="" border="0"></a></td></tr></tbody></table></td><td class="forie_otstup"></td></tr></tbody></table></td>

</tr>
</tbody></table>
    </div>
</div>  <!-- end wn div -->

<!-- border attribute added to reduce support questions on the subject. 
    If you like valid strict markup, remove and place a img {border:none;} spec in style sheet -->
<div id="scrollbar">
    <div id="left"><a class="mouseover_left" href="#"><img src="/images/btn-lft.gif"  alt="" border="0" /></a></div>

    <div id="track">
         <div id="dragBar">Ekka</div>
    </div>
    <div id="right"><a class="mouseover_right" href="#"><img src="/images/btn-rt.gif"  alt="" border="0" /></a></div>
  </div>
</div>
  </center>


</body>
</html>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>