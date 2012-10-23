<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");

if(!defined('NOT_CHECK_PERMISSIONS') || NOT_CHECK_PERMISSIONS !== true)
{
	if (!$USER->CanDoOperation('view_other_settings'))
	    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/checklist.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/lang/".LANG."/admin/checklist.php");
$APPLICATION->AddHeadString('
	<style type="text/css">
        p,ul,li{font-size:100%!important;}
    </style>
');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

CUtil::InitJSCore(Array('ajax','window','popup','fx'));

$checklist = new CCheckList();
$arPoints = $checklist->GetPoints();

if($_REQUEST["TEST_ID"] && $arPoints[$_REQUEST["TEST_ID"]]):?>
	<?
	$arTestID = $_REQUEST["TEST_ID"];
	$arPosition = 0;
	foreach($arPoints as $k=>$v)
	{
		$arPosition++;
		if ($k==$arTestID)
			break;
	}
	$arTotal = count($arPoints);
	if(strlen($arPoints[$arTestID]["STATE"]["COMMENTS"]["SYSTEM"]["DETAIL"])>0)
	$display="inline-block";
	else
		$display="none";
	$display_result = (count($arPoints[$arTestID]["STATE"]["COMMENTS"]["SYSTEM"])>0)?"block":"none";
	$APPLICATION->RestartBuffer();?>
	<div id="test_detail_content" style="z-index: 1000; position: absolute; top:10px; min-width:700px;">
					<span class="bx-check-list-dit"><?=$arPosition.GetMessage("CL_FROM").$arTotal?></span>
        <div class="bx-core-admin-dialog-head" style="display: block;">
         <div class="bx-core-dialog-head-content">
             <span id="tabs" class="tabs">
                 <a class="tab-container-selected" id="tab_cont_edit1"><span class="tab-left"><span class="tab-right"><?=GetMessage("CL_TAB_TEST");?></span></span></a>
                 <a class="tab-container" id="tab_cont_edit2" ><span class="tab-left"><span class="tab-right"><?=GetMessage("CL_TAB_DESC");?></span></span></a>
             </span>
         </div>
        <div>
         <div style="margin:6px;" class="edit-tab-inner" id="edit1" style="display:block;">
             <div class="checklist-popup-test">
                 <span class="checklist-popup-name-test"><?=GetMessage("CL_TEST_NAME");?>:</span>
                 <span class="checklist-popup-test-text"><?=$arPoints[$arTestID]["NAME"];?>&nbsp;(<?=$arTestID;?>)</span>
             </div>
             <div class="checklist-popup-test">
                 <div class="checklist-popup-name-test"><?=GetMessage("CL_TEST_STATUS");?></div>
                 <div class="checklist-popup-tes-status-wrap" id="checklist-popup-tes-status">
                     <span class="checklist-popup-tes-status"><span
                             class="checklist-popup-tes-waiting-l"></span><span
                             class="checklist-popup-tes-waiting-c"><?=GetMessage("CL_W_STATUS");?></span><span
                             class="checklist-popup-tes-waiting-r"></span><input name="checklist-form-radio" type="radio" value="W" id="W_status" /></span>
                     <span
                         class="checklist-popup-tes-status"><span
                             class="checklist-popup-tes-successfully-l"></span><span
                             class="checklist-popup-tes-successfully-c"><?=GetMessage("CL_A_STATUS");?></span><span
                             class="checklist-popup-tes-successfully-r"></span><input name="checklist-form-radio" type="radio" value="A" id="A_status" /></span>
                      <span
                         class="checklist-popup-tes-status"><span
                             class="checklist-popup-tes-fails-l"></span><span
                             class="checklist-popup-tes-fails-c"><?=GetMessage("CL_F_STATUS");?></span><span
                             class="checklist-popup-tes-fails-r"></span><input name="checklist-form-radio" value="F" id="F_status" name="checklist-form-radio" type="radio"  /></span><span
                         class="checklist-popup-tes-status"><span
                             class="checklist-popup-tes-not-necessarily-l"></span><span
                             class="checklist-popup-tes-not-necessarily-c"><?=GetMessage("CL_S_STATUS");?></span><span
                             class="checklist-popup-tes-not-necessarily-r"></span><input name="checklist-form-radio" type="radio" value="S" id="S_status" /></span>
                 </div>
             </div>
             <div id="bx_test_result" style="display:<?=$display_result;?>" class="checklist-popup-test">
			<div class="checklist-popup-name-test"><?=GetMessage("CL_RESULT_TEST");?>:</div>
                 <div class="checklist-popup-test-text">
				<span id="system_comment"><?=($arPoints[$arTestID]["STATE"]["COMMENTS"]["SYSTEM"]["PREVIEW"])?$arPoints[$arTestID]["STATE"]["COMMENTS"]["SYSTEM"]["PREVIEW"]:"&mdash;";?></span>
				<div align="right"><span id="show_detail_link" onclick="ShowDetailComment()" class="checklist-popup-test-link"><?=GetMessage("CL_MORE_DETAILS");?></span></div>
				<div style="display:none" id="detail_system_comment_<?=$arTestID;?>">
					<div class="checklist-system-textarea"><?=preg_replace("/\r\n|\r|\n/",'<br>',$arPoints[$arTestID]["STATE"]["COMMENTS"]["SYSTEM"]["DETAIL"]);?></div>
				</div>
			</div>
		</div>
		<?if($arPoints[$arTestID]["AUTO"] == "Y"):?>
			<div class="checklist-popup-start-test-block">
				<a onclick="StartPointAutoCheck();" id="bx_start_button_detail" class="checklist-top-button checklist-top-button-detail">
					<span class="checklist-button-left-corn"></span><span id ="bx_autotest_btn"
						class="checklist-button-cont"><?=GetMessage("CL_AUTOTEST_START");?></span><span
						class="checklist-button-right-corn"></span>
					<span class="checklist-loader"></span>
				</a><span id="bx_per_point_done" class="checklist-popup-start-test-text"></span>
			</div>
		<?endif;?>
             <div id="check_list_comments" class="checklist-popup-result-test-block">
                 <div class="checklist-popup-result-form">
                     <div class="checklist-form-textar-block">
                         <div class="checklist-form-textar-status"><?=GetMessage("CL_TESTER");?></div>
						<div class="checklist-dot-line"></div>
                         <div OnClick = "BX('performer_comment_edit_area').style.display ='block'; this.style.display='none';BX('performer_comment').focus();" OnMouseOver="BX.addClass(this,'checklist-form-textar');BX.removeClass(this,'checklist-form-textar-non-active');" OnMouseOut="BX.addClass(this,'checklist-form-textar-non-active');BX.removeClass(this,'checklist-form-textar');" id="performer_comment_area"  class="checklist-form-textar-non-active" ><?=preg_replace("/\r\n|\r|\n/",'<br>', htmlspecialchars($arPoints[$arTestID]["STATE"]["COMMENTS"]["PERFOMER"]));?></div><div id="performer_comment_edit_area" style="display:none;"><textarea id="performer_comment" OnBlur = "BX('performer_comment_area').style.display ='block';BX('performer_comment_edit_area').style.display='none';CopyText(this,BX('performer_comment_area'));" class="checklist-form-textar"><?=htmlspecialchars($arPoints[$arTestID]["STATE"]["COMMENTS"]["PERFOMER"])?></textarea></div>
					</div>
                    <div class="checklist-form-textar-block">
                        <div class="checklist-form-textar-status"><?=GetMessage("CL_VENDOR");?></div>
						<div class="checklist-dot-line"></div>
						<div OnClick = "BX('customer_comment_edit_area').style.display ='block'; this.style.display='none';BX('customer_comment').focus();" OnMouseOver="BX.addClass(this,'checklist-form-textar');BX.removeClass(this,'checklist-form-textar-non-active');" OnMouseOut="BX.addClass(this,'checklist-form-textar-non-active');BX.removeClass(this,'checklist-form-textar');" id="customer_comment_area"  class="checklist-form-textar-non-active" ><?=preg_replace("/\r\n|\r|\n/",'<br>', htmlspecialchars($arPoints[$arTestID]["STATE"]["COMMENTS"]["CUSTOMER"]));?></div>
                         <div id="customer_comment_edit_area" style="display:none;"><textarea id="customer_comment" OnBlur = "BX('customer_comment_area').style.display ='block';BX('customer_comment_edit_area').style.display='none'; CopyText(this,BX('customer_comment_area'));" class="checklist-form-textar"><?=htmlspecialchars($arPoints[$arTestID]["STATE"]["COMMENTS"]["CUSTOMER"])?></textarea></div>
					</div>
                     <input id="bx_sc_btn" type="button" value="<?=GetMessage("CL_SAVE_COMMENTS");?>" onclick="SaveStatus();"/>
                 </div>
             </div>
         </div>
         <div style="display:none;margin:6px;" class="edit-tab-inner" id="edit2">
			<div class="checklist-popup-test">
					<div class="checklist-popup-name-test"><?=GetMessage("CL_TAB_DESC");?></div>
					<div class="checklist-popup-test-text">
						<div class="checklist-popup-result-form">
						<?if($arPoints[$arTestID]["DESC"]):
							echo $arPoints[$arTestID]["DESC"];
						else:
							echo GetMessage("CL_EMPTY_DESC");
						endif;?>
					</div>
					</div>
				</div>
				<div class="checklist-popup-test">
					<div class="checklist-popup-name-test"><?=GetMessage("CL_NOW_TO_TEST_IT");?></div>
					<div class="checklist-popup-test-text">
						<div class="checklist-popup-result-form">
						<?if($arPoints[$arTestID]["HOWTO"]):?>
							<div class="checklist-popup-code">
								<?=$arPoints[$arTestID]["HOWTO"];?>
							</div>
						<?else:?>
							<?=GetMessage("CL_EMPTY_DESC");?>
						<?endif;?>
					</div>
					</div>
				</div>
				<?if($arPoints[$arTestID]["AUTOTEST_DESC"]):?>
				<div class="checklist-popup-test">
					<div class="checklist-popup-name-test"><?=GetMessage("CL_NOW_AUTOTEST_WORK");?></div>
					<div class="checklist-popup-test-text">
						<div class="checklist-popup-result-form">
							<?=$arPoints[$arTestID]["AUTOTEST_DESC"];?>
						</div>
					</div>
				</div>
			<?endif;?>
         </div>
    </div>
	<script>
	var test_is_run = false;
	var testID = "<?=$arTestID;?>";
	var step = 0;
	var currentStatus ="<?=$arPoints[$arTestID]["STATE"]["STATUS"]?>";
	BX("<?=$arPoints[$arTestID]["STATE"]["STATUS"]?>_status").checked = true;
	BX("show_detail_link").style.display='<?=$display;?>';
	var test_buttons={
            buttons:BX.findChildren(BX('checklist-popup-tes-status'), {className:'checklist-popup-tes-status'}),
            clickable:function(){
                for(var i=0; i<this.buttons.length; i++){
                    BX.bind(this.buttons[i], 'click', this.active_button);
                }
            },
            active_button:function(event){
				if (test_is_run == true)
				{
					ShowStatusAlert("bx_autotest_btn","<?=addslashes(GetMessage("CL_NEED_TO_STOP"));?>");
					return;
                }
				for(var i=0;i<test_buttons.buttons.length; i++){
                    if(test_buttons.buttons[i]==this){
                        BX.addClass(this,'checklist-popup-tes-active');
                        BX.findChild(this,{tagName:'input'},false).checked=true;
                        SaveStatus(BX.findChild(this,{tagName:'input'},false));
                    }
                    else{
                        BX.removeClass(test_buttons.buttons[i], 'checklist-popup-tes-active');
						BX.findChild(test_buttons.buttons[i],{tagName:'input'},false).checked=false;
                    }
                }
            }
        };
        test_buttons.clickable()
		BX.addClass(BX(currentStatus+"_status").parentNode,'checklist-popup-tes-active');
		if (BX('performer_comment_area').innerHTML.length<=0)
		{
			BX('performer_comment_area').style.color="#999";
			BX('performer_comment_area').style.fontWeight="lighter";
			BX('performer_comment_area').innerHTML = '<?=GetMessage("CL_NO_COMMENT");?>';
		}
		if (BX('customer_comment_area').innerHTML.length<=0)
		{
			BX('customer_comment_area').style.color="#999";
			BX('customer_comment_area').style.fontWeight="lighter";
			BX('customer_comment_area').innerHTML = '<?=GetMessage("CL_NO_COMMENT");?>';
		}

	var tabs = BX.findChildren(BX('tabs'), {tagName:'a'}, false);
    var blocks=[BX('edit1'), BX('edit2')];
	   for(var i=0; i < tabs.length;i++){
            tabs[i].onclick=function(){popup_tabs(this, this.id)};
        }
    function popup_tabs(_this, id)
	{
         for(var i=0; i<tabs.length; i++){
             blocks[i].style.display='none';
             BX.removeClass(tabs[i], 'tab-container-selected');
             BX.addClass(tabs[i], 'tab-container');
         }

          BX.removeClass(_this, 'tab-container');
          BX.addClass(_this, 'tab-container-selected');
          BX(id.substring(9)).style.display='block';
          return false;
    }

    function ShowDetailComment()
    {
		var DetailWindow = new BX.CAdminDialog(
				{
					title: "<?=GetMessage("CL_MORE_DETAILS");?>",
					head: "",
					content: BX("detail_system_comment_"+testID).innerHTML.replace(/\r\n|\r|\n/g,'<br>'),
					icon: "head-block",
					resizable: true,
					draggable: true,
					height: "400",
					width: "700",
					buttons: [BX.CAdminDialog.btnClose]
				}
			);

			DetailWindow.Show();
	}

	function ShowStatusAlert(bindElement,text,hide,class_name)
	{
		if (!hide)
		var hide = false;
		if (!class_name)
			class_name = "checklist-alert-comment";
		var bx_info = document.createElement('div');
					BX.addClass(bx_info,class_name);
					bx_info.innerHTML = text;
		var bx_alert = BX.PopupWindowManager.create(
			"bx_alert"+Math.random(),
			BX(bindElement),
			{
				autoHide : true,
				lightShadow : true,
				closeIcon:false,
				angle:true,
				offsetLeft:50,
				zIndex:100100
			}
		);
		bx_alert.setContent(bx_info);
		if(hide == true)
		BX(bindElement).onmouseout = function(){bx_alert.close();};
		BX.addCustomEvent(Dialog,"OnWindowClose",function(){if (bx_alert) bx_alert.close();});
		bx_alert.show();
	}

	function ShowPopupDetail(_this)
	{
		ShowStatusAlert(_this.id,"<?=addslashes(GetMessage("CL_MORE_DETAILS_INF"));?>",true,"checklist-alert-comment-detail");
	}

    function SaveStatus(_this)
    {
		var status = currentStatus;
		if (_this)
		{
			status = _this.value;
			if (status == currentStatus)
				return;
		}
		if (status == "S")
		{
			if ( BX("customer_comment").value.length <5 && BX("performer_comment").value.length <5)
			{
				BX(currentStatus+"_status").checked = true;
				BX.addClass(BX(currentStatus+"_status").parentNode,'checklist-popup-tes-active');
				if (_this)
				{
					BX.removeClass(BX(_this.value+"_status").parentNode,'checklist-popup-tes-active');
					_this.checked = false;
				}
				ShowStatusAlert("performer_comment_area","<?=addslashes(GetMessage("CL_EMPTY_COMMENT"));?>");
				return;
			}
		}

		currentStatus = status;
		BX(currentStatus+"_status").checked = true;
		ShowWaitWindow();
		Dialog.hideNotify();
		var query_str = "ACTION=update&STATUS="+status+"&TEST_ID="+testID+"&COMMENTS=Y"+"&custom_comment="+BX("customer_comment").value+"&perfomer_comment="+BX("performer_comment").value+"&lang=<?=LANG;?>";
		if (_this)
			query_str+="&CAN_SHOW_CP_MESSAGE=Y";
		BX.ajax.post("/bitrix/admin/checklist.php",query_str,TestResultSimple);
	}

	function StartPointAutoCheck()
	{
		var callback = function(data)
		{
		 	try
		 	{
		 		var json_data=eval("(" +data+")");
		 		var show_result = false;
		 		var buttons = BX.findChildren(BX('checklist-popup-tes-status'), {className:'checklist-popup-tes-status'});
		 		if (json_data.STATUS || stoptest == true)
		 		{
		 			if (json_data.STATUS)
		 			{
		 			 	BX("show_detail_link").style.display = "none";
		 			 	BX("detail_system_comment_<?=$arTestID;?>").innerHTML = "";
		 			 	currentStatus = json_data.STATUS;
		 			 	RefreshCheckList(json_data);
		 			 	for(var i=0; i<buttons.length; i++)
		 			 	BX.removeClass(buttons[i], 'checklist-popup-tes-active');
		 			 	BX.addClass(BX(json_data.STATUS+"_status").parentNode,'checklist-popup-tes-active');
		 			 	BX(json_data.STATUS+"_status").checked = true;
		 			 	if (json_data.SYSTEM_MESSAGE.PREVIEW)
		 			 	{
		 			 		BX("system_comment").innerHTML = json_data.SYSTEM_MESSAGE.PREVIEW;
		 			 		show_result = true;
		 			 	}
		 			 	if (json_data.SYSTEM_MESSAGE.DETAIL)
		 			 	{

		 			 	 	BX("show_detail_link").style.display = "inline-block";
		 			 	 	ShowPopupDetail(BX("show_detail_link"));
		 			 	 	BX("detail_system_comment_<?=$arTestID;?>").innerHTML = "<div class=\"checklist-system-textarea\">"+json_data.SYSTEM_MESSAGE.DETAIL.replace(/\r\n|\r|\n/g,'<br>')+"</div>";
		 			 	 	show_result = true;
		 			 	}
		 			 	if (show_result == true)
		 			 		BX("bx_test_result").style.display = "block";
		 			 	else
		 			 	                                              	BX("bx_test_result").style.display = "none";
		 			 	BX(json_data.STATUS+"_status").checked = true;

		 			 	if (json_data.CAN_CLOSE_PROJECT == "Y")
		 			 		ShowCloseProject();
		 			}
		 			BX("bx_per_point_done").innerHTML = "<?=GetMessage("CL_AUTOTEST_DONE")?>";
		 			loadButton("bx_start_button_detail");
		 			step = 0;
		 			test_is_run = false;
		 		}
		 		else if (json_data.IN_PROGRESS == "Y")
		 		{
		 			BX("bx_per_point_done").innerHTML = "<?=GetMessage("CL_PERCENT_LIVE")?>"+" "+json_data.PERCENT+"%";
		 			BX.ajax.post("/bitrix/admin/checklist.php","ACTION=update&autotest=Y&TEST_ID="+testID+"&STEP="+(++step)+"&lang=<?=LANG;?>",callback);
		 		}
		 		else
		 		{
		 		 	loadButton("bx_start_button_detail");
		 		 	step = 0;
		 		 	test_is_run = false;
		 		}
		 	}catch(e){
		 		 	loadButton("bx_start_button_detail");
		 		 	step = 0;
		 		 	test_is_run = false;
		 		 	stoptest = false;
		 		}
		}

		if (test_is_run == true)
		{
		 	var buttonText =  BX.findChild(BX("bx_start_button_detail"), {className:'checklist-button-cont'}, true, false);
		 	buttonText.innerHTML = "<?=GetMessage("CL_END_TEST_PROCCESS");?>";
		 	stoptest = true;
		 	return;
		}
		BX("bx_per_point_done").innerHTML = "";
		test_is_run = true;
		stoptest = false
		loadButton("bx_start_button_detail");
		BX.ajax.post("/bitrix/admin/checklist.php","ACTION=update&autotest=Y&TEST_ID="+testID+"&STEP="+step+"&lang=<?=LANG;?>",callback);
	}

	function Move(action)
	{
	 	var data = null;
	 	var testtitle = false;
	 	if (action == "prev")
	 		current = prev;
	 	if (action == "next")
	 		current = next;
	 	Dialog.hideNotify();
	 	ShowWaitWindow();
	 	BX.ajax.post(
	 	  	"/bitrix/admin/checklist_detail.php?TEST_ID="+arStates["POINTS"][current].TEST_ID+"&lang=<?=LANG;?>",
	 	  	data,
	 	  	function(data)
	 	  	{
	 	  		ReCalc(current);
	 	  		testtitle = arStates["POINTS"][current].NAME+" - "+arStates["POINTS"][current].TEST_ID;
	 	  		if (arStates["POINTS"][current].IS_REQUIRE == "Y")
	 	  		                           	testtitle = testtitle+" ("+"<?=GetMessage("CL_TEST_IS_REQUIRE");?>"+")";
	 	  		Dialog.SetTitle(testtitle);
	 	  		Dialog.SetContent(data);
	 	  		CloseWaitWindow();
	 	  	}
	 	);


	}

	function CopyText(_this,toDiv)
	{
		var text='';
		if (_this.value.length>0)
		{
			text = jsUtils.htmlspecialchars(_this.value);
			text=text.replace(/\r\n|\r|\n/g,'<br>');
			toDiv.style.color="black";
			toDiv.style.fontWeight="normal";
		}
		else
		{
			toDiv.style.color="#999";
			toDiv.style.fontWeight="lighter";
			text='<?=GetMessage("CL_NO_COMMENT");?>';
		}

		toDiv.innerHTML = text;
	}
    </script>
<?endif;?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_after.php");?>

