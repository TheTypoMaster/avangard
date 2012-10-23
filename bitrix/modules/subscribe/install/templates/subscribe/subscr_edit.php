<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
if(CModule::IncludeModule("subscribe")):

if (!is_set($arParams, "ALLOW_ANONYMOUS"))
	$ALLOW_ANONYMOUS = COption::GetOptionString("subscribe", "allow_anonymous", "Y");

if (!is_set($arParams, "SHOW_AUTH_LINKS"))
	$SHOW_AUTH_LINKS = COption::GetOptionString("subscribe", "show_auth_links", "Y");

//variables from component
$ALLOW_ANONYMOUS = ($ALLOW_ANONYMOUS <> "N"? "Y":"N");
$SHOW_AUTH_LINKS = ($SHOW_AUTH_LINKS <> "N"? "Y":"N");
$SHOW_HIDDEN = ($SHOW_HIDDEN <> "Y"? "N":"Y");

//options
$bAllowAnonymous = ($ALLOW_ANONYMOUS == "Y");
$bShowAuthLinks = ($SHOW_AUTH_LINKS == "Y");
$bAllowRegister = (COption::GetOptionString("main", "new_user_registration") == "Y");
$sLastLogin = ${COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"};

$ID = intval($_REQUEST["ID"]); // Id of the subscription
$strWarning = "";

$subscr = new CSubscription;

//onscreen messages about actions
$aMsg = array(
	"UPD"=>GetMessage("adm_upd_mess"),
	"SENT"=>GetMessage("adm_sent_mess"),
	"SENTPASS"=>GetMessage("subscr_pass_mess"),
	"CONF"=>GetMessage("adm_conf_mess"),
	"UNSUBSCR"=>GetMessage("adm_unsubscr_mess"),
	"ACTIVE"=>GetMessage("subscr_active_mess")
);
$iMsg = $_REQUEST["mess_code"];

//*************************
//settings form processing
//*************************
$bVarsFromForm = false;
if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_REQUEST["PostAction"]))
{
	$bVarsFromForm = true;

	if(!empty($_REQUEST["LOGIN"]))
	{
		//authorize the user
		$res = $USER->Login($_REQUEST["LOGIN"], $_REQUEST["PASSWORD"]);
		if($res["TYPE"] == "ERROR")
			$strWarning .= $res["MESSAGE"];
	}
	elseif(!empty($_REQUEST["NEW_LOGIN"]))
	{
		//new user
		$res = $USER->Register($_REQUEST["NEW_LOGIN"], "", "", $_REQUEST["NEW_PASSWORD"], $_REQUEST["CONFIRM_PASSWORD"], $_REQUEST["EMAIL"], false, $_REQUEST["captcha_word"], $_REQUEST["captcha_sid"]);
		if($res["TYPE"] == "ERROR")
			$strWarning .= $res["MESSAGE"];
	}

	//if anonymous users are not permitted then the user must be authorized
	if(!$bAllowAnonymous && !$USER->IsAuthorized())
		$strWarning .= GetMessage("adm_auth_err")."<br>";
	
	//there must be at least one newsletter category
	if(!is_array($_REQUEST["RUB_ID"]) || count($_REQUEST["RUB_ID"]) == 0)
		$strWarning .= GetMessage("adm_auth_err_rub")."<br>";
	elseif($SHOW_HIDDEN=="N") //check for hidden categories
	{
		$bAllowSubscription=true;
		foreach($_REQUEST["RUB_ID"] as $rub_id)
		{
			$rsRubric = CRubric::GetByID($rub_id);
			if($arRubric = $rsRubric->Fetch())
				if($arRubric["VISIBLE"]=="N")
					$bAllowSubscription=false;
		}
		if($bAllowSubscription===false)
			$strWarning .= GetMessage("subscr_wrong_rubric")."<br>";
	}
	
	if($strWarning == "")
	{
		$arFields = Array(
			"USER_ID" => ($USER->IsAuthorized()? $USER->GetID():false), 
			"FORMAT" => ($_REQUEST["FORMAT"] <> "html"? "text":"html"), 
			"EMAIL" => $_REQUEST["EMAIL"], 
			"RUB_ID" => $_REQUEST["RUB_ID"]
		);
	
		$res = false;
		if($ID>0)
		{
			//allow edit only after authorization
			if(CSubscription::IsAuthorized($ID))
			{
				$res = $subscr->Update($ID, $arFields);
				if($res)
					$iMsg = ($subscr->LAST_MESSAGE<>""? $subscr->LAST_MESSAGE:"UPD");
			}
		}
		else
		{
			//can add without authorization
			$arFields["ACTIVE"] = "Y";
			$ID = $subscr->Add($arFields);
			$res = ($ID>0);
			if($res)
			{
				$iMsg = "SENT";
				CSubscription::Authorize($ID);
			}
		}
	
		if($res)
		{
			//remember e-mail in cookies
			$bVarsFromForm = false;
			$APPLICATION->set_cookie("SUBSCR_EMAIL", $_REQUEST["EMAIL"], mktime(0,0,0,12,31,2030));
			LocalRedirect($APPLICATION->GetCurPage()."?ID=".$ID.($iMsg <> ""? "&mess_code=".$iMsg:""));
		}
		else
			$strWarning .= $subscr->LAST_ERROR;
	}//$strWarning
}//POST

