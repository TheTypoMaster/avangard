<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
/*
$APPLICATION->IncludeFile("blog/blog/post_comment.php");
*/
$is404 = ($is404=='N') ? false: true;

if (CModule::IncludeModule("blog"))
{
	$GLOBALS["APPLICATION"]->SetTemplateCSS("blog/blog.css");
	if(IntVal($_POST["postId"])>0 && IntVal($_POST["blogId"])>0)
	{
		$Perm = CBlogPost::GetBlogUserCommentPerms(IntVal($_POST["postId"]), $USER->GetID());
		$message=null;
		$strErrorMessage = "";
		

		$arr = CBlogSitePath::GetBySiteID(SITE_ID);
		$sBlogPath = $arr['PATH'];
		
		if($Perm>=BLOG_PERMS_WRITE && $_POST["sessid"] == bitrix_sessid() && strlen($_POST["post"])>0)
		{
			//print_r($_POST);
			$strErrorMessage = '';
			$arBlog = CBlog::GetByID(IntVal($_POST["blogId"]));
			$arPost = CBlogPost::GetByID(IntVal($_POST["postId"]));
			$APPLICATION->AddChainItem($arBlog["NAME"], CBlog::PreparePath($arBlog["URL"]));
			$APPLICATION->AddChainItem($arPost["TITLE"], CBlogPost::PreparePath($arBlog["URL"], $arPost["ID"]));
			if (!$USER->IsAuthorized() && $arBlog["ENABLE_IMG_VERIF"]=="Y")
			{
				include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
				$captcha_code = $_POST["captcha_code"];
				$captcha_word = $_POST["captcha_word"];
				$cpt = new CCaptcha();
				$captchaPass = COption::GetOptionString("main", "captcha_password", "");
				if (strlen($captcha_code) > 0)
				{
					if (!$cpt->CheckCodeCrypt($captcha_word, $captcha_code, $captchaPass))
						$strErrorMessage .= GetMessage("B_B_PC_CAPTCHA_ERROR")." \n";
				}
				// elseif(strlen($captcha_sid)>0)
				// {
					// if (!$cpt->CheckCode($captcha_word, $captcha_sid))
						// $strErrorMessage .= GetMessage("B_B_PC_CAPTCHA_ERROR")." \n";
				// }
				else
					$strErrorMessage .= GetMessage("B_B_PC_CAPTCHA_ERROR")." \n";
			}

			$UserIP = CBlogUser::GetUserIP();
			$arFields = Array(
						"POST_ID" => $_POST["postId"],
						"BLOG_ID" => $_POST["blogId"],
						"TITLE" => $_POST["subject"],
						"POST_TEXT" => $_POST["comment"],
						"DATE_CREATE" => ConvertTimeStamp(false, "FULL"),
						"AUTHOR_IP" => $UserIP[0],
						"AUTHOR_IP1" => $UserIP[1],
						);
						
			if(IntVal($_POST["user_id"])>0)
				$arFields["AUTHOR_ID"] = $USER->GetID();
			else
			{
				$arFields["AUTHOR_NAME"] = $_POST["user_name"];
				if(strlen($_POST["user_email"])>0)
					$arFields["AUTHOR_EMAIL"] = $_POST["user_email"];
				if(strlen($_POST["user_name"])<=0)
					$strErrorMessage .= GetMessage("B_B_PC_NO_ANAME");
			}

			if(IntVal($_POST["parentId"])>0)
				$arFields["PARENT_ID"] = IntVal($_POST["parentId"]);
			else 
				$arFields["PARENT_ID"] = false;
			
			if(strlen($_POST["comment"])<=0)
				$strErrorMessage .= GetMessage("B_B_PC_NO_COMMENT");
			if(strlen($arFields["TITLE"])<=0)
			{
				if($arFields["PARENT_ID"]>0)
				{
					$PrevCom = CBlogComment::GetByID($arFields["PARENT_ID"]);
					$arFields["TITLE"] = "RE: ".$PrevCom["TITLE"];
				}
				else
				{
					$Mes = CBlogPost::GetByID($arFields["POST_ID"]);
					$arFields["TITLE"] = "RE: ".$Mes["TITLE"];
				}
			}
			//print_r($arFields);
			if(strlen($strErrorMessage)<=0)
			{
				if($commmentId = CBlogComment::Add($arFields))
				{
					BXClearCache(True, "/".SITE_ID."/blog/".$arBlog["URL"]."/first_page/");
					BXClearCache(True, "/".SITE_ID."/blog/".$arBlog["URL"]."/comment/".$arFields["POST_ID"]."/");
					$urlToPost = CBlogPost::PreparePath($arBlog["URL"], $arFields["POST_ID"], false, $is404);
					$BlogUser = CBlogUser::GetByID($USER->GetID(), BLOG_BY_USER_ID);
					$dbUser = CUser::GetByID($USER->GetID());
					$arUser = $dbUser->Fetch();
					$AuthorName = CBlogUser::GetUserName($BlogUser["ALIAS"], $arUser["NAME"], $arUser["LAST_NAME"], $arUser["LOGIN"]); 
					if(strlen($AuthorName)<=0)
						$AuthorName = $arFields["AUTHOR_NAME"];
					$arMailFields = array(
							"BLOG_ID"	=> $arBlog['ID'],
							"BLOG_NAME"	=> $arBlog['NAME'],
							"BLOG_URL"	=> $arBlog['URL'],
							"MESSAGE_TITLE" => $arPost['TITLE'],
							"COMMENT_TITLE" => $_POST['subject'],
							"COMMENT_TEXT" => $_POST['comment'],
							"COMMENT_DATE" => ConvertTimeStamp(false, "FULL"),
							"COMMENT_PATH" => "http://".COption::GetOptionString("main","server_name").$urlToPost."#".$commmentId,
							"AUTHOR"	 => $AuthorName,
							"EMAIL_FROM"	 => COption::GetOptionString("main","email_from", "nobody@nobody.com"),
					);

					if ($arBlog['EMAIL_NOTIFY']=='Y' && $USER->GetID() != $arPost['AUTHOR_ID']) // Если автор комента не является автором поста 
					{
						$res = CUser::GetByID($arPost['AUTHOR_ID']);
						$arOwner = $res->Fetch();
						$arMailFields["EMAIL_TO"] = $arOwner['EMAIL'];

						CEvent::Send(
							"NEW_BLOG_COMMENT",
							SITE_ID,
							$arMailFields
						);
					}

					if($arFields["PARENT_ID"] > 0) // Если есть комент выше - будем пытаться уведомить автора
					{
						$arPrev = CBlogComment::GetByID($arFields["PARENT_ID"]);
						if ($USER->GetID() != $arPrev['AUTHOR_ID']) // Если автор верхнего комента не автор поста
						{
							$email = '';

							$res = CUser::GetByID($arPrev['AUTHOR_ID']);
							if ($arOwner = $res->Fetch()) // Зарег. пользователь
							{
								$arPrevBlog = CBlog::GetByOwnerID($arPrev['AUTHOR_ID']);
								if ($arPrevBlog['EMAIL_NOTIFY']!='N') // Автор верхнего комента хочет получать емайл
									$email = $arOwner['EMAIL'];
							}
							elseif($arPrev['AUTHOR_EMAIL']) // Аноним, но оставил свой мейл
								$email = $arPrev['AUTHOR_EMAIL'];

							if ($email)
							{
								$arMailFields['EMAIL_TO'] = $email;
								CEvent::Send(
									"NEW_BLOG_COMMENT",
									SITE_ID,
									$arMailFields
								);
							}
						}
					}
					
					LocalRedirect($urlToPost."#".$commmentId);
				}
			}
			else
			{
				if ($e = $APPLICATION->GetException())
					echo ShowError(GetMessage("B_B_PC_COM_ERROR")." ".$e->GetString());
				if(strlen($strErrorMessage)>0)
					echo ShowError(GetMessage("B_B_PC_COM_ERROR")." ".$strErrorMessage);
					
				$user_id = $USER->GetID();
				if(IntVal($user_id)>0)
				{
					$BlogUser = CBlogUser::GetByID($user_id, BLOG_BY_USER_ID); 
					$dbUser = CUser::GetByID($user_id);
					$arUser = $dbUser->Fetch();
					$User["NAME"] = CBlogUser::GetUserName($BlogUser["ALIAS"], $arUser["NAME"], $arUser["LAST_NAME"], $arUser["LOGIN"]);
					$User["ID"] = $user_id;
				}
				?>
						<table width="100%" cellpadding="0" cellspacing="1" border="0" class="blogtableborder">
						<form method="POST" name="comment" enctype="multipart/form-data" action="">
						<tr>
							<td>
								<table cellspacing="0" cellpadding="3" width="100%" class="blogtablebody" border="0">
								<?=bitrix_sessid_post();?>
								<input type="hidden" name="parentId" value="<?=$arFields["PARENT_ID"]?>">
								<input type="hidden" name="blogId" value="<?=$arFields["BLOG_ID"]?>">
								<input type="hidden" name="postId" value="<?=$arFields["POST_ID"]?>">
								<?if(strlen($User["NAME"])>0):?>
									<input type="hidden" name="user_id" value="<?=$User["ID"]?>">
									<tr valign="top">
										<td width="0%" align="right" class="blogtext"><?=GetMessage("B_B_PC_AUTHOR")?></td>
										<td width="100%" class="blogtext"><?=htmlspecialcharsex($User["NAME"])?></td>
									</tr>
								<?else:?>
									<tr valign="top">
										<td align="right" class="blogtext"><?=GetMessage("B_B_PC_UNAME")?><font class="errortext">*</font></td>
										<td><input size="50" type="text" name="user_name" value="<?=htmlspecialcharsex($arFields["AUTHOR_NAME"])?>" class="inputtext"></td>
									</tr>
									<tr valign="top">
										<td align="right" class="blogtext">Email:</td>
										<td><input size="50" type="text" name="user_email" value="<?=htmlspecialcharsex($arFields["AUTHOR_EMAIL"])?>" class="inputtext"></td>
									</tr>
								<?endif;?>
								<tr valign="top">
									<td align="right" class="blogtext"><?=GetMessage("B_B_PC_SUBJECT")?></td>
									<td><input size="50" type="text" name="subject" value="<?=htmlspecialcharsex($arFields["TITLE"])?>" class="inputtext"></td>
								</tr>
								<tr valign="top">
									<td align="right" class="blogtext"><?=GetMessage("B_B_PC_BODY")?><font class="errortext">*</font></td>
									<td><textarea name="comment" style="width:95%" rows="5" class="inputtextarea"><?=htmlspecialcharsex($arFields["POST_TEXT"])?></textarea>
									</td>
								</tr>
								<?if($arBlog["ENABLE_IMG_VERIF"]=="Y" && !$USER->IsAuthorized()):?>
									<tr valign="top">
										<td>&nbsp;</td>
										<td><?
												include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
												$cpt = new CCaptcha();
												$captchaPass = COption::GetOptionString("main", "captcha_password", "");
												if (strlen($captchaPass) <= 0)
												{
													$captchaPass = randString(10);
													COption::SetOptionString("main", "captcha_password", $captchaPass);
												}
												$cpt->SetCodeCrypt($captchaPass);
												?>
												<img src="/bitrix/tools/captcha.php?captcha_code=<?= htmlspecialchars($cpt->GetCodeCrypt()) ?>"></td>
									</tr>
									<tr>
										<td align="right" class="blogtext"><?=GetMessage("B_B_PC_CAPTCHA_CODE")?></td>
										<td>
											<input type="hidden" name="captcha_code" value="<?= htmlspecialchars($cpt->GetCodeCrypt()) ?>">
											<input type="text" size="10" name="captcha_word" class="inputtext">
										</td>
									</tr>
								<?endif;?>

								<tr>
									<td colspan="2"><input type="submit" name="post" class="inputbutton" value="<?=GetMessage("B_B_PC_SEND")?>"></td>
								</tr>
								</table>
							</td>
						</tr>
						</form>
						</table>
				<?
			}
		}
		else
			echo ShowError(GetMessage("B_B_PC_NO_RIGHTS"));
	}
	else
		echo ShowError(GetMessage("B_B_PC_NO_BLOG_POST"));
}
else
	echo ShowError(GetMessage("B_B_PC_NO_MODULE"));
 ?>
