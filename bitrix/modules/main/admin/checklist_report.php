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

$APPLICATION->AddHeadString('<link type="text/css" rel="stylesheet" href="/bitrix/themes/.default/check-list-style.css">');
CUtil::InitJSCore(Array('ajax','window',"popup"));
$APPLICATION->SetTitle(GetMessage("CL_TITLE_CHECKLIST"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");?>
<?
$arReportID = intval($_REQUEST["ID"]);
$checklist = new CCheckList($arReportID);
$arPoints = $checklist->GetPoints();
if($_REQUEST["ACTION"] == "INFO" && $_REQUEST["TEST_ID"] && $arPoints[$_REQUEST["TEST_ID"]]):?>
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
	$APPLICATION->RestartBuffer();?>
	<div id="test_detail_content" style="z-index: 1000; position: absolute; top:10px;min-width:700px;">
		<span class="bx-check-list-dit"><?=$arPosition.GetMessage("CL_FROM").$arTotal?></span>
        <div class="bx-core-admin-dialog-content">
            <div class="bx-core-admin-dialog-head" style="display: block;">
                <div class="bx-core-dialog-head-content">
                    <span id="tabs" class="tabs">
                        <a class="tab-container-selected" id="tab_cont_edit1"><span class="tab-left"><span class="tab-right"><?=GetMessage("CL_TAB_TEST");?></span></span></a>
                        <a class="tab-container" id="tab_cont_edit2" ><span class="tab-left"><span class="tab-right"><?=GetMessage("CL_TAB_DESC");?></span></span></a>
                    </span>
                </div>
            </div>				
			<div style="margin:3px;" class="edit-tab-inner" id="edit1" style="display:block;">
				<div class="checklist-popup-test">
					<span class="checklist-popup-name-test"><?=GetMessage("CL_TEST_NAME");?>:</span>
					<span class="checklist-popup-test-text"><?=$arPoints[$arTestID]["NAME"];?>(<?=$arTestID;?>)</span>
				</div>
				<div class="checklist-popup-test">
					<div class="checklist-popup-name-test"><?=GetMessage("CL_TEST_STATUS");?></div>
					<div class="checklist-popup-tes-status-wrap" id="checklist-popup-tes-status">
						<span class="checklist-popup-tes-status">
						<span id="bleft" style="width:10px;"></span><span id="bcenter" ><?=GetMessage("CL_".$arPoints[$arTestID]["STATE"]["STATUS"]."_STATUS");?></span><span id="bright" ></span>
						</span>                                            
					</div>
				</div> 
				<?if ($arPoints[$arTestID]["STATE"]["COMMENTS"]["SYSTEM"]["PREVIEW"]):?>
					<div class="checklist-popup-test">                                  
						<div class="checklist-popup-name-test"><?=GetMessage("CL_RESULT_TEST");?>:</div>				
							<div class="checklist-popup-test-text">                                                
							<span id="system_comment"><?=$arPoints[$arTestID]["STATE"]["COMMENTS"]["SYSTEM"]["PREVIEW"];?></span>
							<a id="show_detail_link" onclick="ShowDetailComment()" class="checklist-popup-test-link"><?=GetMessage("CL_MORE_DETAILS");?></a>
							<div style="display:none">
								<textarea readonly="readonly" id="detail_system_comment" class="checklist-system-textarea"><?=$arPoints[$arTestID]["STATE"]["COMMENTS"]["SYSTEM"]["DETAIL"]?></textarea>
							</div>
							</div>
					</div>
				<?endif;?>
				<div id="check_list_comments" class="checklist-popup-result-test-block">                                      
					<div class="checklist-popup-result-form">
						<div class="checklist-form-textar-block">
							<div class="checklist-form-textar-status"><?=GetMessage("CL_TESTER");?></div>
							<div class="checklist-dot-line"></div>
							<div id="performer_comment_area"  class="checklist-form-textar-comment" ><?=preg_replace("/\r\n|\r|\n/",'<br>', htmlspecialchars($arPoints[$arTestID]["STATE"]["COMMENTS"]["PERFOMER"]));?></div>
						</div>
						<div class="checklist-form-textar-block">
							<div class="checklist-form-textar-status"><?=GetMessage("CL_VENDOR");?></div>
							<div class="checklist-dot-line"></div>
							<div id="customer_comment_area"  class="checklist-form-textar-comment" ><?=preg_replace("/\r\n|\r|\n/",'<br>', htmlspecialchars($arPoints[$arTestID]["STATE"]["COMMENTS"]["CUSTOMER"]));?></div>
						</div>
					</div>
				</div>
			</div>
			<div style="display:none;margin:3px;" class="edit-tab-inner" id="edit2">
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
						<div class="checklist-popup-result-form checklist-popup-code">
						<?if($arPoints[$arTestID]["HOWTO"]):
							echo $arPoints[$arTestID]["HOWTO"];
						else:
							echo GetMessage("CL_EMPTY_DESC");
						endif;?>	
						</div>                                         
					</div>
				</div>
				<?if($arPoints[$arTestID]["AUTOTEST_DESC"]):?>
					<div class="checklist-popup-test">
						<div class="checklist-popup-name-test"><?=GetMessage("CL_NOW_AUTOTEST_WORK");?></div>
						<div class="checklist-popup-test-text"></div>
					</div>
				<?endif;?>
			</div>
        </div>    
    </div>
    <script>
	var arStatus = "<?=$arPoints[$arTestID]["STATE"]["STATUS"]?>";
	
	switch(arStatus)
	{
		case "W":
			style="waiting";
			break;
		case "A":
			style="successfully";
			break;
		case "F":
			style="fails";
			break;
		case "S":
			style="not-necessarily";
	}
	BX.addClass(BX("bleft"),"checklist-popup-tes-"+style+"-l");
	BX.addClass(BX("bcenter"),"checklist-popup-tes-"+style+"-c");
	BX.addClass(BX("bright"),"checklist-popup-tes-"+style+"-r");
	
	BX("check_list_comments").style.display = "block";
	var tabs = BX.findChildren(BX('tabs'), {tagName:'a'}, false);	
    var blocks=[BX('edit1'), BX('edit2')];
	   for(var i=0; i < tabs.length;i++){
            tabs[i].onclick=function(){popup_tabs(this, this.id)};
        }
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
					content: BX("detail_system_comment").parentNode.innerHTML,
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
    </script>
    <?die();?>
<?endif;?>


<?if (!$arReport = $checklist->GetReportInfo()):
	ShowError(GetMessage("CL_REPORT_NOT_FOUND"));
else:
	$arPoints = $checklist->GetPoints();
	$arSections = $checklist->GetSections();
	$arSectionStat = $checklist->GetSectionStat();
/////////////////////////////////////////////////////////
//////////////////////PARAMS_PREPARE/////////////////////
/////////////////////////////////////////////////////////	

	foreach ($arPoints as $key=>$arFields)
	{
		$arStates["POINTS"][] = Array(
			"NAME"=>$arFields["NAME"],
			"TEST_ID" => $key,
			"STATUS" => $arFields["STATE"]["STATUS"],
			"IS_REQUIRE" => $arFields["REQUIRE"],
			"AUTO" => $arFields["AUTO"],
			"COMMENTS_COUNT"=>count($arFields["STATE"]["COMMENTS"])
		);
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
		);
	}
	$arStates = CUtil::PhpToJsObject($arStates);
	if ($arReport["INFO"]["PICTURE"])
		$arPictureSrc = CFile::GetPath($arReport["INFO"]["PICTURE"]);

/////////////////////////////////////////////////////////
//////////////////////PREPARE_END/////////////////////
/////////////////////////////////////////////////////////		
?>
	<div class="checklist-wrapper checklist-result">
			<div class="checklist-top-info">
				<div class="checklist-top-text"><?=GetMessage("CL_REPORT_INFO");?></div>
				<div class="checklist-top-info-result">
					<table class="checklist-top-info-result-left" cellspacing="0">
						<tr>
							<td><span class="checklist-top-info-test checklist-testlist-grey"><?=GetMessage("CL_REPORT_DATE")?></span></td>
							<td><span class="checklist-top-info-test"><?=$arReport["INFO"]["DATE_CREATE"]?></span></td>
						</tr>
						<tr>
							<td><span class="checklist-top-info-test checklist-testlist-grey"><?=GetMessage("CL_TESTER")?></span></td>
							<td><span class="checklist-top-info-test right">
							<?if ($arPictureSrc):?>
								<img width="30px" src="<?=$arPictureSrc;?>"/>
							<?endif;?>
							<?=htmlspecialchars($arReport["INFO"]["COMPANY_NAME"]);?> (<?=htmlspecialchars($arReport["INFO"]["TESTER"]);?>)</span></td>
						</tr>
					</table>
					<div class="checklist-top-info-result-right">
						<div class="checklist-top-info-left-item"><?=GetMessage("CL_TEST_TOTAL")?>:<span class="checklist-top-info-left-item-qt"><?=$arSectionStat["TOTAL"]?></span></div>
						<div class="checklist-top-info-left-item"><?=GetMessage("CL_TEST_CHECKED")?>:<span class="checklist-test-successfully"><?=$arSectionStat["CHECK"]?></span></div>
						<div class="checklist-top-info-left-item"><?=GetMessage("CL_TEST_FAILED")?>:<span class="checklist-test-unsuccessful"><?=$arSectionStat["FAILED"]?></span></div>
						<div class="checklist-top-info-left-item"><?=GetMessage("CL_TEST_SKIP_REQUIRE")?>:<span class="checklist-top-info-left-item-qt"><?=$arSectionStat["REQUIRE_SKIP"]?></span></div>
					</div>
				</div>
			</div>
			<ul class="checklist-testlist">
			<?foreach($arReport["STRUCTURE"] as $rkey=>$rFields):$num = 1;?>
				<li class="checklist-testlist-level1">
					<div id="<?=$rkey?>_name" class="checklist-testlist-text"><?=$rFields["NAME"];?><span id="<?=$rkey;?>_stat" class="checklist-testlist-amount-test"></span>
						<span class="checklist-testlist-marker-list"></span>
					</div>
					<ul class="checklist-testlist-level2-wrap">
						<?foreach($rFields["POINTS"] as $pkey=>$pFields):?>
							<li id="<?=$pkey;?>" class="checklist-testlist-level3">
								<span class="checklist-testlist-level3-cont">
									<span class="checklist-testlist-level3-cont-nom"><?=$num++.". ";?></span>
									<span class="checklist-testlist-level3-cont-right">
										<span class="checklist-testlist-level3-cont-border" onclick="ShowPopupWindow('<?=$pkey;?>','<?=addslashes($pFields["NAME"])?>');">
												<?=$pFields["NAME"];?>
										</span>		
										<span id="comments_<?=$pkey;?>" class="checklist-testlist-comments" onclick='ShowPopupWindow("<?=$pkey;?>","<?=addslashes($pFields["NAME"]);?>");'><?=count($pFields["STATE"]["COMMENTS"]);?></span>
									</span>
								</span>						
								<span id="mark_<?=$pkey;?>"></span>
							</li>			
						<?endforeach;?>
					   <?foreach($rFields["CATEGORIES"] as $skey=>$sFields): $num = 1;?>					
								<li class="checklist-testlist-level2">
									<div class="checklist-testlist-text" id="<?=$skey?>_name">
										<?=$sFields["NAME"];?><span id="<?=$skey;?>_stat" class="checklist-testlist-amount-test"></span>
										<span class="checklist-testlist-marker-list"></span>
									</div>
									<ul class="checklist-testlist-level3-wrap">
										<?foreach($sFields["POINTS"] as $pkey=>$pFields):?>
										<li id="<?=$pkey;?>" class="checklist-testlist-level3">
											<span class="checklist-testlist-level3-cont">
												<span class="checklist-testlist-level3-cont-nom"><?=$num++.". ";?></span>
												<span class="checklist-testlist-level3-cont-right">
													<span class="checklist-testlist-level3-cont-border" onclick='ShowPopupWindow("<?=$pkey;?>","<?=addslashes($pFields["NAME"]);?>");'>
															<?=$pFields["NAME"];?>
													</span>		
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
			<a class="checklist-result-back" href="/bitrix/admin/checklist.php?lang=<?=LANG;?>"><?=GetMessage("CL_BACK_TO_CHECKLIST");?></a>
		</div>
<?endif;?>
    <script type="text/javascript">
	
		var arStates = eval(<?=$arStates;?>);
		var current = 0;
		var next = 0;
		var prev = 0;
		
	    function InitState()
		{
			var el = false;
			for (var i=0;i<arStates["SECTIONS"].length;i++)
			{
				el = arStates["SECTIONS"][i];			
				if (el.CHECKED == "Y")
					BX.addClass(BX(el.ID+"_name"),"checklist-testlist-green");
				BX(el.ID+"_stat").innerHTML = "(<span class=\"checklist-testlist-passed-test\">"+el.CHECK+"</span>/"+el.TOTAL+")";
			}
			for (var i=0;i<arStates["POINTS"].length;i++)
			{	
				ChangeStatus(arStates["POINTS"][i]);				
			}
		}
		InitState();		
		
		function ChangeStatus(element)
		{	
			BX.removeClass(BX(element.TEST_ID), BX(element.TEST_ID).className);	
			BX("mark_"+element.TEST_ID).className = "";		
			if (element.STATUS == "F")
			{
				BX.addClass(BX(element.TEST_ID),"checklist-testlist-red");
				BX.addClass(BX("mark_"+element.TEST_ID),"checklist-testlist-item-closed");				
			}else
			if (element.STATUS == "A")
			{
				BX.addClass(BX(element.TEST_ID),"checklist-testlist-green");
				BX.addClass(BX("mark_"+element.TEST_ID),"checklist-testlist-item-done");
			}else	
			if (element.STATUS == "W")
			{
				if (element.REQUIRE == "Y")
					BX.addClass(BX(element.TEST_ID),"checklist-testlist-black");			
				else				
					BX.addClass(BX(element.TEST_ID),"checklist-testlist-grey");				
			}else
			if (element.STATUS == "S")
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
		
        var checklist_div= document.getElementsByTagName('div');
        for(var i=0; i<checklist_div.length; i++){
            if(BX.hasClass(checklist_div[i], 'checklist-testlist-text')){
                BX.bind(checklist_div[i], "click", show_list)
            }
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
					content_url: "/bitrix/admin/checklist_report.php?ACTION=INFO&TEST_ID="+testID+"&ID=<?=$arReportID;?>&lang=<?=LANG;?>",
					icon: "head-block",	
					resizable: true,	
					draggable: true,	
					height: "530",	
					width: "700",
					buttons: ['<input id="prev" type="button" onclick="Move(\'prev\');"name="prev" value="<?=GetMessage("CL_PREV_TEST");?>"><input id="next" type="button" name="next" onclick="Move(\'next\');" value="<?=GetMessage("CL_NEXT_TEST");?>">']
				}
			);
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
		
		function Move(action)
		{
			var data = null;	
			if (action == "prev")
				current = prev;
			if (action == "next")
				current = next;		
			ShowWaitWindow();		
			BX.ajax.post(
				"/bitrix/admin/checklist_report.php?ACTION=INFO&TEST_ID="+arStates["POINTS"][current].TEST_ID+"&lang=<?=LANG;?>"+"&ID="+<?=$arReportID;?>,
				data,
				function(data)
				{
					Dialog.SetContent(data);
					testtitle = arStates["POINTS"][current].NAME+" - "+arStates["POINTS"][current].TEST_ID;
					if (arStates["POINTS"][current].IS_REQUIRE == "Y")
						testtitle = testtitle+" ("+"<?=GetMessage("CL_TEST_IS_REQUIRE");?>"+")";
					Dialog.SetTitle(testtitle);
					CloseWaitWindow();
				}
			);	
			
			ReCalc(current);
		}	
		
        function show_list(){
            BX.hasClass(this.parentNode,'testlist-open')?BX.removeClass(this.parentNode, 'testlist-open'):BX.addClass(this.parentNode, 'testlist-open');
        }  
    </script>
<?require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");?>