//default values
$str_FORMAT = "text";
$str_EMAIL = htmlspecialchars($_REQUEST["sf_EMAIL"]);

//new or existing subscription?
//ID==0 indicates new subscription
if(strlen($_REQUEST["sf_EMAIL"]) > 0 || $ID > 0)
{
	if($ID > 0)
		$subscription = CSubscription::GetByID($ID);
	else
		$subscription = CSubscription::GetByEmail($_REQUEST["sf_EMAIL"]);

	if(($subscr_arr = $subscription->Fetch()))
	{
		foreach($subscr_arr as $key => $value)
			${"str_".$key} = htmlspecialchars($value);

		$ID = (integer)$str_ID;
	}
	else
		$ID=0;
}
else
	$ID = 0;

//try to authorize subscription by CONFIRM_CODE or user password AUTH_PASS
if($ID > 0 && !CSubscription::IsAuthorized($ID))
{
	if($str_USER_ID > 0 && !empty($_REQUEST["AUTH_PASS"]))
	{
		//trying to login user
		$usr = CUser::GetByID($str_USER_ID);
		if(($usr_arr = $usr->Fetch()))
		{
			$res = $USER->Login($usr_arr["LOGIN"], $_REQUEST["AUTH_PASS"]);
			if($res["TYPE"] == "ERROR")
				$strWarning .= $res["MESSAGE"];
		}
	}
	CSubscription::Authorize($ID, (empty($_REQUEST["AUTH_PASS"])? $_REQUEST["CONFIRM_CODE"]:$_REQUEST["AUTH_PASS"]));
}
	
//confirmation code from letter or confirmation form 
if($_REQUEST["CONFIRM_CODE"] <> "" && $ID > 0 && empty($_REQUEST["action"]))
{
	if($str_CONFIRMED <> "Y")
	{
		//subscribtion confirmation
		if($subscr->Update($ID, array("CONFIRM_CODE"=>$_REQUEST["CONFIRM_CODE"])))
			$str_CONFIRMED = "Y";
		$strWarning .= $subscr->LAST_ERROR;
		$iMsg = $subscr->LAST_MESSAGE;
	}
}

