<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/***********************************************************************
Component for empty form representation for filling

This universal component is for a blog post edit. This is a standard component, it is included to module distributive

example of using:

$APPLICATION->IncludeFile("blog/blog/post_edit.php", array(
));

Parameters:


***********************************************************************/
$GLOBALS["APPLICATION"]->SetTemplateCSS("blog/blog.css");
$BLOG_WIDTH = intval($BLOG_WIDTH);
if ($BLOG_WIDTH < 100) 
	$BLOG_WIDTH = "100%";

global $USER, $APPLICATION, $strError, $DB;
IncludeTemplateLangFile(__FILE__);

$is404 = ($is404=='N') ? false: true;

if (CModule::IncludeModule("blog"))
{
	$USER_ID = intval($USER->GetID());
	$ID = intval($ID);
	if (!$ID)
		$ID = intval($_POST['ID']);
	$BLOG_ID = intval($BLOG_ID);
	if (!$BLOG_ID)
		$BLOG_ID = intval($_POST['BLOG_ID']);
	
	$arr = CBlogSitePath::GetBySiteID(SITE_ID);
	$sBlogPath = $arr['PATH'];
	
	if ($BLOG_ID)
		$arBlog = CBlog::GetByID($BLOG_ID);
	else
	{
		$res = CBlog::GetList(array(),array("URL" => $OWNER));
		$arBlog = $res->Fetch();
		$BLOG_ID = intval($arBlog['ID']);
	}
	
	if ($arBlog)
	{
		if(IntVal($ID)>0 && $arPost=CBlogPost::GetByID($ID))// Существующий пост
		{
			$APPLICATION->SetTitle(str_replace("#BLOG#", htmlspecialchars($arBlog["NAME"]), "".GetMessage("BLOG_POST_EDIT").""));
			$perms = CBlogPost::GetBlogUserPostPerms($ID, $USER_ID);
		}
		else
		{
			$ID = 0;
			$APPLICATION->SetTitle(str_replace("#BLOG#", htmlspecialchars($arBlog["NAME"]), "".GetMessage("BLOG_NEW_MESSAGE").""));
			$perms = CBlog::GetBlogUserPostPerms($BLOG_ID, $USER_ID);
		}

		if ($perms >= BLOG_PERMS_WRITE && (intval($arPost['ID'])==0 || $arPost['BLOG_ID']==$BLOG_ID)) // Вся эта хитрая комбинация нужна для того, чтобы не возникало желания написать ID поста чужого блога 
		{
			###### Form ####
			$image_form = '
				<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
				<html>
				<head>
				<title>'.GetMessage("BLOG_P_IMAGE_UPLOAD").'</title>
				</head>
				<form action="'.($sBlogPath).'/post_edit.php" method=POST enctype="multipart/form-data">
				'.bitrix_sessid_post().'
				<input type=hidden name=ID value="'.$ID.'">
				<input type=hidden name=BLOG_ID value="'.$BLOG_ID.'">
				<style type=text/css>
					td.tableborder, table.tableborder {background-color:#8FB0D2;}
					table.tablehead, td.tablehead {background-color:#F1F5FA;}
					table.tablebody, td.tablebody {background-color:#FFFFFF;}
					.tableheadtext, .tablebodylink {font-family: Verdana,Arial,Hevetica,sans-serif; font-size:12px;}
					.tableheadtext {color:#456A74}
					H1, H2, H3, H4 {font-family: Verdana, Arial, Helvetica, sans-serif; color:#3A84C4; font-size:13px; font-weight:bold; line-height: 16px; margin-bottom: 1px;}
					input.inputradio, input.inputfile, input.inputbutton, input.inputbodybutton {font-family:Verdana,Arial,Helvetica; font-size:11px;}
					.errortext, .oktext, .notetext {font-family:Verdana,Arial,Hevetica,sans-serif; font-size:13px; font-weight:bold;}
					.errortext {color:red;}
				</style>
				<h1>'.GetMessage("BLOG_P_IMAGE_UPLOAD").'</h1>
				<br>
				<table border=0 cellspacing=1 cellpadding=3 class=tableborder>
				<tr>
					<td class=tablehead valign=top align=right nowrap>
					<font class=tableheadtext>
					<b>'.GetMessage("BLOG_IMAGE").'</b></td>
					<td class=tablebody>';
			$image_form .= CFile::InputFile("FILE_ID", 20, 0);
			$image_form .= '</td>
				</tr>
				<!--
				<tr>
					<td class=tablehead valign=top align=right nowrap>
					<font class=tableheadtext>
					<b>'.GetMessage("BLOG_P_TITLE").'</b></td>
					<td class=tablebody><input type=text size=20 name=IMAGE_TITLE value="'.htmlspecialchars($_POST['IMAGE_TITLE']).'"></td>
				</tr>
				!-->
				</table>
			<br>
			<input type=submit value="'.GetMessage("BLOG_P_DO_UPLOAD").'" name=do_upload class=inputbutton>
			<input type=button value="'.GetMessage("BLOG_P_CANCEL").'" onclick=self.close() class=inputbutton>
			</form>
			</html>
			';
			##################

			if ($_GET['image_upload'] || $_POST['do_upload']) // Загрузка картинок
			{
				$APPLICATION->RestartBuffer();
				header("Pragma: no-cache");
					
				if ($_POST['do_upload'] && $_FILES['FILE_ID']['size'] > 0) // Заслали картинку
				{
					$arFields = array(
						"BLOG_ID"	=> $BLOG_ID,
						"POST_ID"	=> $ID,
						"USER_ID"	=> $USER_ID,
						"=TIMESTAMP_X"	=> $DB->GetNowFunction(),
						"TITLE"		=> $_POST['IMAGE_TITLE'],
						"IMAGE_SIZE"	=> $_FILES['FILE_ID']['size']
					);
					$arImage=array_merge(
						$_FILES['FILE_ID'],
						array(
							"MODULE_ID" => "blog",
							"del" => "Y"
						)
					);
					$arFields['FILE_ID']	= $arImage;

					if ($imgID = CBlogImage::Add($arFields))
					{
						$aImg = CBlogImage::GetByID($imgID);
					?>
					<script>
						my_html = '<? 
							$file = CFile::ShowImage($aImg['FILE_ID'], 100, 100, "border=0 style=cursor:pointer onclick=\"doInsert('[IMG ID=".$aImg['ID']."]','',false)\" title='".GetMessage("BLOG_P_INSERT")."'");
							$file = str_replace("'","\'",$file);
							$file = str_replace("\r"," ",$file);
							$file = str_replace("\n"," ",$file);
							print $file;
							?>' +
							'<br><input class=inputtext name=IMAGE_ID_title[<?=$aImg['ID']?>] value="<?=htmlspecialchars($aImg['TITLE'])?>" style="width:100px">' +
							'<br><input type=checkbox name=IMAGE_ID_del["<?=$aImg['ID']?>] id=img_del_<?=$aImg['ID']?>> <label for=img_del_<?=$aImg['ID']?> class=blogtext><?=GetMessage("BLOG_DELETE")?></label>';
							
						if (!opener.document.getElementById('img_TABLE'))
						{
							main_table = opener.document.getElementById("main_table");
							tr_text = opener.document.getElementById("tr_TEXT");
							
							oTR = main_table.insertRow(tr_text.rowIndex + 1);

							oTD = oTR.insertCell(-1);
							oTD.className = "blogtablehead";
							oTD.vAlign = "top";
							oTD.align = "right";
							oTD.innerHTML = '<font class=\"blogheadtext\"><b><?=GetMessage("BLOG_P_IMAGES")?></b></font>';
							
							oTD = oTR.insertCell(-1);
							oTD.className = "blogtablebody";
							oTD.innerHTML = '<table cellspacing=0 cellpadding=4 border=0 id=img_TABLE></table>';
						}
					
						imgTable = opener.document.getElementById('img_TABLE');

						if (imgTable.rows.length > 0)
						{
							oRow = imgTable.rows[imgTable.rows.length - 1];
							if (oRow.cells.length >= 4)
								oRow = imgTable.insertRow(-1);
						}
						else
							oRow = imgTable.insertRow(-1);
						
						oRow.vAlign = 'top';

						oCell = oRow.insertCell(-1);
						oCell.vAlign = 'top';
						oCell.innerHTML = my_html;

						opener.doInsert('[IMG ID=<?=$aImg['ID']?>]','',false);
						
						self.close();
					</script>
					<?
					}
					else
					{
						if ($ex = $APPLICATION->GetException())
							$strError = $ex->GetString()."<br>";
						echo ShowError($strError);
					}
				}

				
				print $image_form;
				die();
			} //////////////////////////////// дальше пойдёт работа с постом
			
			if (($_POST['apply'] || $_POST['save']) && check_bitrix_sessid()) // Сохраняем если нажали соотв. кнопку
			{
				$TRACKBACK = trim($_POST['TRACKBACK']);
				InitBVar($_POST['ENABLE_TRACKBACK']);
				
				if ($_POST['CATEGORY_ID']=='ADD')
				{
					$CATEGORY_ID = CBlogCategory::Add(array("BLOG_ID"=>$BLOG_ID,"NAME"=>$_POST['NEW_CATEGORY']));
					BXClearCache(True, "/".SITE_ID."/blog/".$arBlog['URL']."/category/");
				}
				else
					$CATEGORY_ID = intval($_POST['CATEGORY_ID']);

				if (strlen($_POST['DATE_PUBLISH'])==0)
					$DATE_PUBLISH = ConvertTimeStamp(time(),"FULL");
				else
					$DATE_PUBLISH = $_POST['DATE_PUBLISH'];


				$arFields=array(
					'TITLE'			=> $_POST['POST_TITLE'],
					'DETAIL_TEXT'		=> $_POST['POST_MESSAGE'],
					'DATE_PUBLISH'		=> $DATE_PUBLISH,
					'PUBLISH_STATUS'	=> $_POST['PUBLISH_STATUS'],
					'ENABLE_TRACKBACK'	=> $_POST['ENABLE_TRACKBACK'],
					'CATEGORY_ID'		=> $CATEGORY_ID,
				);

				if ($_POST['blog_perms']==1)
				{
					if ($_POST['perms_p'][1] > BLOG_PERMS_READ)
						$_POST['perms_p'][1] = BLOG_PERMS_READ;
					if ($_POST['perms_c'][1] > BLOG_PERMS_READ)
						$_POST['perms_c'][1] = BLOG_PERMS_READ;

					$arFields['PERMS_POST'] = $_POST['perms_p'];
					$arFields['PERMS_COMMENT'] = $_POST['perms_c'];
				}
				else
				{
					$arFields['PERMS_POST'] = array();
					$arFields['PERMS_COMMENT'] = array();
				}

				while (is_array($_POST['IMAGE_ID_title']) && list($imgID, $imgTitle)=each($_POST['IMAGE_ID_title']))
				{
					$aImg = CBlogImage::GetByID($imgID);
					if ($aImg['BLOG_ID']==$BLOG_ID && $aImg['POST_ID']==$ID)
					{
						if ($_POST['IMAGE_ID_del'][$imgID])
						{
							CBlogImage::Delete($imgID);
							$arFields['DETAIL_TEXT'] = str_replace("[IMG ID=$imgID]","",$arFields['DETAIL_TEXT']);
						}
						else
							CBlogImage::Update($imgID, array("TITLE"=>$imgTitle));
					}
				}
					
				if ($ID > 0) // Проверяем: новая запись или изменение старой
					$newID = CBlogPost::Update($ID, $arFields);
				else
				{

					$arFields['=DATE_CREATE'] = $DB->GetNowFunction();
					$arFields['AUTHOR_ID'] = $USER_ID;
					$arFields['BLOG_ID'] = $BLOG_ID;
					
					$newID = CBlogPost::Add($arFields);

					if ($newID && $arBlog['EMAIL_NOTIFY']=='Y' && $USER_ID != $arBlog['OWNER_ID']) // Отправим уведомление на мейл
					{
						$BlogUser = CBlogUser::GetByID($USER_ID, BLOG_BY_USER_ID);
						$res = CUser::GetByID($arBlog['OWNER_ID']);
						$arOwner = $res->Fetch();
						$dbUser = CUser::GetByID($USER_ID);
						$arUser = $dbUser->Fetch();
						$AuthorName = CBlogUser::GetUserName($BlogUser["ALIAS"], $arUser["NAME"], $arUser["LAST_NAME"], $arUser["LOGIN"]); 
						
						CEvent::Send(
							"NEW_BLOG_MESSAGE",
							SITE_ID,
							array(
								"BLOG_ID"	=> $BLOG_ID,
								"BLOG_NAME"	=> $arBlog['NAME'],
								"BLOG_URL"	=> $arBlog['URL'],
								"MESSAGE_TITLE" => $_POST['POST_TITLE'],
								"MESSAGE_TEXT" => $_POST['POST_MESSAGE'],
								"MESSAGE_DATE" => $DATE_PUBLISH,
								"MESSAGE_PATH" => "http://".(COption::GetOptionString("main","server_name").$sBlogPath."/".($is404 ? $arBlog['URL']."/".$newID.".php" : "post.php?blog=".$arBlog['URL']."&post_id=".$newID)),
								"AUTHOR"	 => $AuthorName,
								"EMAIL_FROM"	 => COption::GetOptionString("main","email_from", "nobody@nobody.com"),
								"EMAIL_TO"	 => $arOwner['EMAIL']
							)
						);
					}
				}
			
				if ($newID > 0) // Запись сохранена
				{
					$DB->Query("UPDATE b_blog_image SET POST_ID='$newID' WHERE BLOG_ID=$BLOG_ID AND POST_ID=0", true);
					
					if (strlen($TRACKBACK)>0)
					{
						$arPingUrls = explode("\n",$TRACKBACK);
						CBlogTrackback::SendPing($newID, $arPingUrls);
					}
					
					BXClearCache(True, "/".SITE_ID."/blog/".$arBlog['URL']."/first_page/");
					BXClearCache(True, "/".SITE_ID."/blog/".$arBlog['URL']."/calendar/");
					BXClearCache(True, "/".SITE_ID."/blog/last_messages/");
					BXClearCache(True, "/".SITE_ID."/blog/groups/".$arBlog['GROUP_ID']."/");
					BXClearCache(True, "/".SITE_ID."/blog/".$arBlog['URL']."/trackback/".$ID."/");
					BXClearCache(True, "/".SITE_ID."/blog/".$arBlog['URL']."/comment/".$ID."/");
					BXClearCache(True, "/".SITE_ID."/blog/".$arBlog['URL']."/rss_out/");
					if ($ID > 0)
						BXClearCache(True, "/".SITE_ID."/blog/".$arBlog['URL']."/post/".$ID."/");

					if ($is404)
					{
						if (strlen($_POST['save'])>0)
							LocalRedirect($sBlogPath."/".$arBlog['URL']);
						else
							LocalRedirect($sBlogPath."/".$arBlog['URL']."/post_edit.php?ID=$newID");
					}
					else
					{
						if (strlen($_POST['save'])>0)
							LocalRedirect($sBlogPath."/blog.php?blog=".$arBlog['URL']);
						else
							LocalRedirect($sBlogPath."/post_edit.php?blog=".$arBlog['URL']."&post_id=$newID");
					}
				}
				else
				{
					if ($ex = $APPLICATION->GetException())
						$strError = $ex->GetString()."<br>";
					else
						$strError = "Error saving data to database.<br>";
				}
			}
			elseif($_POST['reset'])
			{
				if ($is404)
					LocalRedirect($sBlogPath."/".$arBlog['URL']);
				else
					LocalRedirect($sBlogPath."/blog.php?blog=".$arBlog['URL']);
				
			}

			######### Читаем значения переменных
			if ($ID > 0 && strlen($strError)==0) // Edit post
			{
	//			print "<pre>"; print_r($arPost); print "</pre>";
				$POST_TITLE = $arPost['TITLE'];
				$POST_MESSAGE = $arPost['DETAIL_TEXT'];
				$PUBLISH_STATUS = $arPost['PUBLISH_STATUS'];
				$ENABLE_TRACKBACK = $arPost['ENABLE_TRACKBACK'];
				$ATTACH_IMG = $arPost['ATTACH_IMG'];
				$DATE_PUBLISH = $arPost['DATE_PUBLISH'];
				$CATEGORY_ID = $arPost['CATEGORY_ID'];

				$res=CBlogUserGroupPerms::GetList(array("ID"=>"DESC"),array("BLOG_ID"=>$BLOG_ID,"POST_ID"=>$ID));
				while($arPerms = $res->Fetch())
				{
					if ($arPerms['AUTOSET']=='N')
						$bExtendedPerms = true;
					if ($arPerms['PERMS_TYPE']=='P')
						$arUGperms_p[$arPerms['USER_GROUP_ID']] = $arPerms['PERMS'];
					elseif ($arPerms['PERMS_TYPE']=='C')
						$arUGperms_c[$arPerms['USER_GROUP_ID']] = $arPerms['PERMS'];
				}
			}
			else // случай ошибки или дефолтные значения
			{
				$POST_TITLE = $_POST['POST_TITLE'];
				$POST_MESSAGE = $_POST['POST_MESSAGE'];
				$PUBLISH_STATUS = $_POST['PUBLISH_STATUS'];
				$ENABLE_TRACKBACK = $_POST['ENABLE_TRACKBACK'];
				$DATE_PUBLISH = $_POST['DATE_PUBLISH'] ? $_POST['DATE_PUBLISH'] : ConvertTimeStamp(time(),"FULL");
				
				if ($_POST['apply'] || $_POST['save'])
				{
					$arUGperms_p = $_POST['perms_p'];
					$arUGperms_c = $_POST['perms_c'];
					$bExtendedPerms = ($_POST['blog_perms']==1?true:false);
				}
				else
				{
					$res=CBlogUserGroupPerms::GetList(array("ID"=>"DESC"),array("BLOG_ID"=>$BLOG_ID,"POST_ID"=>0));
					while($arPerms = $res->Fetch())
					{
						if ($arPerms['PERMS_TYPE']=='P')
							$arUGperms_p[$arPerms['USER_GROUP_ID']] = $arPerms['PERMS'];
						elseif ($arPerms['PERMS_TYPE']=='C')
							$arUGperms_c[$arPerms['USER_GROUP_ID']] = $arPerms['PERMS'];
					}
				}
			}

			##############################################################
			# Начало вывода формы
			##############################################################
			if (strlen($strError) > 0)
				echo ShowError($strError);

			// *************** Подключаем JS *****************************************************
			// Тэги <script> в подключаемом скрипте - с тем, чтобы работала подсветка синтаксиса js
			if ($strJSPath = $APPLICATION->GetTemplatePath("blog/blog/blog_js.php"))
				include($_SERVER["DOCUMENT_ROOT"].$strJSPath);
			?>
<style type=text/css>
.blogButton
{
	font-size: 8.5pt;
	font-family: Arial, Verdana, helvetica, sans-serif;
	border-style:none;
}

.blogButton:hover
{
	background-color:#FFFFEE;
}
</style>
			<form action="<?=$sBlogPath ?>/post_edit.php" name=REPLIER method=post enctype="multipart/form-data">
			<?=bitrix_sessid_post();?>
			<input type=hidden name=ID value="<?=$ID?>">
			<input type=hidden name=BLOG_ID value="<?=$BLOG_ID?>">
			<input type=hidden name=NEW_CATEGORY>
				<table border=0 cellspacing=1 cellpadding=3 class="blogtableborder" width=700 id=main_table>
				<tr>
					<td colspan=2 class="blogtablebody" style="padding:0px;">
					<table cellpadding=2 cellspacing=0 border=0>
						<tr valign=middle style="background-image:url(/bitrix/templates/.default/blog/images/message_edit/toolbarbg.gif)">
							<td><select class=inputselect name='ffont' id=select_font onchange="alterfont(this.options[this.selectedIndex].value, 'FONT')" >
									<option value='0'><?echo GetMessage("FPF_FONT")?></option>
									<option value='Arial' style='font-family:Arial'>Arial</option>
									<option value='Times' style='font-family:Times'>Times</option>
									<option value='Courier' style='font-family:Courier'>Courier</option>
									<option value='Impact' style='font-family:Impact'>Impact</option>
									<option value='Geneva' style='font-family:Geneva'>Geneva</option>
									<option value='Optima' style='font-family:Optima'>Optima</option>
									<option value='Verdana' style='font-family:Verdana'>Verdana</option>
							</select></td>
							<td nowrap><a id=FontColor
									class=blogButton href='javascript:ColorPicker()'><img class=blogButton src="/bitrix/templates/.default/blog/images/message_edit/font_color.gif" width=20 height=20 title="<?echo GetMessage("FPF_IMAGE")?>"></a><a class=blogButton href='javascript:simpletag("B")'><img class=blogButton src="/bitrix/templates/.default/blog/images/message_edit/bold.gif" width=20 height=20 title="<?echo GetMessage("FPF_BOLD")?>"></a><a class=blogButton href='javascript:simpletag("I")'><img class=blogButton src="/bitrix/templates/.default/blog/images/message_edit/italic.gif" width=20 height=20 title="<?echo GetMessage("FPF_ITALIC")?>"
								></a><a class=blogButton href='javascript:simpletag("U")'><img class=blogButton src="/bitrix/templates/.default/blog/images/message_edit/under.gif" width=20 height=20 title="<?echo GetMessage("FPF_UNDER")?>"
								></a><a class=blogButton href='javascript:tag_url()'><img class=blogButton src="/bitrix/templates/.default/blog/images/message_edit/link.gif" width=20 height=20 title="<?echo GetMessage("FPF_HYPERLINK")?>"
								></a><a class=blogButton href='javascript:tag_image()'><img class=blogButton src="/bitrix/templates/.default/blog/images/message_edit/image_link.gif" width=20 height=20 title="<?=GetMessage("BLOG_P_IMAGE_LINK")?>"
								></a><a class=blogButton href='javascript:ShowImageUpload()'><img class=blogButton src="/bitrix/templates/.default/blog/images/message_edit/image.gif" width=20 height=20 title="<?=GetMessage("BLOG_P_DO_UPLOAD")?>"
								></a><a class=blogButton href='javascript:quoteMessage()'><img class=blogButton src="/bitrix/templates/.default/blog/images/message_edit/quote.gif" width=20 height=20 title="<?echo GetMessage("FPF_QUOTE")?>"
								></a><a class=blogButton href='javascript:simpletag("CODE")'><img class=blogButton src="/bitrix/templates/.default/blog/images/message_edit/code.gif" width=20 height=20 title="<?echo GetMessage("FPF_CODE")?>"
								></a><a class=blogButton href='javascript:tag_list()'><img class=blogButton src="/bitrix/templates/.default/blog/images/message_edit/list.gif" width=20 height=20 title="<?echo GetMessage("FPF_LIST")?>"
								></a><a class=blogButton href='javascript:void(0)' onclick='doInsert("[CUT]", "", false)'><img class=blogButton src="/bitrix/templates/.default/blog/images/message_edit/cut.gif" width=20 height=20 title="<?echo GetMessage("FPF_CUT")?>"
								></a></td>
							<td width=100% align=right nowrap><a id=close_all style=visibility:hidden class=blogButton href='javascript:closeall()' title='<?=GetMessage("FPF_CLOSE_OPENED_TAGS")?>'><?=GetMessage("FPF_CLOSE_ALL_TAGS")?></a></td>
						</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td width=1% class="blogtablehead" valign=top align=right nowrap>
					<font class="blogheadtext">
					<font color="#FF0000">*</font> <b><?=GetMessage("BLOG_TITLE")?></b>
					</td>
					<td class="blogtablebody">
						<input type=text name=POST_TITLE value="<?=htmlspecialchars($POST_TITLE)?>" style="width:100%">
					</td>
				</tr>
				<tr id=tr_TEXT>
					<td class="blogtablehead" valign=top align=right nowrap>
						<font class="blogheadtext">
						<b><?=GetMessage("BLOG_TEXT")?></b>
					</td>
					<td class="blogtablebody">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td width=100%><textarea name="POST_MESSAGE" style="width:100%" rows=15 id=MESSAGE><?=htmlspecialchars($POST_MESSAGE)?></textarea></td>
							<td valign=middle>
								<table style="border:1px solid gray;margin-left:10px" cellspacing=5 cellpadding=0  border=0>
									<?
									$arSelectFields = array("ID", "SMILE_TYPE", "TYPING", "IMAGE", "DESCRIPTION", "CLICKABLE", "SORT", "IMAGE_WIDTH", "IMAGE_HEIGHT", "LANG_NAME");
									$total = 0;
									$arSmiles = array();
									$res=CBlogSmile::GetList($arOrder=array("SORT"=>"ASC","ID"=>"DESC"),$arFilter=array("SMILE_TYPE"=>"S","CLICKABLE"=>"Y","LANG_LID"=>LANGUAGE_ID),$arGroupBy=false,$arNavStartParams=false,$arSelectFields);
									while ($arr = $res->Fetch())
									{
										$total++;
										$arSmiles[] = $arr;
									}
									$cols = ceil($total/10);
									for($i=0;$i<count($arSmiles);$i++)
									{
										if ($i==0)
											print "<tr>";
										elseif ($i % $cols==0)
											print "\n</tr>\n<tr>";
										list($type)=explode(" ",$arSmiles[$i]['TYPING']);
										$type=str_replace("'","\'",$type);
										print "\n<td align=center><img src='/bitrix/images/blog/smile/{$arSmiles[$i]['IMAGE']}' width='{$arSmiles[$i]['IMAGE_WIDTH']}' height='{$arSmiles[$i]['IMAGE_HEIGHT']}' title='{$arSmiles[$i]['LANG_NAME']}' OnClick=\"emoticon('$type')\" style='cursor:pointer'></td>";
									}
									?>
									</tr>
								</table>
							</td>
						</table>
					</td>
				</tr>
		<?
		$arFilter = array(
			"POST_ID"=>$ID, 
			"BLOG_ID"=>$BLOG_ID
			);
		if ($ID==0)
			$arFilter['USER_ID'] = $USER_ID;
			
		$res = CBlogImage::GetList(array("ID"=>"ASC"), $arFilter);
		if ($aImg = $res->Fetch())
		{
			print " <tr>
					<td class=\"blogtablehead\" valign=top align=right nowrap>
						<font class=\"blogheadtext\"><b>".GetMessage("BLOG_P_IMAGES")."</b></font>
					</td>
					<td class=blogtablebody>
						<table cellspacing=0 cellpadding=4 border=0 id=img_TABLE align=left>
						";
						$i=0;
						do {
							if ($i==0)
								print "<tr>";
							elseif($i%4==0)
								print "</tr><tr>";
								
							print "
							<td valign='top'>
							".CFile::ShowImage($aImg['FILE_ID'], 100, 100, "border=0 style=cursor:pointer onclick=\"doInsert('[IMG ID=".$aImg['ID']."]','',false)\" title='".GetMessage("BLOG_P_INSERT")."'")."<br><input class=inputtext name=IMAGE_ID_title[".$aImg['ID']."] value=\"".htmlspecialchars($aImg['TITLE'])."\" style=\"width:100px\" title=\"".GetMessage("BLOG_BLOG_IN_IMAGES_TITLE")."\"><br><input type=checkbox name=IMAGE_ID_del[".$aImg['ID']."] id=img_del_$aImg[ID]> <label for=img_del_$aImg[ID] class=tablebodytext>".GetMessage("BLOG_DELETE")."</label>
							";
							$i++;
						} while ($aImg = $res->Fetch());
						print "
						</table>
					</td>
				</tr>";
		}
	?>
					
				<tr>
					<td class="blogtablehead" valign=top align=right nowrap>
					<font class="blogheadtext">
					<b><?=GetMessage("BLOG_STATUS")?></b></td>
					<td class="blogtablebody">
						<select name=PUBLISH_STATUS class=inputselect>
							<option value="<?=BLOG_PUBLISH_STATUS_DRAFT?>" <?=($PUBLISH_STATUS==BLOG_PUBLISH_STATUS_DRAFT?'selected':'')?>><?=$GLOBALS['AR_BLOG_PUBLISH_STATUS'][BLOG_PUBLISH_STATUS_DRAFT]?></option>
							<option value="<?=BLOG_PUBLISH_STATUS_PUBLISH?>" <?=($PUBLISH_STATUS==BLOG_PUBLISH_STATUS_DRAFT?'':'selected')?>><?=$GLOBALS['AR_BLOG_PUBLISH_STATUS'][BLOG_PUBLISH_STATUS_PUBLISH]?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="blogtablehead" valign=top align=right nowrap>
					<font class="blogheadtext">
					<b><?=GetMessage("BLOG_CATEGORY")?></b></td>
						<td class="blogtablebody"><select name=CATEGORY_ID id=CATEGORY_ID OnChange='AddCategory()' class=inputselect>
						<option value=0<?=($CATEGORY_ID==0 ? ' selected' : '')?>><?=GetMessage("BLOG_NO_CATEGORY")?></option>
						<?
						$i=0;
						$res=CBlogCategory::GetList(array("NAME"=>"ASC"),array("BLOG_ID"=>$BLOG_ID));
						while ($arCategory=$res->Fetch())
							print "<option value='$arCategory[ID]'".($arCategory['ID']==$CATEGORY_ID?' selected':'').">".htmlspecialchars($arCategory['NAME'])."</option>";
						?>
						<option value=NEW style="font-weight:bold"><?=GetMessage("BLOG_NEW_CATEGORY")?></option>
						</select></td>
				</tr>
				<tr>
					<td class="blogtablehead" valign=top align=right nowrap nowrap>
					<font class="blogheadtext">
					<font color="#FF0000">*</font> <b><?=GetMessage("BLOG_DATE_PUBLISH")?></b></td>
						<td nowrap class="blogtablebody"><?=CalendarDate("DATE_PUBLISH",$DATE_PUBLISH,"REPLIER","20")?></td>
				</tr>
		<?
		function ShowSelectPerms($type,$id,$def)
		{
			if ($type=='p')
				$arr = $GLOBALS["AR_BLOG_POST_PERMS"];
			else
				$arr = $GLOBALS["AR_BLOG_COMMENT_PERMS"];

			$res = "<select name='perms_{$type}[{$id}]' class=inputselect>";
			while(list(,$key)=each($arr))
				if ($id > 1 || ($type=='p' && $key <= BLOG_PERMS_READ) || ($type=='c' && $key <= BLOG_PERMS_WRITE))
					$res.= "<option value='$key'".($key==$def?' selected':'').">".$GLOBALS["AR_BLOG_PERMS"][$key]."</option>";
			$res.= "</select>";
			return $res;
		}
		?>
				<tr>
					<td class="blogtablehead" valign=top align=right nowrap>
					<font class="blogheadtext">
					<b><?=GetMessage("BLOG_ACCESS")?></b></td>
						<td valign=top class="blogtablebody">
							<input name=blog_perms value=0 onClick='show_special()' id=blog_perms_0 type=radio <?=$bExtendedPerms?'':'checked'?>> <label class=blogtext for=blog_perms_0><?=GetMessage("BLOG_DEFAULT_PERMS")?></label>
							<br>
							<input name=blog_perms value=1 onClick='show_special()' id=blog_perms_1 type=radio <?=$bExtendedPerms?'checked':''?>> <label class=blogtext for=blog_perms_1><?=GetMessage("BLOG_SPECIAL_PERMS")?></label>

							<div id=special_perms style="<?=($bExtendedPerms?'':'display:none')?>">
							<table class=blogtext cellspacing=0 cellpadding=5>
								<tr>
									<td><b><?=GetMessage("BLOG_GROUPS")?></b></td>
									<td><b><?=GetMessage("BLOG_POST_MESSAGE")?></b></td>
									<td><b><?=GetMessage("BLOG_COMMENTS")?></b></td>

								</tr>
								<tr>
									<td><?=GetMessage("BLOG_ALL_USERS")?></td>
									<td><?=ShowSelectPerms('p',1,$arUGperms_p[1])?></td>
									<td><?=ShowSelectPerms('c',1,$arUGperms_c[1])?></td>
								</tr>
								<tr>
									<td><?=GetMessage("BLOG_REG_USERS")?></td>
									<td><?=ShowSelectPerms('p',2,$arUGperms_p[2])?></td>
									<td><?=ShowSelectPerms('c',2,$arUGperms_c[2])?></td>
								</tr>
								
						<?
							$res=CBlogUserGroup::GetList(array(),$arFilter=array("BLOG_ID"=>$BLOG_ID));
							while ($aUGroup=$res->Fetch())
								print "
								<tr>
									<td>".htmlspecialchars($aUGroup['NAME'])."</td>
									<td>".ShowSelectPerms('p',$aUGroup['ID'],$arUGperms_p[$aUGroup['ID']])."</td>
									<td>".ShowSelectPerms('c',$aUGroup['ID'],$arUGperms_c[$aUGroup['ID']])."</td>
								</tr>";
						?>
							</table>
							</div>
					</td>
				</tr>

			<?if((COption::GetOptionString("blog","enable_trackback", "N") == "Y" && $arBlog["ALLOW_TRACKBACK"] == "Y"))
			{
				?>
				<tr>
					<td class="blogtablehead" valign=top align=right nowrap>
					<font class="blogheadtext">
					<b><?=GetMessage("BLOG_ADDRESSES")?></b></td>
						<td class="blogtablebody">
						<textarea name="TRACKBACK" style="width:100%" rows=5><?=htmlspecialchars($TRACKBACK)?></textarea>
				</tr>
				<tr>
					<td class="blogtablehead" valign=top align=right nowrap>
					<font class="blogheadtext">
					<b>Trackback:</b></td>
						<td class="blogtablebody">
						<input type=checkbox name=ENABLE_TRACKBACK value=Y id=enable_tb <?=($ENABLE_TRACKBACK=='Y'?'checked':'')?>>
						<label for=enable_tb class=blogtext><?=GetMessage("BLOG_ALLOW_TRACKBACK")?></label>
						</td>
				</tr>
				<?
			}
			?>
				</table>
				<br>
				<input type=submit name=save value='<?=GetMessage("BLOG_SAVE")?>' class="inputbutton">
				<input type=submit name=apply value='<?=GetMessage("BLOG_APPLY")?>' class="inputbutton">
				<input type=submit name=reset value='<?=GetMessage("BLOG_CANCEL")?>' class="inputbutton">
			</form>
			<br>
			<font class="blogheadtext">
			<?echo GetMessage("FPF_TO_QUOTE_NOTE")?><br>
			<?echo GetMessage("STOF_REQUIED_FIELDS_NOTE")?>
			</font>
			<?
		}
		else
			echo ShowError("".GetMessage("BLOG_ERR_NO_RIGHTS")."");
	}
	else
		echo ShowError("".GetMessage("BLOG_ERR_NO_BLOG")."");
}
else
	echo ShowError("".GetMessage("BLOG_ERR_NOT_INSTALLED")."");
?>
