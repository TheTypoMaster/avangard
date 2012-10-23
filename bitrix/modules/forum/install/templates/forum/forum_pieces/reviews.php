<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
IncludeTemplateLangFile(__FILE__);
if (CModule::IncludeModule("forum") &&  CModule::IncludeModule("iblock")):
//*******************************************************

// Infoblock items should have property with FORUM_TOPIC_ID code!

$FORUM_ID = IntVal($FORUM_ID);

if (intVal($PRODUCT_ID)<=0)
	$PRODUCT_ID = $GLOBALS["ID"];
$PRODUCT_ID = IntVal($PRODUCT_ID);

$cache = new CPHPCache; 
$cache_id = "forum_reviews_".$FORUM_ID."_".$PRODUCT_ID."_".CDBResult::NavStringForCache(5, false);

if ($_SERVER["REQUEST_METHOD"]=="POST" && $_POST["save_product_review"] == "Y")
{
	BXClearCache(True, "/forum/forum_pieces/reviews.php/".$FORUM_ID."_".$PRODUCT_ID);
}


if ($CACHE_TIME>0 && $cache->InitCache($CACHE_TIME, $cache_id, "/forum/forum_pieces/reviews.php/".$FORUM_ID."_".$PRODUCT_ID."/"))
{
	$cache->Output();
}
else
{
	if ($CACHE_TIME>0 && ($_SERVER["REQUEST_METHOD"]!="POST" || $_POST["save_product_review"]!="Y"))
		$cache->StartDataCache($CACHE_TIME, $cache_id, "/forum/forum_pieces/reviews.php/".$FORUM_ID."_".$PRODUCT_ID."/");

	$strSystemErrorMessage = "";
	$strErrorMessage = "";
	$strOKMessage = "";

	$FORUM_TOPIC_ID = 0;

	$db_product = CIBlockElement::GetList(array(), array("ID" => $PRODUCT_ID), false, false, array("IBLOCK_ID","NAME","PROPERTY_FORUM_TOPIC_ID"));
	if ($ar_product = $db_product->Fetch())
	{
		$PRODUCT_IBLOCK_ID = IntVal($ar_product["IBLOCK_ID"]);
		$PRODUCT_NAME = Trim($ar_product["NAME"]);
		$FORUM_TOPIC_ID = IntVal($ar_product["PROPERTY_FORUM_TOPIC_ID_VALUE"]);
	}
	else
	{
		$strSystemErrorMessage .= str_replace("#ITEM#", intval($PRODUCT_ID), GetMessage("FTR_NO_PRODUCT")).". ";
	}

	if ($FORUM_ID>0)
	{
		$arForum = CForumNew::GetByID($FORUM_ID);
		if (!$arForum)
			$strSystemErrorMessage .= str_replace("#FORUM#", intval($FORUM_ID), GetMessage("FTR_NO_FID")).". ";
	}
	else
		$strSystemErrorMessage .= GetMessage("FTR_FID_EMPTY").". ";


	if (strlen($strSystemErrorMessage)>0)
	{
		?>
		<br><font color="#FF0000"><b><?= $strSystemErrorMessage ?></b></font><br>
		<?
	}
	else
	{
		if ($_SERVER["REQUEST_METHOD"]=="POST" && $_POST["save_product_review"] == "Y")
		{
			if (strlen($_POST["REVIEW_TEXT"])<3)
				$strErrorMessage .= GetMessage("FTR_NO_REVIEW_TEXT").". ";

			if (strlen($strErrorMessage)<=0)
			{
				if ($FORUM_TOPIC_ID > 0)
				{
					$arTopic = CForumTopic::GetByID($FORUM_TOPIC_ID);
					if (!$arTopic || !is_array($arTopic) || count($arTopic) <= 0)
					{
						CIBlockElement::SetPropertyValues($PRODUCT_ID, $PRODUCT_IBLOCK_ID, 0, "FORUM_TOPIC_ID");
						$FORUM_TOPIC_ID = 0;
					}
				}
			}

			$sTransStarted = False;
			if (strlen($strErrorMessage)<=0)
			{
				$DB->StartTransaction();
				$sTransStarted = True;

				$strNewTopic = "N";
				if ($FORUM_TOPIC_ID <= 0)
				{
					$arFields = Array(
						"TITLE" => $PRODUCT_NAME,
						"FORUM_ID" => $FORUM_ID,
						"USER_START_NAME" => (strlen($_POST["REVIEW_AUTHOR"]) > 0) ? $_POST["REVIEW_AUTHOR"] : GetMessage("FTR_GUEST"),
						"LAST_POSTER_NAME" => (strlen($_POST["REVIEW_AUTHOR"]) > 0) ? $_POST["REVIEW_AUTHOR"] : GetMessage("FTR_GUEST"),
						"APPROVED" => ($arForum["MODERATION"]=="Y" ? "N" : "Y")
					);

					$strNewTopic = "Y";
					$FORUM_TOPIC_ID = CForumTopic::Add($arFields);
					if (IntVal($FORUM_TOPIC_ID)<=0)
						$strErrorMessage .= GetMessage("ADDMESS_ERROR_ADD_TOPIC").". ";
				}
			}

			if (strlen($strErrorMessage)<=0)
			{
				$AUTHOR_IP = ForumGetRealIP();
				$AUTHOR_IP_tmp = $AUTHOR_IP;
				$AUTHOR_REAL_IP = $_SERVER['REMOTE_ADDR'];
				$AUTHOR_IP = @gethostbyaddr($AUTHOR_IP);
				if ($AUTHOR_IP_tmp==$AUTHOR_REAL_IP)
					$AUTHOR_REAL_IP = $AUTHOR_IP;
				else
					$AUTHOR_REAL_IP = @gethostbyaddr($AUTHOR_REAL_IP);

				$arFields = Array(
					"POST_MESSAGE"	=> $_POST["REVIEW_TEXT"],
					"AUTHOR_NAME" => (strlen($_POST["REVIEW_AUTHOR"]) > 0) ? $_POST["REVIEW_AUTHOR"] : GetMessage("FTR_GUEST"),
					"FORUM_ID" => $FORUM_ID,
					"TOPIC_ID" => $FORUM_TOPIC_ID,
					"AUTHOR_IP" => ($AUTHOR_IP!==False) ? $AUTHOR_IP : "<no address>",
					"AUTHOR_REAL_IP" => ($AUTHOR_REAL_IP!==False) ? $AUTHOR_REAL_IP : "<no address>",
					"NEW_TOPIC" => $strNewTopic,
					"GUEST_ID" => $_SESSION["SESS_GUEST_ID"],
					"APPROVED" => ($arForum["MODERATION"]=="Y" ? "N" : "Y")
				);

				$MID1 = CForumMessage::Add($arFields);
				if (IntVal($MID1)<=0)
				{
					$strErrorMessage .= GetMessage("ADDMESS_ERROR_ADD_MESSAGE").". ";
					if ($strNewTopic == "Y")
					{
						CForumTopic::Delete($FORUM_TOPIC_ID);
						$FORUM_TOPIC_ID = 0;
					}
				}
			}

			if (strlen($strErrorMessage)<=0)
			{
				if ($strNewTopic == "Y")
				{
					CIBlockElement::SetPropertyValues($PRODUCT_ID, $PRODUCT_IBLOCK_ID, IntVal($FORUM_TOPIC_ID), "FORUM_TOPIC_ID");
				}

				$DB->Commit();
				$strOKMessage .= GetMessage("COMM_COMMENT_OK").". ";
			}
			else
			{
				if ($sTransStarted)
					$DB->Rollback();
			}
		}
		?>

		<table width="100%" border="0" cellSpacing="1" cellPadding="4">
			<form action="<?= $APPLICATION->GetCurPageParam("a", "a") ?>#review_anchor" method="POST">
				<tr>
					<td class="tablebody" valign="top">
						<a name="review_anchor"></a>
						<font class="tableheadtext">
						<?
						if (strlen($strErrorMessage)>0)
						{
							?><font color="#FF0000"><b><? echo GetMessage("OPINIONS_ERROR"); ?></b></font><br>
								<font color="#FF0000"><b>"<? echo $strErrorMessage; ?>"</b></font><br><?
						}
						if (strlen($strOKMessage)>0)
						{
							?><font color="#008800"><b><? echo GetMessage("OPINIONS_OK"); ?></b></font><br><br><?
						}

						$strUserName = "";
						if ($USER->IsAuthorized())
						{
							$ar_res = CForumUser::GetByUSER_ID($USER->GetID());
							if ($ar_res["SHOW_NAME"]=="Y")
								$strUserName = $USER->GetFullName();
							if (strlen($strUserName)<=0)
								$strUserName = $USER->GetLogin();
						}
						else
						{
							$strUserName = GetMessage("FTR_GUEST");
						}
						?>
						<? echo GetMessage("OPINIONS_NOTES"); ?>
						<div style="padding-bottom:5px;">
							<table cellspacing="0" cellpadding="0" border="0">
								<tr>
									<td valign="top" colspan="2" align="right">
										<textarea class="inputtextarea" cols="60" rows="7" name="REVIEW_TEXT"><?if (strlen($strErrorMessage)>0) echo htmlspecialchars($_POST["REVIEW_TEXT"]); ?></textarea>
									</td>
								</tr>
								<tr>
									<td align="left"><font class="tableheadtext"><? echo GetMessage("OPINIONS_NAME"); ?>:&nbsp;</font></td>
									<td align="right"><input class="inputtext" type="text" name="REVIEW_AUTHOR" value="<?echo (isset($_POST["REVIEW_AUTHOR"]) && strlen($_POST["REVIEW_AUTHOR"]) > 0) ? htmlspecialchars($_POST["REVIEW_AUTHOR"]) : htmlspecialchars($strUserName) ?>" size="31"></td>
								</tr>
							</table>
						</div>
						<input type="hidden" name="save_product_review" value="Y">
						<input class="inputbutton" type="submit" value="<?echo GetMessage("OPINIONS_SEND"); ?>">
						</font>
					</td>
				</tr>
				<tr>
					<td class="tablebody"><font class="tablebodytext"></font></td>
				</tr>
			</form>

			<?
			if ($FORUM_TOPIC_ID > 0):

				$parser = new textParser(LANGUAGE_ID);
				$db_res = CForumMessage::GetList(Array("ID"=>"DESC"), Array("FORUM_ID"=>$FORUM_ID, "TOPIC_ID"=>$FORUM_TOPIC_ID, "APPROVED" => "Y"));

				$db_res->NavStart(5, false);
				$arAllow = array(
					"HTML" => $arForum["ALLOW_HTML"],
					"ANCHOR" => $arForum["ALLOW_ANCHOR"],
					"BIU" => $arForum["ALLOW_BIU"],
					"IMG" => $arForum["ALLOW_IMG"],
					"LIST" => $arForum["ALLOW_LIST"],
					"QUOTE" => $arForum["ALLOW_QUOTE"],
					"CODE" => $arForum["ALLOW_CODE"],
					"FONT" => $arForum["ALLOW_FONT"],
					"SMILES" => $arForum["ALLOW_SMILES"],
					"UPLOAD" => $arForum["ALLOW_UPLOAD"],
					"NL2BR" => $arForum["ALLOW_NL2BR"]
					);

				while ($arMessage = $db_res->Fetch()):
					if ($arMessage["USE_SMILES"] != "Y")
						$arAllow["SMILES"] = "N";
					?>
					<tr>
						<td class="tablebody">
						<font class="tablebodytext">
						<?
					if ((COption::GetOptionString("forum", "FILTER", "Y")=="Y")||(COption::GetOptionString("forum", "MESSAGE_HTML", "Y")=="Y"))
						$message = $arMessage["POST_MESSAGE_HTML"];
					else 
						$message = $arMessage["POST_MESSAGE"];
						
					if (COption::GetOptionString("forum", "MESSAGE_HTML", "Y") == "N")
						$message = $parser->convert($message, $arAllow);
						
					echo $message;
					
					?><div align="right"><i><?= htmlspecialchars($arMessage["AUTHOR_NAME"]); ?>, <?= $arMessage["POST_DATE"]; ?></i></div>
						</font>
						</td>
					</tr>
					<?
				endwhile;
				?>
				<tr>
					<td class="tablebody">
					<font class="tablebodytext">
					<?echo $db_res->NavPrint(GetMessage("NAV_OPINIONS"));?>
					</font>
					</td>
				</tr>
				<?
			endif;
			?>
			<?
			if (IntVal($FORUM_ID)>0 && IntVal($FORUM_TOPIC_ID)>0):
				$arTopic = CForumTopic::GetByID($FORUM_TOPIC_ID);
				if($arTopic["APPROVED"]=="Y"):
				$arForumPaths = CForumNew::GetSites($FORUM_ID);
				if (isset($arForumPaths[LANG]) && strlen($arForumPaths[LANG])>0)
				{
					$strForumPath = CForumNew::PreparePath2Message($arForumPaths[LANG], array("FORUM_ID"=>$FORUM_ID, "TOPIC_ID"=>$FORUM_TOPIC_ID, "MESSAGE_ID"=>""));
					?>
					<tr>
						<td class="tablebody">
							<font class="tablebodytext">
							<a href="<?= $strForumPath ?>"><?= GetMessage("F_C_GOTO_FORUM") ?></a>
							</font>
						</td>
					</tr>
					<?
				}
			endif;
			endif;
			?>
		</table>
		<?
	}

	if ($CACHE_TIME>0 && ($_SERVER["REQUEST_METHOD"]!="POST" || $_POST["save_product_review"]!="Y"))
		$cache->EndDataCache(array());
}

//*******************************************************
endif;
?>