//*************************
//form actions processing
//*************************
if($ID > 0)
{
	//confirmation code request
	if($_REQUEST["action"] == "sendcode")
	{
		if(CSubscription::ConfirmEvent($ID))
			$iMsg = "SENT";
	}
	if($_REQUEST["action"] == "sendpassword")
	{
		if(intval($str_USER_ID) == 0)
		{
			//anonymous subscription
			if(CSubscription::ConfirmEvent($ID))
				$iMsg = "SENT";
		}
		else
		{
			//user account subscription
			CUser::SendUserInfo($str_USER_ID, LANG, GetMessage("subscr_send_pass_mess"));
			$iMsg = "SENTPASS";
			LocalRedirect($APPLICATION->GetCurPage()."?sf_EMAIL=".urlencode($_REQUEST["sf_EMAIL"])."&change_password=yes&mess_code=".$iMsg);
		}
	}
	if($_REQUEST["action"] == "unsubscribe" && CSubscription::IsAuthorized($ID))
	{
		//unsubscription
		if($subscr->Update($ID, array("ACTIVE"=>"N")))
		{
			$str_ACTIVE = "N";
			$iMsg = "UNSUBSCR";
		}
	}
	if($_REQUEST["action"] == "activate" && CSubscription::IsAuthorized($ID))
	{
		//activation
		if($subscr->Update($ID, array("ACTIVE"=>"Y")))
		{
			$str_ACTIVE = "Y";
			$iMsg = "ACTIVE";
		}
	}
}
if($ID == 0 && !empty($_REQUEST["action"]))
	$strWarning .= GetMessage("subscr_email_not_found")."<br>";

//initialize variables from POST on error
if($bVarsFromForm)
{
	$str_FORMAT = $_REQUEST["FORMAT"];
	$str_EMAIL = htmlspecialchars($_REQUEST["EMAIL"]);
}

//page title
if($ID>0)
	$APPLICATION->SetTitle(GetMessage("subscr_title_edit"));
else
	$APPLICATION->SetTitle(GetMessage("subscr_title_add"));
?>

<?echo ShowMessage(array("MESSAGE"=>$aMsg[$iMsg], "TYPE"=>"OK"));?>
<?echo ShowMessage(array("MESSAGE"=>$strWarning, "TYPE"=>"ERROR"));?>

<?
//if the subscription belongs to USER_ID then authorization is required
if($ID > 0 && (integer)$str_USER_ID > 0 && !CSubscription::IsAuthorized($ID)):
	unset($HTTP_GET_VARS["mess_code"]);
	$APPLICATION->AuthForm("", false);
endif;

//whether to show the forms
if($ID == 0 && empty($_REQUEST["action"]) || CSubscription::IsAuthorized($ID)):

if($ID>0 && $str_CONFIRMED <> "Y"):
//*************************************
//show confirmation form
//*************************************
?>
<form action="<?=$APPLICATION->GetCurPage()?>" method="GET">
<h4><?echo GetMessage("subscr_title_confirm")?></h4>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr valign="top">
	<td width="60%" class="tablebody" style="padding:5px;">
	<table width="100%" border="0" cellspacing="3" cellpadding="0">
		<tr> 
			<td><font class="tablefieldtext"><?echo GetMessage("subscr_conf_code")?></font><font class="starrequired">*</font></td>
		</tr>
		<tr> 
			<td><input type="text" class="inputtext" name="CONFIRM_CODE" value="<?echo htmlspecialchars($_REQUEST["CONFIRM_CODE"]);?>" size="20"></td>
		</tr>
		<tr> 
			<td><font style="font-size:4px">&nbsp;<br></font></td>
		</tr>
		<tr> 
			<td><font class="tablefieldtext"><?echo GetMessage("subscr_conf_date")?></font></td>
		</tr>
		<tr> 
			<td><font class="tablebodytext"><?echo $str_DATE_CONFIRM;?></font></td>
		</tr>
	</table>
	</td>
	<td width="0%" class="text">&nbsp;</td>
	<td width="0%" class="tableborder"><img src="/bitrix/images/1.gif" width="1" height="1" alt=""></td>
	<td width="0%" class="text">&nbsp;&nbsp;</td>
	<td width="40%"><font class="text"><?echo GetMessage("subscr_conf_note1")?> <a title="<?echo GetMessage("adm_send_code")?>" href="<?echo $APPLICATION->GetCurPage()?>?ID=<?echo $ID?>&amp;action=sendcode"><?echo GetMessage("subscr_conf_note2")?></a>.</font></td>
</tr>
</table>
<p>
<input type="submit" class="inputbutton" name="" value="<?echo GetMessage("subscr_conf_button")?>">
<input type="hidden" name="ID" value="<?echo $str_ID;?>">
</p>
</form>

