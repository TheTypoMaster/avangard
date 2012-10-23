<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!defined("BX_GADGET_DEFAULT"))
{
	define("BX_GADGET_DEFAULT", true);
	?>
	<script type="text/javascript">
	var updateURL = '<?=CUtil::JSEscape(htmlspecialcharsback($arResult['UPD_URL']))?>';
	var bxsessid = '<?=CUtil::JSEscape(bitrix_sessid())?>';
	var language_id = '<?=CUtil::JSEscape(LANGUAGE_ID)?>';	
	var langGDError1 = '<?=CUtil::JSEscape(GetMessage("CMDESKTOP_TDEF_ERR1"))?>';
	var langGDError2 = '<?=CUtil::JSEscape(GetMessage("CMDESKTOP_TDEF_ERR2"))?>';
	var langGDConfirm1 = '<?=CUtil::JSEscape(GetMessage("CMDESKTOP_TDEF_CONF"))?>';
	var langGDClearConfirm = '<?=CUtil::JSEscape(GetMessage("CMDESKTOP_TDEF_CLEAR_CONF"))?>';
	var langGDCancel = "<?echo CUtil::JSEscape(GetMessage("CMDESKTOP_TDEF_CANCEL"))?>";
	
	BX.message({
			langGDSettingsDialogTitle: '<?=CUtil::JSEscape(GetMessage("CMDESKTOP_TDEF_SETTINGS_DIALOG_TITLE"))?>',
			langGDSettingsAllDialogTitle: '<?=CUtil::JSEscape(GetMessage("CMDESKTOP_TDEF_SETTINGS_ALL_DIALOG_TITLE"))?>',
			langGDSettingsDialogRowTitle: '<?=CUtil::JSEscape(GetMessage("CMDESKTOP_TDEF_COLUMN_WIDTH"))?>',
			langGDGadgetSettingsDialogTitle: '<?=CUtil::JSEscape(GetMessage("CMDESKTOP_TDEF_GADGET_SETTINGS_DIALOG_TITLE"))?>'
	});
	</script>
	<?

	if ($arParams["MULTIPLE"] == "Y")
	{
		?>
		<script type="text/javascript">
		var desktopPage = '<?=CUtil::JSEscape(htmlspecialcharsback($arParams["DESKTOP_PAGE"]))?>';
		var desktopBackurl = '<?=CUtil::JSEscape(htmlspecialcharsback($GLOBALS["APPLICATION"]->GetCurPageParam("", array("dt_page"))))?>';
		</script>
		<?
	}

	if($arResult["PERMISSION"] > "R"):?>
		<script type="text/javascript" src="/bitrix/components/bitrix/desktop/script.js?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/bitrix/desktop/script.js');?>"></script>
		<script type="text/javascript" src="/bitrix/components/bitrix/desktop/templates/admin/script_admin.js?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/bitrix/desktop/templates/admin/script_admin.js');?>"></script>	
	<?endif?>
	<div id="antiselect" style="height:100%; width:100%; left: 0; top: 0; position: absolute; -moz-user-select: none !important; display: none; background-color:#FFFFFF; -moz-opacity: 0.01;"></div>
	<?
}

