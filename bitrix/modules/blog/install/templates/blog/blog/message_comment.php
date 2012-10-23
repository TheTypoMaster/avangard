<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
/*
$APPLICATION->IncludeFile(
	"blog/blog/message_comment.php", 
	Array(
		"ID"=>$ID,
		"OWNER"=>$arPost["AUTHOR_ID"],
		"BLOG_ID" => $arPost["BLOG_ID"],
		"CACHE_TIME"=>0,
	)
);
*/

if (CModule::IncludeModule("blog"))
{
	function ShowComment($comment, $tabCount=0, $tabSize=30, $canModerate=false, $User, $use_captcha, $bCanUserComment)
	{
		global $APPLICATION, $is404;
		$tabWidth = $tabCount*$tabSize;

		if(IntVal($comment["AUTHOR_ID"])>0)
		{
		
			$arBlog = CBlog::GetByOwnerID(IntVal($comment["AUTHOR_ID"]));
			$urtToAuthor = CBlogUser::PreparePath($comment["AUTHOR_ID"], false, $is404);
			if(strlen($arBlog["URL"])>0)
				$urtToBlog = CBlog::PreparePath($arBlog["URL"], false, $is404);
			else
				$urtToBlog = CBlogUser::PreparePath($comment["AUTHOR_ID"], false, $is404);
			if(strlen($urtToBlog)<=0)
				$urtToBlog = false;
			$BlogUser = CBlogUser::GetByID($comment["AUTHOR_ID"], BLOG_BY_USER_ID); 
			$dbUser = CUser::GetByID($comment["AUTHOR_ID"]);
			$arUser = $dbUser->Fetch();
			$AuthorName = CBlogUser::GetUserName($BlogUser["ALIAS"], $arUser["NAME"], $arUser["LAST_NAME"], $arUser["LOGIN"]);
		}
		else
			$AuthorName = $comment["AUTHOR_NAME"];

		echo '<a name="'.$comment["ID"].'"></a>';
		echo '<table width="100%" cellpadding="0" border="0">';
		echo '<tr>';
		echo '<td width="0%"><img src="/images/1.gif" height="1" width="'.$tabWidth.'"></td>';
		echo '<td width="100%" valign="top">';
		echo '<table class="blogtableborder" cellspacing="1" cellpadding="0" width="100%" border="0">
						<tr>
						<td>
							<table border="0" width="100%" cellpadding="3" cellspacing="0" class="blogtablebody">
							<tr>
								<td class="blogtablehead" align="left" nowrap width="70%" style="padding-left:10px;"><font class="blogpostdate">'.$comment["DATE_CREATE"].'</font></td>
								<td align="right" class="blogtablehead" nowrap width="30%"><font class="blogauthor">';
		if($urtToBlog)
			echo '<a href="'.$urtToAuthor.'"><img src="/bitrix/templates/.default/blog/images/icon_user.gif" width="16" height="16" border="0" align="absmiddle"></a>&nbsp;<a href="'.$urtToBlog.'">'.htmlspecialcharsex($AuthorName).'</a><br>';
		else
		{
			echo '<img src="/bitrix/templates/.default/blog/images/icon_user.gif" width="16" height="16" border="0" align="absmiddle">&nbsp;'.htmlspecialcharsex($AuthorName);
			if(strlen($comment["AUTHOR_EMAIL"])>0 && $canModerate)
				echo '&nbsp;<small>(Email: <a href="mailto:'.htmlspecialchars($comment["AUTHOR_EMAIL"]).'">'.htmlspecialchars($comment["AUTHOR_EMAIL"]).'</a>)</small>';
		}
		echo '</font></td>';
		
		if($canModerate)
		{
			echo '<td align="right" nowrap class="blogtablehead" valign="center">';
			echo '<span class="blogtext">';
			echo '<small>&nbsp;('.GetMessage("B_B_MS_FROM").' '.$comment["AUTHOR_IP"];
			if(strlen($comment["AUTHOR_IP1"])>0)
				echo ', '.$comment["AUTHOR_IP1"];
			echo ')';
			echo '</small>&nbsp;<a href="'.$APPLICATION->GetCurPageParam("del_id=".$comment["ID"]."&".bitrix_sessid_get(), Array("del_id", "sessid")).($comment['PARENT_ID']>0 ? '#'.$comment['PARENT_ID'] : '').'"><img src="/bitrix/templates/.default/blog/images/delete_button.gif" width="18" height="18" border="0" title="'.GetMessage("B_B_MS_COMENT_DEL").'" align="absmiddle"></a>';
			echo '</span>';
			echo '</td>';
		}		
		
		$p = new blogTextParser();
		echo '				</tr>
						<tr>
								<td colspan="3" style="padding-left:10px; padding-right:10px; padding-top:5px; padding-bottom:5px;">'.CFile::ShowImage($BlogUser["AVATAR"], 100, 100, "align='right'").'<h2>'.$p->convert($comment["TITLE"], false).'</h2><font class="blogtext">';
		echo $p->convert($comment["POST_TEXT"], false);

		echo '</font><br clear="all">';

		echo '<div align="left" class="blogcommentlink" style="padding:10 0 5 0;">';
		if($bCanUserComment)
			echo '(<a href="javascript:void(0)" onclick="javascript:showComment(\''.$comment["ID"].'\', \'RE: '.str_replace(array("\\", "\"", "'"), array("\\\\", "\\"."\"", "\\'"), htmlspecialchars($comment["TITLE"])).'\')">'.GetMessage("B_B_MS_REPLY").'</a>)&nbsp;';
		if(IntVal($comment["PARENT_ID"])>0)
			echo '(<a href="#'.$comment["PARENT_ID"].'">'.GetMessage("B_B_MS_PARENT").'</a>)&nbsp;';
		echo '(<a href="#'.$comment["ID"].'">'.GetMessage("B_B_MS_LINK").'</a>)';
		echo '</div><div id=form_comment_'.$comment['ID'].'></div>';

		echo '
								</td>
							</tr>
							</table>
						</td>
						</tr>
						</table>
						';
		echo '</td></tr></table>';
		
	}
	
	function RecursiveComments($sArray, $key, $level=0, $first=false, $canModerate=false, $User, $use_captcha, $bCanUserComment)
	{
		foreach($sArray[$key] as $comment)
		{
			ShowComment($comment, $level, 30, $canModerate, $User, $use_captcha, $bCanUserComment);
			if(!empty($sArray[$comment["ID"]]))
			{
				$level++;
				foreach($sArray[$comment["ID"]] as $key1)
				{
					ShowComment($key1, $level, 30, $canModerate, $User, $use_captcha, $bCanUserComment);
					
					if(!empty($sArray[$key1["ID"]]))
					{
						RecursiveComments($sArray, $key1["ID"], ($level+1), false, $canModerate, $User, $use_captcha, $bCanUserComment);
					}
				}
			}
			if($first)
				$level=0;
		}
	}
?>	

	<?
	$ID = IntVal($ID);
	$BLOG_ID = IntVal($BLOG_ID);
	$CACHE_TIME = IntVal($CACHE_TIME);
	global $is404;
	$is404 = ($is404 == 'N') ? false: true;	

	$canModerate = false;
	$Perm = CBlogPost::GetBlogUserCommentPerms($ID,$USER->GetID());
	$Blog = CBlog::GetByID($BLOG_ID);
	
	//Удаление комментария
	if($Perm>=BLOG_PERMS_MODERATE && IntVal($_GET["del_id"])>0 && $_GET["sessid"] == bitrix_sessid())
	{
		$arResult = CBlogComment::GetByID(IntVal($_GET["del_id"]));
		if(!empty($arResult))
		{
			if(CBlogComment::Delete(IntVal($_GET["del_id"])))
			{
				BXClearCache(True, "/".SITE_ID."/blog/".$Blog["URL"]."/first_page/");
				BXClearCache(True, "/".SITE_ID."/blog/".$Blog["URL"]."/comment/".$arResult["POST_ID"]."/");
			}
		}
	}
	
	//Вывод комментариев
	if($Perm>=BLOG_PERMS_READ)
	{
		$user_id = $USER->GetID();
		$bCanUserComment = true;
		if($Perm<BLOG_PERMS_WRITE)
			$bCanUserComment = false;
		if(IntVal($user_id)>0)
		{
			$BlogUser = CBlogUser::GetByID($user_id, BLOG_BY_USER_ID); 
			$dbUser = CUser::GetByID($user_id);
			$arUser = $dbUser->Fetch();
			$User["NAME"] = CBlogUser::GetUserName($BlogUser["ALIAS"], $arUser["NAME"], $arUser["LAST_NAME"], $arUser["LOGIN"]);
			$User["ID"] = $user_id;
		}
		
		$cache = new CPHPCache;
		$cache_id = "blog_comment_".serialize($arParams)."_".$user_id;
		$cache_path = "/".SITE_ID."/blog/".$Blog["URL"]."/comment/".$ID."/";

		if ($CACHE_TIME > 0 && $cache->InitCache($CACHE_TIME, $cache_id, $cache_path))
		{
			$cache->Output();
		}
		else
		{
			if ($CACHE_TIME > 0)
				$cache->StartDataCache($CACHE_TIME, $cache_id, $cache_path);

			$Blog = CBlog::GetByID($BLOG_ID);
			$use_captcha = ($Blog["ENABLE_IMG_VERIF"]=="Y")? true : false;
			if($Perm >= BLOG_PERMS_MODERATE)
				$canModerate = true;
			$arOrder = Array("ID" => "ASC", "DATE_CREATE" => "ASC");
			$arFilter = Array("POST_ID"=>$ID);
			$arSelectedFields = Array("ID", "BLOG_ID", "POST_ID", "PARENT_ID", "AUTHOR_ID", "AUTHOR_NAME", "AUTHOR_EMAIL", "AUTHOR_IP", "AUTHOR_IP1", "TITLE", "POST_TEXT", "DATE_CREATE");
			$dbComment = CBlogComment::GetList($arOrder, $arFilter, false, false, $arSelectedFields);
			$arPostsT = CBlogPost::GetByID($ID);
			//echo '<hr class="tableborder"/>';
			echo '<div style="height:1px; overflow:hidden; background-color:#C7D2D5;"></div>';
			if($bCanUserComment)
				echo '<p align="center" class="blogtext"><a name="comment"></a><a href="javascript:void(0)" onclick="javascript:showComment(\'0\', \'RE: '.str_replace(array("\\", "\"", "'"), array("\\\\", "\\"."\"", "\\'"), htmlspecialchars($arPostsT["TITLE"])).'\')"><b>'.GetMessage("B_B_MS_ADD_COMMENT").'</b></a></p>
				<div id=form_comment_0></div>';
			$tabCount = 0;
			$prevID = 0;
			$resComments = Array();
			if($arComment = $dbComment->Fetch())
			{
				do
				{
					if(empty($resComments[IntVal($arComment["PARENT_ID"])]))
						$resComments[IntVal($arComment["PARENT_ID"])] = Array();
					$resComments[IntVal($arComment["PARENT_ID"])][] = $arComment;
			   	}
				while($arComment = $dbComment->Fetch());
				RecursiveComments($resComments, 0, 0, true, $canModerate, $User, $use_captcha, $bCanUserComment);
			}

	if ($strJSPath = $APPLICATION->GetTemplatePath("blog/blog/blog_js.php"))
		include($_SERVER["DOCUMENT_ROOT"].$strJSPath);

	$arurlToBlogs = CBlogSitePath::GetBySiteID(SITE_ID);
	$UrlToBlogs = $arurlToBlogs["PATH"];
	?>
	<script language="JavaScript">
	<?
	$form = "
		<form method=\"POST\" name=\"comment\" action=\"$UrlToBlogs/post_comment.php\">
		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"1\" border=\"0\" class=\"tableborder\">
		<tr>
			<td>
				<table cellspacing=\"0\" cellpadding=\"3\" width=\"100%\" class=\"tablebody\" border=\"0\">
				".bitrix_sessid_post()."
				<input type=\"hidden\" name=\"parentId\" id=parentId>
				<input type=\"hidden\" name=\"blogId\" value=\"$BLOG_ID\">
				<input type=\"hidden\" name=\"postId\" value=\"$ID\">
					<tr valign=\"top\">
						<td colspan=2 class=\"tablebody\" style=\"padding:0px;\">
						<table cellpadding=2 cellspacing=0 border=0>
							<tr valign=middle style=\"background-image:url(/bitrix/templates/.default/blog/images/message_edit/toolbarbg.gif)\">
								<td><select class=inputselect name='ffont' id=select_font onchange=\"alterfont(this.options[this.selectedIndex].value, 'FONT')\" >
										<option value='0'>".GetMessage("FPF_FONT")."</option>
										<option value='Arial' style='font-family:Arial'>Arial</option>
										<option value='Times' style='font-family:Times'>Times</option>
										<option value='Courier' style='font-family:Courier'>Courier</option>
										<option value='Impact' style='font-family:Impact'>Impact</option>
										<option value='Geneva' style='font-family:Geneva'>Geneva</option>
										<option value='Optima' style='font-family:Optima'>Optima</option>
										<option value='Verdana' style='font-family:Verdana'>Verdana</option>
								</select></td>
								<td nowrap>
									<a id=FontColor	class=blogButton href='javascript:ColorPicker()'></a>
									<a id=bold class=blogButton href='javascript:simpletag(\"B\")'></a>
									<a id=italic class=blogButton href='javascript:simpletag(\"I\")'></a>
									<a id=under class=blogButton href='javascript:simpletag(\"U\")'></a>
									<a id=url class=blogButton href='javascript:tag_url()'></a>
									<a id=image class=blogButton href='javascript:tag_image()'></a>
									<a id=quote class=blogButton href='javascript:quoteMessage()'></a>
									<a id=code class=blogButton href='javascript:simpletag(\"CODE\")'></a>
									<a id=list class=blogButton href='javascript:tag_list()'></a>
								</td>
								<td width=100% align=right nowrap><a id=close_all style=visibility:hidden class=blogButton href='javascript:closeall()' title='".GetMessage("FPF_CLOSE_OPENED_TAGS")."'>".GetMessage("FPF_CLOSE_ALL_TAGS")."</a></td>
							</tr>
					</table>
						</td>
					</tr>".
					(
					(strlen($User["NAME"])>0) 
					? 
						"<input type=\"hidden\" name=\"user_id\" value=\"".$User["ID"]."\">
						<tr valign=\"top\">
							<td width=\"0%\" align=\"right\" class=\"tablebodytext\">".GetMessage("B_B_MS_AUTHOR")."</td>
							<td width=\"100%\" class=\"tablebodytext\">".htmlspecialcharsex($User["NAME"])."</td>
						</tr>"
					:
						"<tr valign=\"top\">
							<td align=\"right\" class=\"tablebodytext\">".GetMessage("B_B_MS_NAME")."<font class=\"errortext\">*</font></td>
							<td><input size=\"50\" type=\"text\" name=\"user_name\" value=\"\" class=\"inputtext\"></td>
						</tr>
						<tr valign=\"top\">
							<td align=\"right\" class=\"tablebodytext\">Email:</td>
							<td><input size=\"50\" type=\"text\" name=\"user_email\" value=\"\" class=\"inputtext\"></td>
						</tr>"
					).
				"<tr valign=\"top\">
					<td align=\"right\" class=\"tablebodytext\">".GetMessage("B_B_MS_SUBJECT")."</td>
					<td><input size=\"50\" type=\"text\" name=\"subject\" value=\"".htmlspecialcharsex($subject)."\" class=\"inputtext\" id=\"subject\"></td>
				</tr>
				<tr valign=\"top\">
					<td align=\"right\" class=\"tablebodytext\">".GetMessage("B_B_MS_M_BODY")."<font class=\"errortext\">*</font></td>
					<td><textarea name=\"comment\" style=\"width:95%\" class=\"inputtextarea\" rows=\"5\" id=MESSAGE>".htmlspecialcharsex($comment)."</textarea>
					</td>
				</tr>";
				if($use_captcha && !$USER->IsAuthorized())
				{
					$form .= "<tr valign=\"top\">
						<td>&nbsp;</td>
						<td>";
								include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
								$cpt = new CCaptcha();
								$captchaPass = COption::GetOptionString("main", "captcha_password", "");
								if (strlen($captchaPass) <= 0)
								{
									$captchaPass = randString(10);
									COption::SetOptionString("main", "captcha_password", $captchaPass);
								}
								$cpt->SetCodeCrypt($captchaPass);
								$form .= "<div id=\"captcha\"></div>
								</td>
					</tr>
					<tr>
						<td align=\"right\" class=\"tablebodytext\">".GetMessage("B_B_MS_CAPTCHA_SYM")."</td>
						<td>
							<input type=\"hidden\" name=\"captcha_code\" value=\"".htmlspecialchars($cpt->GetCodeCrypt())."\">
							<input type=\"text\" size=\"10\" name=\"captcha_word\" class=\"inputtext\">
						</td>
					</tr>";
				}
		$form .="
				<tr>
					<td colspan=\"2\"><input type=\"submit\" name=\"post\" value=\"".GetMessage("B_B_MS_SEND")."\" class=\"inputbutton\"></td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		</form>
		";

		$form= str_replace("'","\'",$form);
		$form = str_replace("\r"," ",$form);
		$form = str_replace("\n","\\n' + \n' ",$form);

		echo "
		var my_form = '".$form."';
		var last_div = '';
		";
?>

	
	function showComment(key, subject)
	{
		var oDiv = document.getElementById('form_comment_' + last_div);
		if(oDiv)
			oDiv.innerHTML = '';
		var div = document.getElementById('form_comment_' + key);
		div.innerHTML = my_form;
		
		var img1 = document.createElement("img");
		img1.width = "20";
		img1.height = "20";
		img1.title = "<?=GetMessage("FPF_IMAGE")?>";
		img1.className = "blogButton";
		img1.src = "/bitrix/templates/.default/blog/images/message_edit/font_color.gif";
		document.getElementById("FontColor").appendChild(img1);

		var img2 = document.createElement("img");
		img2.width = "20";
		img2.height = "20";
		img2.title = "<?=GetMessage("FPF_BOLD")?>";
		img2.className = "blogButton";
		img2.src = "/bitrix/templates/.default/blog/images/message_edit/bold.gif";
		document.getElementById("bold").appendChild(img2);

		var img3 = document.createElement("img");
		img3.width = "20";
		img3.height = "20";
		img3.title = "<?=GetMessage("FPF_ITALIC")?>";
		img3.className = "blogButton";
		img3.src = "/bitrix/templates/.default/blog/images/message_edit/italic.gif";
		document.getElementById("italic").appendChild(img3);

		var img4 = document.createElement("img");
		img4.width = "20";
		img4.height = "20";
		img4.title = "<?=GetMessage("FPF_UNDER")?>";
		img4.className = "blogButton";
		img4.src = "/bitrix/templates/.default/blog/images/message_edit/under.gif";
		document.getElementById("under").appendChild(img4);

		var img5 = document.createElement("img");
		img5.width = "20";
		img5.height = "20";
		img5.title = "<?=GetMessage("FPF_HYPERLINK")?>";
		img5.className = "blogButton";
		img5.src = "/bitrix/templates/.default/blog/images/message_edit/link.gif";
		document.getElementById("url").appendChild(img5);

		var img6 = document.createElement("img");
		img6.width = "20";
		img6.height = "20";
		img6.title = "<?=GetMessage("BLOG_P_INSERT_IMAGE_LINK")?>";
		img6.className = "blogButton";
		img6.src = "/bitrix/templates/.default/blog/images/message_edit/image_link.gif";
		document.getElementById("image").appendChild(img6);

		var img7 = document.createElement("img");
		img7.width = "20";
		img7.height = "20";
		img7.title = "<?=GetMessage("FPF_QUOTE")?>";
		img7.className = "blogButton";
		img7.src = "/bitrix/templates/.default/blog/images/message_edit/quote.gif";
		document.getElementById("quote").appendChild(img7);
			
		var img8 = document.createElement("img");
		img8.width = "20";
		img8.height = "20";
		img8.title = "<?=GetMessage("FPF_CODE")?>";
		img8.className = "blogButton";
		img8.src = "/bitrix/templates/.default/blog/images/message_edit/code.gif";
		document.getElementById("code").appendChild(img8);

		var img9 = document.createElement("img");
		img9.width = "20";
		img9.height = "20";
		img9.title = "<?=GetMessage("FPF_LIST")?>";
		img9.className = "blogButton";
		img9.src = "/bitrix/templates/.default/blog/images/message_edit/list.gif";
		document.getElementById("list").appendChild(img9);
		<?
		if($use_captcha && !$USER->IsAuthorized())
		{
			?>
		var img10 = document.createElement("img");
		img10.width = "180";
		img10.height = "40";
		img10.src = "/bitrix/tools/captcha.php?captcha_code=<?=htmlspecialchars($cpt->GetCodeCrypt())?>";
		document.getElementById("captcha").appendChild(img10);
			<?
		}?>
		document.getElementById('parentId').value = key;
		document.getElementById('subject').value = subject;
		last_div = key;
		return false;	
	}
	
	</script>
<?

			if ($CACHE_TIME > 0)
				$cache->EndDataCache(array());

		}
	}
}
else
	echo ShowError(GetMessage("B_B_MS_NO_MODULE"));
 ?>