<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td class="tableborder"><img src="/bitrix/images/1.gif" width="1" height="2" alt=""></td></tr></table>
<?
	//end of confirmation form
endif;
?>

<form action="<?echo $APPLICATION->GetCurPage()?>" method="POST">
<?
$sRub = "";
for($i=0; $i<count($_REQUEST["sf_RUB_ID"]); $i++)
	$sRub .= "&sf_RUB_ID[]=".urlencode($_REQUEST["sf_RUB_ID"][$i]);
$sRub = htmlspecialchars($sRub);

if($USER->IsAuthorized() && ($ID == 0 || $str_USER_ID == 0)):
//*************************************
//show current authorization section
//*************************************
?>
<h4><?echo GetMessage("subscr_title_auth")?></h4>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr valign="top">
	<td width="60%" class="tablebody" style="padding:5px;"><font class="tablebodytext"><?echo GetMessage("adm_auth_user")?> 
<?echo htmlspecialchars($USER->GetFullName());?> [<?echo htmlspecialchars($USER->GetLogin())?>].</font></td>
	<td width="0%" class="text">&nbsp;</td>
	<td width="0%" class="tableborder"><img src="/bitrix/images/1.gif" width="1" height="1" alt=""></td>
	<td width="0%" class="text">&nbsp;&nbsp;</td>
	<td width="40%"><font class="text">
	<?if($ID==0):?>
	<?echo GetMessage("subscr_auth_logout1")?> <a href="<?echo $APPLICATION->GetCurPage()?>?logout=YES&amp;sf_EMAIL=<?echo htmlspecialchars(urlencode($_REQUEST["sf_EMAIL"]))?><?echo $sRub?>"><?echo GetMessage("adm_auth_logout")?></a><?echo GetMessage("subscr_auth_logout2")?><br>
	<?else:?>
	<?echo GetMessage("subscr_auth_logout3")?> <a href="<?echo $APPLICATION->GetCurPage()?>?logout=YES&amp;sf_EMAIL=<?echo htmlspecialchars(urlencode($_REQUEST["sf_EMAIL"]))?><?echo $sRub?>"><?echo GetMessage("adm_auth_logout")?></a><?echo GetMessage("subscr_auth_logout4")?><br>
	<?endif;?>
	</font></td>
</tr>
</table>
<?
	//end of current authorization section
endif;//$USER->IsAuthorized()