if($arResult["PERMISSION"] > "R"):

	$allGD = Array();
	foreach($arResult['ALL_GADGETS'] as $gd)
	{
		$allGD[] = Array(
			'ID' => $gd["ID"],
			'TEXT' =>
				'<div style="text-align: left;">'.($gd['ICON1']?'<img src="'.($gd['ICON']).'" align="left">':'').
				'<b>'.(htmlspecialchars($gd['NAME'])).'</b><br>'.(htmlspecialchars($gd['DESCRIPTION'])).'</div>',
			);
	}

	$arSettingsMenu = 	array(
		array(
			"TEXT" => GetMessage("CMDESKTOP_TDEF_DESKTOP_ADD"),
			"TITLE" => GetMessage("CMDESKTOP_TDEF_DESKTOP_ADD"),
			"ONCLICK"=>"__ShowDesktopAddDialog()"
		),
		array(
			"TEXT" => GetMessage("CMDESKTOP_TDEF_DESKTOP_SETTINGS"),
			"TITLE" => GetMessage("CMDESKTOP_TDEF_DESKTOP_SETTINGS"),
			"ONCLICK"=>"__ShowDesktopSettingsDialog()"
		),
		array(
			"SEPARATOR" => "Y"
		),
		array(
			"TEXT" => GetMessage("CMDESKTOP_TDEF_DESKTOP_ALL_SETTINGS"),
			"TITLE" => GetMessage("CMDESKTOP_TDEF_DESKTOP_ALL_SETTINGS"),
			"ONCLICK"=>"__ShowDesktopAllSettingsDialog()"
		),
		array(
			"SEPARATOR" => "Y"
		),
		array(
			"TEXT" => GetMessage("CMDESKTOP_TDEF_CLEAR"),
			"TITLE" => GetMessage("CMDESKTOP_TDEF_CLEAR"),
			"ONCLICK"=>"getGadgetHolder('".AddSlashes($arResult["ID"])."').ClearUserSettingsConfirm()"
		)				
	);

	if($arResult["PERMISSION"]>"W")
		$arSettingsMenu[] = 
			array(
				"TEXT" => GetMessage("CMDESKTOP_TDEF_SET"),
				"TITLE" => GetMessage("CMDESKTOP_TDEF_SET"),
				"ONCLICK"=>"getGadgetHolder('".AddSlashes($arResult["ID"])."').SetForAll('')"
			);
	?>
	<script type="text/javascript">
		arGDGroups = <?=CUtil::PhpToJSObject($arResult["GROUPS"])?>;
		new BX.AdminGadget('<?=$arResult["ID"]?>', <?=CUtil::PhpToJSObject($allGD)?>, <?=CUtil::PhpToJSObject($arSettingsMenu)?>);
	</script>


	<div class="bx-gadgets-header">
		<div class="bx-gadgets-desktops-tabs"><span class="bx-gadgets-desktops-text"><?=GetMessage("CMDESKTOP_TDEF_DESKTOPS")?></span><span class="bx-gadgets-desktops-buttons"><?
			foreach($arResult["DESKTOPS"] as $key => $arDesktop):
				?><a class="bx-gadgets-desktops<?if (intval($key) == $arParams["DESKTOP_PAGE"]):?> bx-gadgets-desktops-active<?endif;?>" href="<?=$GLOBALS["APPLICATION"]->GetCurPageParam("dt_page=".$key, array("dt_page"))?>"><span class="bx-gadgets-desktops-left"></span><span class="bx-gadgets-desktops-center"><?=(strlen($arDesktop["NAME"])>0?htmlspecialchars($arDesktop["NAME"]):GetMessage("CMDESKTOP_TDEF_DESKTOP").($key+1))?></span><span class="bx-gadgets-desktops-right"></span><span class="bx-gadgets-left-line"></span><span class="bx-gadgets-right-line"></span></a><?
			endforeach;
			?></span></div>
		<div class="bx-gadgets-buttons">
			<a id="bg_gd_gadget_add" class="bx-gadgets-button" href="javascript:void(0)" onclick="getGadgetHolder('<?=AddSlashes($arResult["ID"])?>').ShowAddGDMenu(this);"><span class="bx-gadgets-button-left"></span><span class="bx-gadgets-button-center"><?=GetMessage("CMDESKTOP_TDEF_ADD_BUTTON")?></span><span class="bx-gadgets-button-right"></span></a>
			<a id="bx_gd_desktop_settings" class="bx-gadgets-button" href="javascript:void(0)" onclick="getGadgetHolder('<?=AddSlashes($arResult["ID"])?>').ShowSettingsMenu(this);"><span class="bx-gadgets-button-left"></span><span class="bx-gadgets-button-center"><?=GetMessage("CMDESKTOP_TDEF_DESKTOP_SETTINGS_BUTTON")?></span><span class="bx-gadgets-button-right"></span></a>
		</div>
	</div>
	<?
