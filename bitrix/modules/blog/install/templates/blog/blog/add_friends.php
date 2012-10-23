<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("blog")):
//*******************************************************
$GLOBALS["APPLICATION"]->SetTemplateCSS("blog/blog.css");

$BLOG_URL = Trim($BLOG_URL);
$BLOG_URL = preg_replace("/[^a-zA-Z0-9_-]/is", "", $BLOG_URL);
$is404 = ($is404=='N') ? false: true;

if (StrLen($BLOG_URL) > 0)
{
	$dbBlog = CBlog::GetList(array(), array("URL" => $BLOG_URL), false, false, array("ID", "NAME"));
	if ($arBlog = $dbBlog->Fetch())
	{
		$arPath = CBlogSitePath::GetBySiteID(SITE_ID);
		$strPath = $arPath["PATH"];		
		if (CBlog::CanUserManageBlog($arBlog["ID"], ($GLOBALS["USER"]->IsAuthorized() ? $GLOBALS["USER"]->GetID() : 0 )))
		{
			$errorMessage = "";
			$okMessage = "";

			if (isset($ADD_FRIEND) && is_array($ADD_FRIEND))
			{
				foreach ($ADD_FRIEND as $key => $friend)
				{
					$friend = preg_replace("/[^a-zA-Z0-9_-]/is", "", $friend);
					if (StrLen($friend) > 0)
					{
						$arUserID = array();

						$dbSearchUser = CBlog::GetList(array(), array("URL" => $friend), false, false, array("ID", "OWNER_ID"));
						if ($arSearchUser = $dbSearchUser->Fetch())
							$arUserID[] = $arSearchUser["OWNER_ID"];

						if (count($arUserID) <= 0)
						{
							$dbSearchUser = CBlog::GetList(array(), array("NAME" => $friend), false, false, array("ID", "OWNER_ID"));
							if ($arSearchUser = $dbSearchUser->Fetch())
								$arUserID[] = $arSearchUser["OWNER_ID"];
						}

						if (count($arUserID) <= 0)
						{
							$canUseAlias = COption::GetOptionString("blog", "allow_alias", "Y");
							if ($canUseAlias == "Y")
							{
								$dbSearchUser = CBlogUser::GetList(array(), array("ALIAS" => $friend), false, false, array("ID", "USER_ID"));
								if ($arSearchUser = $dbSearchUser->Fetch())
									$arUserID[] = $arSearchUser["USER_ID"];
							}
						}

						if (count($arUserID) <= 0)
						{
							$dbSearchUser = CUser::GetList(($b = ""), ($o = ""), array("NAME" => $friend));
							while ($arSearchUser = $dbSearchUser->Fetch())
								$arUserID[] = $arSearchUser["ID"];
						}

						if (count($arUserID) > 0)
						{
							for ($i = 0; $i < count($arUserID); $i++)
							{
								$dbCandidate = CBlogCandidate::GetList(
									array(),
									array("BLOG_ID" => $arBlog["ID"], "USER_ID" => $arUserID[$i])
								);
								if ($dbCandidate->Fetch())
								{
									$okMessage .= str_replace("#NAME#", "[".$arUserID[$i]."] ".$friend, GetMessage("BLOG_BLOG_ADD_F_POS_ALREADY_WANT")).".<br>";
								}
								else
								{
									if (CBlogCandidate::Add(array("BLOG_ID" => $arBlog["ID"], "USER_ID" => $arUserID[$i])))
										$okMessage .= str_replace("#NAME#", "[".$arUserID[$i]."] ".$friend, GetMessage("BLOG_BLOG_ADD_F_POS_ADDED")).".<br>";
									else
										$errorMessage .= str_replace("#NAME#", "[".$arUserID[$i]."] ".$friend, GetMessage("BLOG_BLOG_ADD_F_POS_ADD_ERROR")).".<br>";
								}
							}
						}
						else
						{
							$errorMessage .= str_replace("#NAME#", $friend, GetMessage("BLOG_BLOG_ADD_F_POS_NOT_FOUND")).".<br>";
						}
					}
				}
			}

			$sessKey = randString(10);
			$_SESSION[$sessKey] = array();
			$_SESSION[$sessKey]["ERROR"] = $errorMessage;
			$_SESSION[$sessKey]["OK"] = $okMessage;
			if($is404)
				LocalRedirect(CBlog::PreparePath($BLOG_URL)."user_settings.php?sessKey=".UrlEncode($sessKey));
			else
				LocalRedirect($strPath."/user_settings.php?sessKey=".UrlEncode($sessKey)."&blog=".$BLOG_URL);
		}
		else
		{
			LocalRedirect($strPath."/blog_auth.php?back_url=".UrlEncode(CBlog::PreparePath($BLOG_URL)."user_settings.php"));

			?><div class="blogError"><?=GetMessage("BLOG_BLOG_ADD_F_U_HAVENT_RIGHTS")?></div><?
		}
	}
	else
	{
		?><div class="blogError"><?=GetMessage("BLOG_BLOG_ADD_F_BLOG_NOT_FOUND")?></div><?
	}
}
else
{
	?><div class="blogError"><?=GetMessage("BLOG_BLOG_ADD_F_BLOG_NOT_FOUND")?></div><?
}

//*******************************************************
endif;
?>