if($ID==0 && !$USER->IsAuthorized()): 
//*************************************************
//show authorization section for new subscription
//*************************************************
?>
	<?
	if(!$bAllowAnonymous || $bAllowAnonymous && $bShowAuthLinks):
	?>

	<h4><?echo GetMessage("subscr_title_auth2")?></h4>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr valign="top">
	<td width="60%" class="tablebody" style="padding:5px;">
	<?if($bAllowAnonymous && $_REQUEST["authorize"]<>"YES" && $_REQUEST["register"]<>"YES"):?>
	<font class="tablebodytext">
		<?echo GetMessage("adm_auth1")?> <a href="<?echo $APPLICATION->GetCurPage()?>?authorize=YES&amp;sf_EMAIL=<?echo htmlspecialchars(urlencode($_REQUEST["sf_EMAIL"]))?><?echo $sRub?>"><?echo GetMessage("adm_auth2")?></a>.
		<?if($bAllowRegister):?>
			<br><br>
			<?echo GetMessage("adm_reg1")?> 
			<a href="<?echo $APPLICATION->GetCurPage()?>?register=YES&amp;sf_EMAIL=<?echo htmlspecialchars(urlencode($_REQUEST["sf_EMAIL"]))?><?echo $sRub?>"><?echo GetMessage("adm_reg2")?></a>.
			 <br><br><?echo GetMessage("adm_reg_text")?>
		<?endif;//$bAllowRegister?>
	<br></font>
	<?else: //$bAllowAnonymous && $authorize<>"YES"?>
	<table width="100%" border="0" cellspacing="3" cellpadding="0">
		<?
		if(!$bAllowAnonymous || $_REQUEST["authorize"]=="YES"):
			//show login form
		?>
		<tr> 
			<td><font class="tablebodytext"><b><?echo GetMessage("adm_auth_exist")?></b></font></td>
		</tr>
		<tr> 
			<td><font class="tablefieldtext"><?echo GetMessage("adm_auth_login")?></font><font class="starrequired">*</font></td>
		</tr>
		<tr>
			<td><font size="-1"><input type="text" class="inputtext" name="LOGIN" value="<?echo htmlspecialchars((isset($_REQUEST["LOGIN"])? $_REQUEST["LOGIN"]:$sLastLogin))?>" size="20"></font></td>
		</tr>
		<tr> 
			<td><font class="tablefieldtext"><?echo GetMessage("adm_auth_pass")?></font><font class="starrequired">*</font></td>
		</tr>
		<tr> 
			<td><input type="password" class="inputtext" name="PASSWORD" size="20" value="<?echo htmlspecialchars($_REQUEST["PASSWORD"])?>"></td>
		</tr>
		<?
			//end of login form
		endif;
		?>
		<?
		if((!$bAllowAnonymous || $_REQUEST["register"]=="YES") && $bAllowRegister):
			//show registration form
		?>
			<?if(!$bAllowAnonymous || $_REQUEST["authorize"]=="YES"):?>
		<tr> 
			<td><font class="tablebodytext">&nbsp;</font></td>
		</tr>
			<?endif?>
		<tr valign="top"> 
			<td><font class="tablebodytext"><b><?echo GetMessage("adm_reg_new")?></b></font></td>
		</tr>
		<tr> 
			<td><font class="tablefieldtext"><?echo GetMessage("adm_reg_login")?></font><font class="starrequired">*</font></td>
		</tr>
		<tr> 
			<td><input type="text" class="inputtext" name="NEW_LOGIN" value="<?echo htmlspecialchars($_REQUEST["NEW_LOGIN"])?>" size="20"></td>
		</tr>
		<tr> 
			<td><font class="tablefieldtext"><?echo GetMessage("adm_reg_pass")?></font><font class="starrequired">*</font></td>
		</tr>
		<tr> 
			<td><input type="password" class="inputtext" name="NEW_PASSWORD" size="20" value="<?echo htmlspecialchars($_REQUEST["NEW_PASSWORD"])?>"></td>
		</tr>
		<tr> 
			<td><font class="tablefieldtext"><?echo GetMessage("adm_reg_pass_conf")?></font><font class="starrequired">*</font></td>
		</tr>
		<tr> 
			<td><input type="password" class="inputtext" name="CONFIRM_PASSWORD" size="20" value="<?echo htmlspecialchars($_REQUEST["CONFIRM_PASSWORD"])?>"></td>
		</tr>
		<?
		/* CAPTCHA */
		if (COption::GetOptionString("main", "captcha_registration", "N") == "Y")
		{
			?>
			<tr>
				<td><font class="tablefieldtext"><?=GetMessage("subscr_CAPTCHA_REGF_TITLE")?></font></td>
			</tr>
			<tr>
				<td align="left" width="99%" class="tablebody">
					<?
					$capCode = $GLOBALS["APPLICATION"]->CaptchaGetCode();
					?>
					<input type="hidden" name="captcha_sid" value="<?= htmlspecialchars($capCode) ?>">
					<img src="/bitrix/tools/captcha.php?captcha_sid=<?= htmlspecialchars($capCode) ?>" width="180" height="40">
				</td>
			</tr>
			<tr>
				<td><font class="tablefieldtext"><?=GetMessage("subscr_CAPTCHA_REGF_PROMT")?></font><font class="starrequired">*</font></td>
			</tr>
			<tr>
				<td><input type="text" name="captcha_word" size="30" maxlength="50" value="" class="inputtext"></td>
			</tr>
			<?
		}
		/* CAPTCHA */
		?>
		<?
			//end of registration form
		endif;//$bAllowRegister
		?>
	</table>
<?
	endif; //$bAllowAnonymous && $authorize<>"YES"