endif;
?>
<form action="<?=POST_FORM_ACTION_URI?>" method="POST" id="GDHolderForm_<?=$arResult["ID"]?>">
<?=bitrix_sessid_post()?>
<input type="hidden" name="holderid" value="<?=$arResult["ID"]?>">
<input type="hidden" name="gid" value="0">
<input type="hidden" name="action" value="">
</form>

<div  style="padding: 0 10px;">
<table class="gadgetholder" cellspacing="0" cellpadding="0" width="96%" id="GDHolder_<?=$arResult["ID"]?>">
  <tbody>
    <tr>
    <?for($i=0; $i<$arResult["COLS"]; $i++):?>
    	<?if($i==0):?>
    	<td class="gd-page-column<?=$i?>" valign="top" width="<?=$arResult["COLUMN_WIDTH"][$i]?>" id="s0">
    	<?elseif($i==$arResult["COLS"]-1):?>
	 	  <td width="10">
	        <div style="WIDTH: 10px"></div>
	        <br />
	      </td>
	      <td class="gd-page-column<?=$i?>" valign="top" width="<?=$arResult["COLUMN_WIDTH"][$i]?>" id="s2">
    	<?else:?>
	 	  <td width="10">
	        <div style="WIDTH: 10px"></div>
	        <br />
	      </td>
	      <td class="gd-page-column<?=$i?>" valign="top"  width="<?=$arResult["COLUMN_WIDTH"][$i]?>" id="s1">
 		<?endif?>
		<?foreach($arResult["GADGETS"][$i] as $arGadget):
			$bChangable = true;
			if (
				!$GLOBALS["USER"]->IsAdmin() 
				&& array_key_exists("GADGETS_FIXED", $arParams) 
				&& is_array($arParams["GADGETS_FIXED"]) 
				&& in_array($arGadget["GADGET_ID"], $arParams["GADGETS_FIXED"])
				&& array_key_exists("CAN_BE_FIXED", $arGadget)
				&& $arGadget["CAN_BE_FIXED"]
			)
				$bChangable = false;
			?>
			<div class="bx-gadgets<?=(strlen($arGadget["TITLE_ICON_CLASS"]) > 0 ? " ".$arGadget["TITLE_ICON_CLASS"] : "")?>" id="t<?=$arGadget["ID"]?>">
				<div class="bx-gadgets-top-wrap" style="cursor:move;" onmousedown="return getGadgetHolder('<?=AddSlashes($arResult["ID"])?>').DragStart('<?=$arGadget["ID"]?>', event)">
					<div class="bx-gadgets-top-left"></div>
					<div class="bx-gadgets-top-right"></div>
					<div class="bx-gadgets-top-center">
						<div class="bx-gadgets-top-title"><?=$arGadget["TITLE"]?></div>
						<div class="bx-gadgets-top-button">
							<?
							if ($bChangable):
								?>
								<a class="bx-gadgets-config<?=($arGadget["NOPARAMS"]?' bx-gadgets-noparams':'')?>" href="javascript:void(0)" onclick="return getAdminGadgetHolder('<?=AddSlashes($arResult["ID"])?>').ShowSettings('<?=$arGadget["ID"]?>', '<?=CUtil::JSEscape($arGadget["TITLE"])?>');" title="<?=GetMessage("CMDESKTOP_TDEF_SETTINGS")?>"></a>
								<a class="bx-gadgets-config-close" href="javascript:void(0)" onclick="return getGadgetHolder('<?=AddSlashes($arResult["ID"])?>').Delete('<?=$arGadget["ID"]?>');" title="<?=GetMessage("CMDESKTOP_TDEF_DELETE")?>"></a>
								<?
							endif;
							?>
						</div>
					</div>
				</div>
				<div class="bx-gadgets-content">
					<?=$arGadget["CONTENT"]?>
				</div>
				<div class="bx-gadgets-footer-left">
					<div class="bx-gadgets-footer-right">
						<div class="bx-gadgets-footer-center">
						</div>
					</div>
				</div>
			</div>
			<div style="display:none; border:1px #404040 dashed; margin-bottom:8px;" id="d<?=$arGadget["ID"]?>"></div>
 		<?endforeach;?>
 	  </td>
    <?endfor;?>
    </tr>
  </tbody>
</table>
</div>