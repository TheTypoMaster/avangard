<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");

if(!defined('NOT_CHECK_PERMISSIONS') || NOT_CHECK_PERMISSIONS !== true)
{
	if (!$USER->CanDoOperation('view_other_settings'))
	    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/checklist.php");
IncludeModuleLangFile(__FILE__);
$APPLICATION->AddHeadString(
    '<link type="text/css" rel="stylesheet" href="/bitrix/themes/.default/check-list-style.css">
	<style type="text/css">
        .checklist-button-left-corn {background:url(\'/bitrix/js/main/core/images/controls-sprite.png\') no-repeat left -328px;}
        .checklist-button-cont{background:url(\'/bitrix/js/main/core/images/controls-sprite.png\') repeat-x left -356px;}
        .checklist-button-right-corn {background:url(\'/bitrix/js/main/core/images/controls-sprite.png\') no-repeat -6px -328px;}
    </style>
');
$APPLICATION->SetTitle(GetMessage("CL_TITLE_CHECKLIST"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
CUtil::InitJSCore(Array('ajax','window','popup','fx'));
if (($res = CCheckListResult::GetList(Array(),Array("REPORT"=>"N"))->Fetch())||($_POST["bx_start_test"] == "Y") || $_POST["ACTION"]):
	$checklist = new CCheckList();
	$isFisrtTime =  CUserOptions::GetOption("checklist","autotest_start","N",false);
	$arStructure = $checklist->GetStructure();
	$arPoints = $checklist->GetPoints();

	if ($_POST["ACTION"] == "update")
	{
		$arTestID = $_POST["TEST_ID"];
		if ($_POST["autotest"]=="Y")//start autotest
		{
			$arStep = intval($_POST["STEP"]);
			$arResult = $checklist->AutoCheck($arTestID,Array("STEP"=>$arStep));
		}
		else
		{
			if ($_POST["COMMENTS"] == "Y")//update only comments
			{
				$arPointFields["COMMENTS"] = $arPoints[$arTestID]["STATE"]["COMMENTS"];
				if ($_POST["perfomer_comment"] && strlen(trim($_POST["perfomer_comment"]))>1)
				$arPointFields["COMMENTS"]["PERFOMER"] = $_POST["perfomer_comment"];
				else
					unset($arPointFields["COMMENTS"]["PERFOMER"]);
				if ($_POST["custom_comment"] && strlen(trim($_POST["custom_comment"]))>1)
					$arPointFields["COMMENTS"]["CUSTOMER"] = $_POST["custom_comment"];
				else
					unset($arPointFields["COMMENTS"]["CUSTOMER"]);

				if (ToUpper(SITE_CHARSET) != "UTF-8" && $arPointFields["COMMENTS"])
				{
					if ($arPointFields["COMMENTS"]["PERFOMER"])
						$arPointFields["COMMENTS"]["PERFOMER"] = $APPLICATION->ConvertCharsetArray($arPointFields["COMMENTS"]["PERFOMER"],"UTF-8",SITE_CHARSET);
					if($arPointFields["COMMENTS"]["CUSTOMER"])
						$arPointFields["COMMENTS"]["CUSTOMER"] = $APPLICATION->ConvertCharsetArray($arPointFields["COMMENTS"]["CUSTOMER"],"UTF-8",SITE_CHARSET);
				}

				$arPointFields["STATUS"] = $arPoints[$arTestID]["STATE"]["STATUS"];
			}
			if ($_POST["STATUS"])//update only status
				$arPointFields["STATUS"] = $_POST["STATUS"];

				$checklist->PointUpdate($arTestID,$arPointFields);
			if ($checklist->Save())
			{
				$arResult = Array(
					"STATUS"=>$arPointFields["STATUS"],
					"IS_REQUIRE"=>$arPoints[$arTestID]["REQUIRE"],
					"COMMENTS_COUNT" =>count($arPointFields["COMMENTS"]),
				);
			}
			else
				$arResult = Array("RESULT"=>"ERROR");
		}

		$arTotal = $checklist->GetSectionStat();
		$arCode = $checklist->checklist["CATEGORIES"][$arPoints[$arTestID]["PARENT"]]["PARENT"];
		if ($arCode)
		{
			$arParentCode = $arCode;
			$arSubParentCode = $arPoints[$arTestID]["PARENT"];
		}
		else
			$arParentCode = $arSubParentCode = $arPoints[$arTestID]["PARENT"];

		$arSubParentStat = $checklist->GetSectionStat($arSubParentCode);
		$arParentStat = $checklist->GetSectionStat($arParentCode);

		//////////////////////////////////////////
		//////////////JSON ANSWER/////////////////
		//////////////////////////////////////////
		$arParentStat["ID"] = $arParentCode;
		$arSubParentStat["ID"] = $arSubParentCode;
		$arResultAdditional = Array(
				"PARENT"=>$arParentStat,
				"SUB_PARENT"=>$arSubParentStat,
				"TEST_ID"=>$arTestID,
				"CAN_CLOSE_PROJECT"=>(!$_POST["CAN_SHOW_CP_MESSAGE"])?"N":$arTotal["CHECKED"],
				"TOTAL"=>$arTotal["TOTAL"],
				"FAILED"=>$arTotal["FAILED"],
				"SUCCESS"=>$arTotal["CHECK"],
				"REQUIRE"=>$arTotal["REQUIRE"],
				"REQUIRE_CHECK"=>$arTotal["REQUIRE_CHECK"],
				"WAITING"=>$arTotal["WAITING"],
				"MAIN_STAT"=>Array(
					"TOTAL"=>$arTotal["FAILED"]+$arTotal["CHECK"],
					"SUCCESS"=>$arTotal["CHECK"],
					"FAILED"=>$arTotal["FAILED"],
					"REQUIRE"=>$arTotal["REQUIRE"],
					"REQUIRE_CHECK"=>$arTotal["REQUIRE_CHECK"]
				)
			);
		$arResult = array_merge($arResultAdditional,$arResult);
		$APPLICATION->RestartBuffer();
		echo CUtil::PhpToJsObject($arResult);
		die();
	}

	if ($_POST["ACTION"] == "ADDREPORT")//add report
	{
		if ($_FILES["PICTURE"])
		{
			$arFile = $_FILES["PICTURE"];
			if (!CFile::CheckImageFile($arFile))
			{
				CFile::ResizeImage(&$arFile, array('width'=>30, 'height'=>30), BX_RESIZE_IMAGE_PROPORTIONAL,true);
				if ($FID = CFile::SaveFile($arFile,"main"))
					$arFields["PICTURE"] = $FID;
			}
		}
		if ($_POST["COMPANY_NAME"])
			$arFields["COMPANY_NAME"] = $_POST["COMPANY_NAME"];
		if ($_POST["TESTER"])
			$arFields["TESTER"] = $_POST["TESTER"];
		if ($_POST["COMMENTS"])
			$arFields["COMMENT"] = $_POST["COMMENTS"];

		$res = $checklist->AddReport($arFields);
		LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANG,true);
	}

/////////////////////////////////////////////////////////
//////////////////////PARAMS_PREPARE/////////////////////
/////////////////////////////////////////////////////////
	$arSections = $checklist->GetSections();
	$arStat = $checklist->GetSectionStat();
	$arCanClose = $arStat["CHECKED"];

	foreach ($arPoints as $key=>$arFields)
	{
		$arStates["POINTS"][] = Array(
			"TEST_ID" => $key,
			"NAME"=>$arFields["NAME"],
			"STATUS" => $arFields["STATE"]["STATUS"],
			"IS_REQUIRE" => ($arFields["REQUIRE"])?$arFields["REQUIRE"]:"N",
			"AUTO" => $arFields["AUTO"],
			"COMMENTS_COUNT" => count($arFields["STATE"]["COMMENTS"]),
		);

		if ($arFields["AUTO"] == "Y")
		{
			$arAutoCheck["ID"][]=$key;
			$arAutoCheck["NAME"][]=$arFields["NAME"];
		}
	}

	foreach ($arSections as $key=>$arFields)
	{
		$arStats = $checklist->GetSectionStat($key);
		$arStates["SECTIONS"][] = Array(
			"ID" => $key,
			"CHECKED" => $arStats["CHECKED"],
			"TOTAL" => $arStats["TOTAL"],
			"PARENT" => $arFields["PARENT"],
			"CHECK" => $arStats["CHECK"],
			"FAILED"=>  $arStats["FAILED"]
		);
	}
	$arStates = CUtil::PhpToJsObject($arStates);

/////////////////////////////////////////////////////////
//////////////////////END_PREPARE////////////////////////
/////////////////////////////////////////////////////////
?>
	<div class="checklist-wrapper">
		<div class="checklist-top-info">
			<div class="checklist-top-info-right-wrap">
				<span class="checklist-top-info-left">
					<span class="checklist-top-info-left-item"><?=GetMessage("CL_TEST_TOTAL");?>:</span><br/>
					<span class="checklist-top-info-left-item"><?=GetMessage("CL_TEST_REQUIRE");?>:</span><br/>
					<span class="checklist-top-info-left-item checklist-test-successfully"><?=GetMessage("CL_TEST_CHECKED");?>:</span><br/>
					<span class="checklist-top-info-left-item checklist-test-unsuccessful"><?=GetMessage("CL_TEST_FAILED");?>:</span><br/>
					<span class="checklist-top-info-left-item checklist-test-not-necessarily"><?=GetMessage("CL_TEST_NOT_REQUIRE");?>:</span><br/>
					<span class="checklist-top-info-left-item not-necessarily"><?=GetMessage("CL_TEST_WAITING");?>:</span><br/>
				</span><span class="checklist-top-info-right-nambers">
					<span id="total" class="checklist-top-info-left-item-qt"><?=$arStat["TOTAL"]?></span><br/>
					<span class="checklist-top-info-left-item-qt"><?=$arStat["REQUIRE"]?></span><br/>
					<span id="success" class="checklist-test-successfully"><?=$arStat["CHECK"]?></span><br/>
					<span id="failed" class="checklist-test-unsuccessful"><?=$arStat["FAILED"]?></span><br/>
					<span class="checklist-test-not-necessarily"><?=($arStat["TOTAL"] - $arStat["REQUIRE"]);?></span><br/>
					<span id="waiting" class="checklist-top-info-left-item-qt"><?=$arStat["WAITING"]?></span>
				</span>
			</div>
			<div class="checklist-top-info-left-wrap">
				<div class="checklist-top-info-right">
					<span><?=GetMessage("CL_CHECK_PROGRESS");?>:</span>
					<div class="checklist-test-completion">
						<div class="checklist-test-completion-right">
							<div class="checklist-test-completion-cont">
								<span id="progress" class="checklist-test-completion-quan"><?=GetMessage("CL_TEST_PROGRESS",Array("#check#"=>$arStat["CHECK"]+$arStat["FAILED"],"#total#"=>$arStat["TOTAL"]));?></span>
								<div id="progress_bar" class="checklist-test-completion-pct" style="width:<?=round(($arStat["CHECK"]+$arStat["FAILED"])/($arStat["TOTAL"]*0.01),0)?>%;"></div>
							</div>
						</div>
					</div>
					<span id="current_test_name" class="checklist-test-completion-text"></span><span id="percent"></span>
				</div>
			</div>

			<div class="checklist-clear"></div>
			<a id="bx_start_button" class="checklist-top-button" onclick="StartAutoCheck()">
				<span class="checklist-button-left-corn"></span><span class="checklist-button-cont"><?=GetMessage("CL_BEGIN_AUTOTEST");?></span><span class="checklist-button-right-corn"></span>
				<span class="checklist-loader"></span>
			</a>
		</div>
	<ul class="checklist-testlist">
	<?foreach($arStructure["STRUCTURE"] as $rkey=>$rFields):?>
		<li class="checklist-testlist-level1">
			<div class="checklist-testlist-text" id="<?=$rkey;?>_name"><?=$rFields["NAME"];?><span id="<?=$rkey;?>_stat" class="checklist-testlist-amount-test"></span>
			<span class="checklist-testlist-marker-list"></span>
			</div>
			<ul class="checklist-testlist-level2-wrap">
				<?
				$num = 1;
				foreach($rFields["POINTS"] as $pkey=>$pFields):?>
				<li id="<?=$pkey;?>" class="checklist-testlist-level3">
					<span class="checklist-testlist-level3-cont">
						<span class="checklist-testlist-level3-cont-nom"><?=$num++.". ";?></span>
						<span class="checklist-testlist-level3-cont-right">
							<span class="checklist-testlist-level3-cont-border" onclick='ShowPopupWindow("<?=$pkey;?>","<?=addslashes($pFields["NAME"]);?>");'>							<?=$pFields["NAME"];?>
							</span>
							<span id="comments_<?=$pkey;?>" onclick='ShowPopupWindow("<?=$pkey;?>","<?=addslashes($pFields["NAME"]);?>");' class="checklist-testlist-comments" ><?=count($pFields["STATE"]["COMMENTS"]);?></span>

						</span>
					</span>
					<span id="mark_<?=$pkey;?>"></span>
				</li>
				<?endforeach;?>
				<?foreach($rFields["CATEGORIES"] as $skey=>$sFields): $num = 1;?>
					<li class="checklist-testlist-level2">
						<div class="checklist-testlist-text" id="<?=$skey;?>_name" ><?=$sFields["NAME"];?><span id="<?=$skey;?>_stat" class="checklist-testlist-amount-test"></span>
							<span class="checklist-testlist-marker-list"></span>
						</div>
						<ul class="checklist-testlist-level3-wrap">
							<?foreach($sFields["POINTS"] as $pkey=>$pFields):?>
								<li id="<?=$pkey;?>" class="checklist-testlist-level3">
									<span class="checklist-testlist-level3-cont">
										<span class="checklist-testlist-level3-cont-nom"><?=$num++.". ";?></span>
										<span class="checklist-testlist-level3-cont-right">
											<span class="checklist-testlist-level3-cont-border" onclick='ShowPopupWindow("<?=$pkey;?>","<?=addslashes($pFields["NAME"]);?>");'><?=$pFields["NAME"];?></span>
											<span id="comments_<?=$pkey;?>" class="checklist-testlist-comments" onclick='ShowPopupWindow("<?=$pkey;?>","<?=addslashes($pFields["NAME"]);?>");'><?=count($pFields["STATE"]["COMMENTS"]);?></span>
										</span>
									</span>
									<span id="mark_<?=$pkey;?>"></span>
								</li>
							<?endforeach;?>
						</ul>
					</li>
				<?endforeach;?>
			</ul>
		</li>
	<?endforeach;?>
	</ul>
		<div class="checklist-result-form">
			<div class="checklist-result-form-top">
				<span class="checklist-result-left"><span class="checklist-result-text"><?=GetMessage("CL_TEST_RESULT");?>:</span>
				<span id = "bx_count_check" class="checklist-result-num">
					<?=$arStructure["STAT"]["CHECK"];?></span>&nbsp;<?=GetMessage("CL_TEST_CHECKED_COUNT_FROM");?>&nbsp;<span id="bx_count_from" class="checklist-result-num"><?=$arStructure["STAT"]["CHECK"]+$arStructure["STAT"]["FAILED"];?></span>&nbsp;<?=GetMessage("CL_TEST_CHECKED_COUNT");?></span>
				<span id="bx_project_checkbox" class="checklist-result-checkbox checklist-result-checkbox-disabled"><input id="project_check" type="checkbox" onclick = "hide_project_form(this);"/><label for="project_check"><?=GetMessage("CL_PASS_PROJECT");?></label></span>
			</div>
			<form id="bx_project_form" style="display:none;" action="" method="POST" enctype="multipart/form-data">
				<div class="checklist-result-form-content" >
					<div class="checklist-result-form-content-date"><?=GetMessage("CL_REPORT_DATE");?>: <?=date("d.m.Y");?></div>
					<div class="checklist-result-form-content-data-left">
						<label><?=GetMessage("CL_REPORT_COMPANY_NAME");?></label><input id ="COMPANY_NAME" name="COMPANY_NAME" type="text"/>
					</div>
					<div class="checklist-result-form-content-data-right">
						<label><?=GetMessage("CL_REPORT_TESTER_NAME");?></label><input id ="TESTER" name="TESTER" type="text"/>
					</div>
					<div class="checklist-result-form-content-upload">
						<img src="/bitrix/themes/.default/images/checklist/avatar.jpg" alt="img" />
						<input type="file" name="PICTURE" />
						<span class="checklist-upload-text"><?=GetMessage("CL_UPLOAD_IMAGE");?></span>
					</div>
					<div class="checklist-result-textarea-wrap">
						<div class="checklist-result-textarea">
							<textarea id="report_comment" name="COMMENTS" class="checklist-textarea" onblur="textarea_edit(this,0)" onclick="textarea_edit(this,1)"><?=GetMessage("CL_ADD_COMMENT");?></textarea>
						</div>
					</div>
					<input type="hidden" name="ACTION" value="ADDREPORT">
				</div>
				<div class="checklist-result-form-button">
					<span class="checklist-button-form">
						<span class="checklist-button-left-corn" ></span><span class="checklist-button-cont" onclick="SaveReport();" ><?=GetMessage("CL_SAVE_REPORT");?></span><span
							class="checklist-button-right-corn"></span>
					</span>
				</div>
			</form>
		</div>

	<script type="text/javascript">
		var arStates = eval(<?=$arStates;?>);
		var arMainStat ={
			"REQUIRE":<?=$arStat["REQUIRE"];?>,
			"REQUIRE_CHECK":<?=$arStat["REQUIRE_CHECK"];?>,
			"FAILED":<?=$arStat["FAILED"];?>,
			"SUCCESS":<?=$arStat["CHECK"];?>,
			"TOTAL":<?=$arStat["FAILED"]+$arStat["CHECK"];?>
		};
		var arRequireCount=<?=$arStat["REQUIRE"];?>;
		var arRequireCheckCount=<?=$arStat["REQUIRE_CHECK"];?>;
		var arFailedCount = <?=$arStat["FAILED"];?>;
		var CanClose = "<?=$arCanClose;?>";
		var arAutoCheck = new Array('<?=implode("','",$arAutoCheck["ID"]);?>');
		var arAutoCheckName = new Array('<?=implode("','",$arAutoCheck["NAME"]);?>');
		var arTestResult = {"total":0,"success":0,"failed":0};
		var start = "<?=$isFisrtTime;?>";
		var bx_autotest_step = 0;
		var bx_test_num = 0;
		var ErrorSections = new Array();
		var bx_autotest=false;
		var bx_stoptest=false;
		var Dialog = false;
		var checklist_div= document.getElementsByTagName('div');
		var body = document.getElementsByTagName('body');
		var current = 0;
		var next = 0;
		var prev = 0;

		if(start=="N")
			BX.ready(function(){ShowPopupManual()});
			BX.ready(function(){InitState();});


		 var list_binds={
            checklist_span:BX.findChildren(document,{className:'checklist-testlist-text'}, true),
            hover_link:BX.findChildren(document,{className:'checklist-testlist-level3-cont'}, true),
            show_list:function(){
                BX.hasClass(this.parentNode,'testlist-open')?BX.removeClass(this.parentNode, 'testlist-open'):BX.addClass(this.parentNode, 'testlist-open');
            },
            hover_border:function(event){
                var event = event || window.event;
                if(event.type=='mouseover') BX.findChild(this,{className:'checklist-testlist-level3-cont-border'}, true).style.borderBottom='1px dashed';
                if(event.type=='mouseout') BX.findChild(this,{className:'checklist-testlist-level3-cont-border'}, true).style.borderBottom='none';
            },
            binds:function(){
                for(var i=0; i<this.checklist_span.length; i++){
                    BX.bind(this.checklist_span[i], "click", this.show_list)
                    }
                for(var b=0; b<this.hover_link.length; b++){
                    BX.bind(this.hover_link[b], 'mouseover', this.hover_border);
                    BX.bind(this.hover_link[b], 'mouseout', this.hover_border)
                }
            }
        };
        list_binds.binds();


		function InitState()
		{
			for (var i=0;i<arStates["SECTIONS"].length;i++)
				ChangeSection(arStates["SECTIONS"][i]);
			for (var i=0;i<arStates["POINTS"].length;i++)
				ChangeStatus(arStates["POINTS"][i]);
			if (CanClose == "Y")
				ShowCloseProject();
			if (ErrorSections.length>0)
				for(var i=0;i<ErrorSections.length;i++)
				{
					if (BX(ErrorSections[i]+"_name").parentNode)
						BX.addClass(BX(ErrorSections[i]+"_name").parentNode, 'testlist-open');
				}
		}

		function textarea_edit(_this, effect){
			if(effect){
				_this.value=="<?=GetMessage("CL_ADD_COMMENT");?>"?_this.value="":false;
				BX.addClass(_this, "checklist-textarea-active");
			}
			if(!effect){
				if(_this.value==''){
					_this.value='<?=GetMessage("CL_ADD_COMMENT");?>';
					BX.removeClass(_this, "checklist-textarea-active");
				}
			}
		}

		function loadButton(id){
			BX.toggleClass(BX(id), 'checklist-top-button-load');
			var buttonText =  BX.findChild(BX(id), {className:'checklist-button-cont'}, true, false);
			buttonText.innerHTML=='<?=GetMessage("CL_BEGIN_AUTOTEST");?>' ? buttonText.innerHTML='<?=GetMessage("CL_END_TEST");?>' : buttonText.innerHTML='<?=GetMessage("CL_BEGIN_AUTOTEST");?>';
			return false
		}

		function ShowPopupWindow(testID,head_name)
		{
			current = 0;
			next = 0;
			prev = 0;
			Dialog = new BX.CAdminDialog(
				{
					title: head_name+" - "+testID,
					head: "",
					content_url: "/bitrix/admin/checklist_detail.php?TEST_ID="+testID+"&lang=<?=LANG;?>",
					icon: "head-block",
					resizable: false,
					draggable: true,
					height: "530",
					width: "700"
				}
			);
			Dialog.SetButtons(['<input id="prev" type="button" onclick="Move(\'prev\');"name="prev" value="<?=GetMessage("CL_PREV_TEST");?>"><input id="next" type="button" name="next" onclick="Move(\'next\');" value="<?=GetMessage("CL_NEXT_TEST");?>">']);
			for (var i=0;i<arStates["POINTS"].length;i++)
			{
				if (arStates["POINTS"][i].TEST_ID == testID)
				{


					if (arStates["POINTS"][i].IS_REQUIRE == "Y")
						Dialog.SetTitle(head_name+" - "+testID+" ("+"<?=GetMessage("CL_TEST_IS_REQUIRE");?>"+")");

					current = i;
					ReCalc(current);
					break;
				}
			}
			Dialog.Show();
		}


		function ReCalc(current)
		{
			BX("next").disabled = null;
			BX("prev").disabled = null;
			prev = current-1;
			next = current+1;
			if (current == 0)
			{
				BX("prev").disabled = "disabled";
				next = current+1;
			}
			if (current == (arStates["POINTS"].length-1))
			{
				BX("next").disabled = "disabled";
				prev = current-1;
			}
		}

		function hide_project_form(_this)
		{
			if (CanClose != "Y")
			{
				_this.checked = false;
				var bx_info = document.createElement('div');
				BX.addClass(bx_info,"checklist-alert-comment");
				var result = "";
				result+= "<?=addslashes(GetMessage("CL_CANT_CLOSE_PROJECT"));?>";
				result+= "<br><br><?=addslashes(GetMessage("CL_CANT_CLOSE_PROJECT_PASSED_REQUIRE"));?>"+"<b>"+arMainStat.REQUIRE_CHECK+"<?=GetMessage("CL_FROM")?>"+arMainStat.REQUIRE+"</b>"
				result+= "<br><?=addslashes(GetMessage("CL_CANT_CLOSE_PROJECT_FAILED"));?>"+"<b>"+arMainStat.FAILED+"</b>";
				bx_info.innerHTML = result;
				var project_info = BX.PopupWindowManager.create(
					"project_info",
					null,
					{
						autoHide : true,
						lightShadow : true,
						closeIcon:true,
						zIndex:100100
					}
				);
				project_info.setContent(bx_info);
				project_info.setButtons([
				new BX.PopupWindowButton({text : "<?=GetMessage("CL_CLOSE");?>", className : "", events : { click : function(){
				if (ErrorSections.length>0)
				for(var i=0;i<ErrorSections.length;i++)
				{
					if (BX(ErrorSections[i]+"_name").parentNode)
						BX.addClass(BX(ErrorSections[i]+"_name").parentNode, 'testlist-open');
				}
				project_info.close();
				} } })

			]);
				project_info.show();
				return;
			}
			if (_this.checked == true)
				BX("bx_project_form").style.display ="block";
			else
				BX("bx_project_form").style.display ="none";
		}

		function ChangeStatus(element)
		{
			BX.removeClass(BX(element.TEST_ID), BX(element.TEST_ID).className);
			BX("mark_"+element.TEST_ID).className = "";

			if (element.STATUS == "F")
			{
				arTestResult.failed++;
				BX.addClass(BX(element.TEST_ID),"checklist-testlist-red");
				BX.addClass(BX("mark_"+element.TEST_ID),"checklist-testlist-item-closed");
			}else if (element.STATUS == "A")
			{
				arTestResult.success++;
				BX.addClass(BX(element.TEST_ID),"checklist-testlist-green");
				BX.addClass(BX("mark_"+element.TEST_ID),"checklist-testlist-item-done");
			}else if (element.STATUS == "W")
			{
				if (element.IS_REQUIRE == "Y")
					BX.addClass(BX(element.TEST_ID),"checklist-testlist-black");
				else
					BX.addClass(BX(element.TEST_ID),"checklist-testlist-grey");
			}else if (element.STATUS == "S")
			{
				if (element.IS_REQUIRE == "Y")
					BX.addClass(BX(element.TEST_ID),"checklist-testlist-black checklist-testlist-through");
				else
					BX.addClass(BX(element.TEST_ID),"checklist-testlist-grey checklist-testlist-through");
			}
			BX.addClass(BX(element.TEST_ID),"checklist-testlist-level3");

			if (element.COMMENTS_COUNT >0)
			{
				BX("comments_"+element.TEST_ID).innerHTML = element.COMMENTS_COUNT;
				BX.removeClass(BX("comments_"+element.TEST_ID),"checklist-hide");
			}
			else
				BX.addClass(BX("comments_"+element.TEST_ID),"checklist-hide");
		}

		function ChangeSection(data)
		{
			BX(data.ID+"_stat").innerHTML = "(";
			if (data.FAILED>0)
				BX(data.ID+"_stat").innerHTML+= "<span class=\"checklist-testlist-red\">"+data.FAILED+"</span>/";
			BX(data.ID+"_stat").innerHTML+= "<span class=\"checklist-testlist-passed-test\">"+data.CHECK+"</span>/"+data.TOTAL;
			BX(data.ID+"_stat").innerHTML+= ")";
			BX.removeClass(BX(data.ID+"_name"),"checklist-testlist-green");
			if (data.CHECKED == "Y")
				BX.addClass(BX(data.ID+"_name"),"checklist-testlist-green");
			else if(data.FAILED > 0)
				ErrorSections[ErrorSections.length] = data.ID;
		}

		function RefreshCheckList(json_data)
		{
			arTestResult.total++;
			ChangeStatus(json_data);
			BX("progress").innerHTML = parseInt(json_data.SUCCESS)+parseInt(json_data.FAILED)+"<?=GetMessage("CL_FROM")?> "+json_data.TOTAL;
			BX("progress_bar").style.width = Math.round((parseInt(json_data.SUCCESS)+parseInt(json_data.FAILED))/(json_data.TOTAL*0.01))+"%";
			BX("success").innerHTML = json_data.SUCCESS;
			BX("failed").innerHTML = json_data.FAILED;
			BX("waiting").innerHTML = json_data.WAITING;
			BX("bx_count_check").innerHTML = json_data.SUCCESS;
			BX("bx_count_from").innerHTML = parseInt(json_data.SUCCESS)+parseInt(json_data.FAILED);

			ChangeSection(json_data.PARENT);
			if (json_data.PARENT.ID!=json_data.SUB_PARENT.ID)
				ChangeSection(json_data.SUB_PARENT);

			CanClose = json_data.CAN_CLOSE_PROJECT;
			arMainStat = json_data.MAIN_STAT;
			BX("percent").innerHTML = "";
		}


		function TestResultSimple(data)
		{
			try
			{
				var json_data=eval("(" +data+")");
				if (json_data && json_data.STATUS)
				{
					RefreshCheckList(json_data);
					Dialog.Notify("<?=GetMessage("CL_SAVE_SUCCESS");?>");
					//setTimeout("Dialog.hideNotify()",2000);
				}
			}catch(e){
				//do nothing
			}

			if (CanClose == "Y")
				ShowCloseProject();
			CloseWaitWindow();
		}

		function TestResultHandler(data)
		{
			bx_autotest_step++;
			try
			{
				var json_data=eval("(" +data+")");
				if (json_data)
				{
					if (json_data.STATUS)
						RefreshCheckList(json_data);
					else if (json_data.IN_PROGRESS == "Y" && bx_stoptest == false)
					{
						BX("percent").innerHTML = " &mdash; "+json_data.PERCENT+"%";
						AutoTest(json_data.TEST_ID,TestResultHandler,bx_autotest_step);
						return;
					}

				}

			}catch(e){
				//do nothing
			}

			if (bx_autotest == true)
			{
				bx_autotest_step = 0;
				bx_test_num++;
				if (bx_test_num<arAutoCheck.length && bx_stoptest == false)
				{
					AutoTest(arAutoCheck[bx_test_num],TestResultHandler);
					return;
				}
				if (CanClose == "Y")
					ShowCloseProject();
				else
					ShowResultInfo();
				loadButton("bx_start_button");
				start = "Y";
				bx_test_num = 0;
				bx_autotest_step = 0;
				bx_autotest = false;
				bx_stoptest = false;
				BX("current_test_name").innerHTML = "<?=GetMessage("CL_AUTOTEST_DONE");?>";
				BX("percent").innerHTML = "";
				CloseWaitWindow();
				return;
			}
			if (CanClose == "Y")
				ShowCloseProject();
			CloseWaitWindow();
		}

		function ScrollToProject()
		{
			var arNodePos = BX.pos(BX("project_check"));
			var animation = new BX.fx({
			start:0,
			finish :arNodePos.top-50,
			type:"accelerated",
			time:1,
			step:0.01,
			callback:function(value)
			{
				window.scroll(0,value)
			}

			});
			animation.start();
		}

		function StartAutoCheck(testID)
		{
			if (bx_autotest == true || bx_stoptest == true)
			{
				var buttonText =  BX.findChild(BX("bx_start_button"), {className:'checklist-button-cont'}, true, false);
				buttonText.innerHTML = "<?=GetMessage("CL_END_TEST_PROCCESS");?>";
				bx_stoptest = true;
				return;
			}
			ErrorSections = Array();
			bx_autotest = true;
			loadButton("bx_start_button");
			arTestResult = {"total":0,"success":0,"failed":0};
			BX("current_test_name").innerHTML = "<?=GetMessage("CL_TEST");?>: "+arAutoCheckName[bx_test_num];
			AutoTest(arAutoCheck[bx_test_num],TestResultHandler,bx_autotest_step);
		}

		function AutoTest(testID,callback,step)
		{
			var data = "ACTION=update&autotest=Y&TEST_ID="+testID+"&STEP="+step;
			for(var i=0; i<arAutoCheck.length; i++)
			{
				if(testID == arAutoCheck[i])
				{
					BX("current_test_name").innerHTML = "<?=GetMessage("CL_TEST");?>: "+arAutoCheckName[i];
					break;
				}
			}
			ShowWaitWindow();
			BX.ajax.post("/bitrix/admin/checklist.php"+"?lang=<?=LANG;?>",data,callback);
		}

		function SaveReport()
		{
			var error_message = "";

			if(BX("TESTER").value == "")
				error_message = "<?=GetMessage("CL_REQUIRE_NAME");?>";
			else
				if(BX("COMPANY_NAME").value == "")
					error_message = "<?=GetMessage("CL_REQUIRE_COMPANY");?>";
			if (error_message.length>0)
				alert(error_message)
			else
			{
				if (BX("report_comment").value == "<?=GetMessage("CL_ADD_COMMENT");?>")
					BX("report_comment").value = "";
				BX('bx_project_form').submit();
			}
		}

		function ShowCloseProject()
		{
			var bx_info = document.createElement('div');
			var result = "";
			result+="<b><?=GetMessage("CL_RESULT_TEST");?></b><br><br>";
			result+="<?=GetMessage("CL_TEST_TOTAL");?>: "+arMainStat.TOTAL;
			result+="<br><?=GetMessage("CL_TEST_CHECKED");?>: "+arMainStat.SUCCESS;
			result+= "<br><?=GetMessage("CL_TEST_FAILED");?>: "+arMainStat.FAILED;
			result+= "<br><?=GetMessage("CL_TEST_REQUIRE");?>: "+arMainStat.REQUIRE_CHECK;
			result+= "<br><br><b><?=GetMessage("CL_MANUAL_MINI_2");?></b>";
			bx_info.innerHTML = result;
			BX.addClass(bx_info,"checklist-manual checklist-detail-popup-result");
			var closePopup = BX.PopupWindowManager.create(
				"popup_manual",
				null,
				{
					autoHide : true,
					lightShadow : true,
					closeIcon:true,
					zIndex:10500
				}
			);
			closePopup.setContent(bx_info);
			closePopup.setButtons([
				new BX.PopupWindowButton({text : "<?=GetMessage("CL_GO_TO_REPORT_FORM");?>", className : "", events : { click : function(){
					ScrollToProject();
					BX("bx_project_form").style.display ="block";
					BX("project_check").checked = true;
					if (Dialog)
						Dialog.Close();
				closePopup.close();
				} } })

			]);
			closePopup.show();
			BX("project_check").disabled = null;
			BX("bx_project_checkbox").disabled = null;
		}

		function ShowResultInfo()
		{
			var bx_info = document.createElement('div');
			var result = "";
			result+="<b><?=GetMessage("CL_AUTOTEST_RESULT");?></b><br><br>";
			result+="<?=GetMessage("CL_TEST_TOTAL");?>: "+arTestResult.total;
			result+="<br><?=GetMessage("CL_TEST_CHECKED");?>: "+arTestResult.success;
			result+= "<br><?=GetMessage("CL_TEST_FAILED");?>: "+arTestResult.failed;
			if (start == "N")
				result+="<br><br><hr><?=GetMessage("CL_MANUAL_MINI");?>";
			bx_info.innerHTML = result;

			BX.addClass(bx_info,"checklist-manual checklist-detail-popup-result");
			var popupInfo = BX.PopupWindowManager.create(
				"popupInfo_"+Math.random(),
				null,
				{
					autoHide : false,
					lightShadow : true,
					closeIcon:true
				}
			);
			popupInfo.setContent(bx_info);
			popupInfo.setButtons([
				new BX.PopupWindowButton({text : "<?=GetMessage("CL_BEGIN_PROGECT_TEST");?>", className : "popup-window-button-create", events : { click : function(){
				if (ErrorSections.length>0)
				for(var i=0;i<ErrorSections.length;i++)
				{
					if (BX(ErrorSections[i]+"_name").parentNode)
						BX.addClass(BX(ErrorSections[i]+"_name").parentNode, 'testlist-open');
				}
				popupInfo.close();
				} } })

			]);
			popupInfo.show();
		}

		function ShowPopupManual()
		{
			var popupManual = BX.PopupWindowManager.create(
				"popup_manual",
				null,
				{
					autoHide : false,
					lightShadow : true,
					closeIcon:true
				}
			);
			popupManual.setContent("<div class=\"checklist-manual checklist-manual-mini\"><?=GetMessage("CL_MANUAL");?></div>");
			popupManual.setButtons([
				new BX.PopupWindowButton({text : "<?=GetMessage("CL_BEGIN_PROJECT_AUTOTEST");?>", className : "popup-window-button-create", events : { click : function(){StartAutoCheck(); popupManual.close();} } }),
				new BX.PopupWindowButton({text : "<?=GetMessage("CL_BEGIN_PROJECT_AUTOTEST_DELAY");?>", className : "", events : { click : function(){
				if (!BX.hasClass(BX("QDESIGN_name").parentNode,'testlist-open'))
					BX.addClass(BX("QDESIGN_name").parentNode, 'testlist-open');
				if(!BX.hasClass(BX("DESIGN_name").parentNode,'testlist-open'))
					BX.addClass(BX("DESIGN_name").parentNode, 'testlist-open');
				popupManual.close();
				} } })

			]);
			popupManual.show();
		}
	</script>
	<?ShowReportList();?>
	<?else:?>
		<?ShowReportList();?>
		<br><form id="bx_start_test" action="?lang=<?=LANG;?>" method="POST">
			<input type="hidden" name = "bx_start_test"  value="Y">
		</form>
		<a class="checklist-top-button checklist-pass-project-button" onclick="BX('bx_start_test').submit();">
			<span class="checklist-button-left-corn"></span><span class="checklist-button-cont checklist-pass-project-button-text">
				<?=GetMessage("CL_BEGIN");?></span><span class="checklist-button-right-corn"></span>
			<span class="checklist-loader"></span>
		</a>
		<div class="notes">
			<table cellspacing="0" cellpadding="0" border="0" class="notes" >
				<tr class="top">
					<td class="left"><div class="empty"></div></td>
					<td><div class="empty"></div></td>
					<td class="right"><div class="empty"></div></td>
				</tr>
				<tr>
					<td class="left"><div class="empty"></div></td>
					<td class="content">
						<?=GetMessage("CL_MANUAL_TEST");?>
					</td>
					<td class="right"><div class="empty"></div></td>
				</tr>
				<tr class="bottom">
					<td class="left"><div class="empty"></div></td>
					<td><div class="empty"></div></td>
					<td class="right"><div class="empty"></div></td>
				</tr>
			</table>
		</div>
	<?endif;?>
</div>
<?function ShowReportList()
{
	$dbReport = CCheckListResult::GetList(Array(),Array("REPORT"=>"Y"));
	while ($arReport = $dbReport->Fetch())
		$arReports[]=$arReport;?>
	<?if(count($arReports)>0):?>
		<div class="checklist-archive-rept">
			<?=GetMessage("CL_REPORT_ARCHIVE");?>
			<table class="checklist-archive-table" cellspacing="0">
				<tr class="checklist-archive-table-header">
					<td><?=GetMessage("CL_REPORT_DATE");?></td>
					<td><?=GetMessage("CL_REPORT_TABLE_TESTER");?></td>
					<td><?=GetMessage("CL_REPORT_TABLE_TOTAL");?></td>
					<td><?=GetMessage("CL_REPORT_TABLE_CHECKED");?></td>
					<td><?=GetMessage("CL_REPORT_TABLE_FAILED");?></td>
					<td>&nbsp;</td>
				</tr>
				<?foreach ($arReports as $arReport):?>
					<tr class="">
						<td><?=$arReport["DATE_CREATE"]?></td>
						<td><?=htmlspecialchars($arReport["TESTER"]);?></td>
						<td><?=$arReport["TOTAL"]?></td>
						<td><?=$arReport["SUCCESS"]?></td>
						<td><?=$arReport["FAILED"]?></td>
						<td><a class="checklist-archive-table-detail" href="/bitrix/admin/checklist_report.php?ID=<?=$arReport["ID"];?>&lang=<?=LANG;?>"><?=GetMessage("CL_REPORT_TABLE_DETAIL");?></a></td>
					</tr>
				<?endforeach;?>
			</table>
		</div>
	<?endif;?>
<?
}

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");?>