?>
</td>
	<td width="0%" class="text">&nbsp;</td>
	<td width="0%" class="tableborder"><img src="/bitrix/images/1.gif" width="1" height="1" alt=""></td>
	<td width="0%" class="text">&nbsp;&nbsp;</td>
	<td width="40%"><font class="text">
	<?if($bAllowAnonymous):?>
		<?echo GetMessage("subscr_auth_note")?><br>
	<?else:?>
		<?echo GetMessage("adm_must_auth")?><br>
	<?endif;?>
	</font></td>
</tr>
</table>
	<?
		//end of authorization section
	endif; // !$bAllowAnonymous || $bAllowAnonymous && COption::GetOptionString("subscribe", "show_auth_links") == "Y"
	?>
<?endif; //!$USER->IsAuthorized()?>


<?
//***********************************
//setting section
//***********************************
?>
<h4><?echo GetMessage("subscr_title_settings")?></h4>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr valign="top">
	<td width="60%" class="tablebody" style="padding:5px;">		  
	<table width="100%" border="0" cellspacing="3" cellpadding="0">
		<tr> 
			<td><font class="tablefieldtext"><?echo GetMessage("subscr_email")?></font><font class="starrequired">*</font></td>
		</tr>
		<tr> 
			<td><input type="text" class="inputtext" name="EMAIL" value="<?echo $str_EMAIL;?>" size="30" maxlength="255"></td>
		</tr>
		<tr> 
			<td><font style="font-size:4px">&nbsp;<br></font></td>
		</tr>
	    <tr> 
			<td><font class="tablefieldtext"><?echo GetMessage("subscr_rub")?></font><font class="starrequired">*</font></td>
		</tr>
		<tr> 
			<td>
<?
if(!is_array($_REQUEST["RUB_ID"]))
	$_REQUEST["RUB_ID"] = array();
$aSubscrRub = CSubscription::GetRubricArray($ID);
$arFilter = array("ACTIVE"=>"Y", "LID"=>LANG);
if($SHOW_HIDDEN<>"Y")
	$arFilter["VISIBLE"]="Y";
$rub = CRubric::GetList(array("LID"=>"ASC", "SORT"=>"ASC", "NAME"=>"ASC"), $arFilter);
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<?
$n=1;
while(($rub_arr = $rub->Fetch())):
	$bChecked = (
		!is_array($_REQUEST["sf_RUB_ID"]) && in_array($rub_arr["ID"], ($bVarsFromForm? $_REQUEST["RUB_ID"]:$aSubscrRub)) || 
		is_array($_REQUEST["sf_RUB_ID"]) && in_array($rub_arr["ID"], $_REQUEST["sf_RUB_ID"])
	);
?>
<tr>
	<td width="0%"><input type="checkbox" class="inputcheckbox" name="RUB_ID[]" id="RUB_ID_<?echo $n?>" value="<?echo $rub_arr["ID"]?>"<?if($bChecked) echo " checked"?>></td>
	<td width="100%"><font class="tablebodytext"><label for="RUB_ID_<?echo $n?>"><?echo htmlspecialchars($rub_arr["NAME"])?></label></font></td>
</tr>
<?
	$n++;
endwhile;
?>
</table>
				</td>
		  </tr>
		<tr> 
			<td><font style="font-size:4px">&nbsp;<br></font></td>
		</tr>
		  <tr> 
			<td><font class="tablefieldtext"><?echo GetMessage("subscr_fmt")?></font></td>
		  </tr>
		  <tr> 
			<td><font class="tablebodytext"><input type="radio" class="inputradio" name="FORMAT" id="FORMAT_1" value="text"<?if($str_FORMAT == "text") echo " checked"?>><label for="FORMAT_1"><?echo GetMessage("subscr_text")?></label>&nbsp;/
<input type="radio" class="inputradio" name="FORMAT" id="FORMAT_2" value="html"<?if($str_FORMAT == "html") echo " checked"?>><label for="FORMAT_2">HTML</label></font></td>
		  </tr>
	</table>
	</td>
	<td width="0%" class="text">&nbsp;</td>
	<td width="0%" class="tableborder"><img src="/bitrix/images/1.gif" width="1" height="1" alt=""></td>
	<td width="0%" class="text">&nbsp;&nbsp;</td>
	<td width="40%"><font class="text">
	<?echo GetMessage("subscr_settings_note1")?> <br><br>
	<?echo GetMessage("subscr_settings_note2")?><br>
	</font></td>
</tr>
</table>

<p>
<input type="submit" class="inputbutton" name="Save" value="<?echo ($ID > 0? GetMessage("subscr_upd"):GetMessage("subscr_add"))?>">
<input type="reset" class="inputbutton" value="<?echo GetMessage("subscr_reset")?>" name="reset">
<input type="hidden" name="PostAction" value="<?echo ($ID>0? "Update":"Add")?>">
<input type="hidden" name="ID" value="<?echo $str_ID;?>">
<?if($_REQUEST["register"] == "YES"):?>
<input type="hidden" name="register" value="YES">
<?endif;?>
<?if($_REQUEST["authorize"]=="YES"):?>
<input type="hidden" name="authorize" value="YES">
<?endif;?>
</p>
</form>


<?
if($ID>0):
//***********************************
//status and unsubscription/activation section
//***********************************
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td class="tableborder"><img src="/bitrix/images/1.gif" width="1" height="2" alt=""></td></tr></table>

<h4><?echo GetMessage("subscr_title_status")?></h4>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr valign="top">
	<td width="60%" class="tablebody" style="padding:5px;">
	<table border="0" cellspacing="3" cellpadding="0">
		<tr> 
			<td><font class="tablefieldtext"><?echo GetMessage("subscr_conf")?></font></td>
			<td><font class="tablebodytext">&nbsp;</font></td>
			<td><font class="tablebodytext <?echo ($str_CONFIRMED == "Y"? "successcolor":"errorcolor")?>"><?echo ($str_CONFIRMED == "Y"? GetMessage("subscr_yes"):GetMessage("subscr_no"));?></font></td>
		</tr>
<?if($str_CONFIRMED == "Y"):?>
		<tr> 
			<td><font class="tablefieldtext"><?echo GetMessage("subscr_act")?></font></td>
			<td><font class="tablebodytext">&nbsp;</font></td>
			<td><font class="tablebodytext <?echo ($str_ACTIVE == "Y"? "successcolor":"errorcolor")?>"><?echo ($str_ACTIVE == "Y"? GetMessage("subscr_yes"):GetMessage("subscr_no"));?></font></td>
		</tr>
<?endif;?>
		<tr> 
			<td><font class="tablefieldtext"><?echo GetMessage("adm_id")?></font></td>
			<td><font class="tablebodytext">&nbsp;</font></td>
			<td><font class="tablebodytext"><?echo $str_ID;?></font></td>
		</tr>
		<tr> 
			<td><font class="tablefieldtext"><?echo GetMessage("subscr_date_add")?></font></td>
			<td><font class="tablebodytext">&nbsp;</font></td>
			<td><font class="tablebodytext"><?echo $str_DATE_INSERT;?></font></td>
		</tr>
<?if($str_DATE_UPDATE <> ""):?>
		<tr> 
			<td><font class="tablefieldtext"><?echo GetMessage("subscr_date_upd")?></font></td>
			<td><font class="tablebodytext">&nbsp;</font></td>
			<td><font class="tablebodytext"><?echo $str_DATE_UPDATE;?></font></td>
		</tr>
<?endif;?>
	</table>

	</td>
	<td width="0%" class="text">&nbsp;</td>
	<td width="0%" class="tableborder"><img src="/bitrix/images/1.gif" width="1" height="1" alt=""></td>
	<td width="0%" class="text">&nbsp;&nbsp;</td>
	<td width="40%"><font class="text">
<?if($str_CONFIRMED <> "Y"):?>
<?echo GetMessage("subscr_title_status_note1")?><br>	
<?elseif($str_ACTIVE == "Y"):?>
<?echo GetMessage("subscr_title_status_note2")?><br><br>
<?echo GetMessage("subscr_status_note3")?><br>
<?else:?>
<?echo GetMessage("subscr_status_note4")?><br><br>
<?echo GetMessage("subscr_status_note5")?><br>
<?endif;?>
	</font></td>
</tr>
</table>
<?if($str_CONFIRMED == "Y"):?>
<form action="<?$APPLICATION->GetCurPage()?>" method="GET">
<?if($str_ACTIVE == "Y"):?>
<input type="submit" class="inputbutton" name="" value="<?echo GetMessage("subscr_unsubscr")?>">
<input type="hidden" name="action" value="unsubscribe">
<?else:?>
<input type="submit" class="inputbutton" name="" value="<?echo GetMessage("subscr_activate")?>">
<input type="hidden" name="action" value="activate">
<?endif;?>
<input type="hidden" name="ID" value="<?echo $str_ID;?>">
</form>
<?endif; //$str_CONFIRMED == "Y"?>

<?
	//end of unsubscription/activation section
endif;
?>

<p><font class="starrequired">*</font><font class="text"><?echo GetMessage("subscr_req")?></font></p>


<?
else://$ID == 0 || CSubscription::IsAuthorized($ID)
//******************************************
//subscription authorization form
//******************************************
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr valign="top">
	<td width="50%">
<h4><?echo GetMessage("subscr_auth_sect_title")?></h4>	

<p><font class="text"><?echo GetMessage("adm_auth_note")?></font></p>

<form action="<?echo $APPLICATION->GetCurPage().($_SERVER["QUERY_STRING"]<>""? "?".htmlspecialchars($_SERVER["QUERY_STRING"]):"")?>" method="POST">
<table border="0" cellspacing="2" cellpadding="0">
<tr>
	<td><font class="text">e-mail</font></td>
</tr>
<tr>
	<td><input type="text" class="inputtext" name="sf_EMAIL" size="20" value="<?echo htmlspecialchars($_REQUEST["sf_EMAIL"]);?>" title="<?echo GetMessage("subscr_auth_email")?>"></td>
</tr>
<tr>
	<td><font class="text"><?echo GetMessage("subscr_auth_pass")?></font></td>
</tr>
<tr>
	<td><input type="password" class="inputtext" name="AUTH_PASS" size="20" value="" title="<?echo GetMessage("subscr_auth_pass_title")?>"></td>
</tr>
<tr>
	<td><input type="submit" class="inputbutton" name="" value="<?echo GetMessage("adm_auth_butt")?>"></td>
</tr>
</table>
<input type="hidden" name="action" value="authorize">
</form>


	</td>
	<td width="0%" class="text">&nbsp;</td>
	<td width="0%" class="tableborder"><img src="/bitrix/images/1.gif" width="1" height="1" alt=""></td>
	<td width="0%" class="text">&nbsp;&nbsp;</td>
	<td width="50%">
<h4><?echo GetMessage("subscr_pass_title")?></h4>	

<p><font class="text"><?echo GetMessage("subscr_pass_note")?></font></p>
<form action="<?=$APPLICATION->GetCurPage()?>">
<table border="0" cellspacing="2" cellpadding="0">
<tr>
	<td><font class="text">e-mail</font></td>
</tr>
<tr>
	<td><input type="text" class="inputtext" name="sf_EMAIL" size="20" value="<?echo htmlspecialchars($_REQUEST["sf_EMAIL"]);?>" title="<?echo GetMessage("subscr_auth_email")?>"></td>
</tr>
<tr>
	<td><input type="submit" class="inputbutton" name="" value="<?echo GetMessage("subscr_pass_button")?>"></td>
</tr>
</table>
<input type="hidden" name="action" value="sendpassword">
</form>

	</td>
</tr>
</table>
<?
endif; //$ID == 0 || CSubscription::IsAuthorized($ID)
?>
<?
else: //IncludeModule("subscribe")
	$APPLICATION->SetTitle(GetMessage("adm_module"));
	ShowError(GetMessage("adm_module1"));
endif;
?>
