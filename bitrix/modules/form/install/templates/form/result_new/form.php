<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $FORM;

echo $FORM->ShowFormErrors();
echo $FORM->ShowFormNote();

if (!$FORM->isFormNote()) 
{

?>

<?=$FORM->ShowFormHeader()?>

			<table cellspacing="0" cellpadding="0" width="100%" border="0">
<?
	if ($FORM->isFormDescription() || $FORM->isFormTitle() || $FORM->isFormImage()) 
	{ 
?>
				<tr>
					<td><?

		/***********************************************************************************
										form header
		***********************************************************************************/

		if ($FORM->isFormTitle()) 
		{
?>
					<p class="h2"><b><?=$FORM->ShowFormTitle("titletext")?></b></p>
<?
		} //endif ;

		if ($FORM->isFormImage())
		{
?>
					<p>
					<table cellpadding="3" cellspacing="0" border="0">
						<tr>
							<td><?=$FORM->ShowFormImage()?></td>
						</tr>
					</table>
					</p>
<?
		} //endif
?>
<p class="text">
<?=$FORM->ShowFormDescription()?>
</p>
</td>
				<tr><td>&nbsp;</td></tr>
				<? 
	} // endif 
				?>
				<tr>
					<td><?

	/***********************************************************************************
										form questions
	***********************************************************************************/

					?>
					<p>
					<table border="0" cellspacing="0" cellpadding="1" width="100%"  class="tableborder">
						<tr>
							<td width="100%">
								<table cellspacing="0" cellpadding="10" class="tablebody" width="100%" border="0">
									<?
									reset($FORM->arQuestions);
									while (list($key, $arQuestion) = each($FORM->arQuestions))
									{
										$FIELD_SID = $arQuestion["SID"];
									?>
											<tr>
												<td valign="top" width="30%" class="tablebodytext">
													<?=$FORM->ShowInputCaption($FIELD_SID);?>
													<?=$FORM->isInputCaptionImage($FIELD_SID) ? "<br />".$FORM->ShowInputCaptionImage($FIELD_SID, 50, 50, "hspace=\"0\" vspace=\"0\" align=\"left\" border=\"0\"", "", true, GetMessage("FORM_ENLARGE")) : ""?>
												</td>
												<td valign="top" width="70%" class="tablebodytext"><?=$FORM->ShowInput($FIELD_SID)?></td>
											</tr>
									<? } //endwhile ?>
								</table>
<?if($FORM->isUseCaptcha()):?>
								<table cellspacing="0" cellpadding="10" border="0" width="100%">
									<tr>
										<td valign="middle" colspan="2" class="tablehead">
											<span class="tableheadtext"><b><?=GetMessage("FORM_CAPTCHA_TABLE_TITLE")?></b></span>
										</td>
									</tr>
									<tr valign="middle"> 
										<td align="right" width="30%" class="tablebody">&nbsp;</td>
										<td align="left" width="70%" class="tablebody"><?=$FORM->ShowCaptchaImage()?></td>
									</tr>
									<tr valign="middle"> 
										<td align="right" class="tablebody"><font class="tablebodytext"><?=GetMessage("FORM_CAPTCHA_FIELD_TITLE")?><?=$FORM->ShowRequired()?></font></td>
										<td align="left" class="tablebody"><?=$FORM->ShowCaptchaField()?></td>
									</tr>
								</table>
<?endif // isUseCaptcha?>
							</td>
						</tr>
					</table></td>
				</tr>
			</table>
			<?=$FORM->ShowRequired();?><span class="smalltext"> - <?=GetMessage("FORM_REQUIRED_FIELDS")?></span>
			<p align="left">
			<?=$FORM->ShowSubmitButton("", "inputbuttonflat")?>&nbsp;&nbsp;<?=$FORM->ShowApplyButton("", "inputbuttonflat")?>&nbsp;&nbsp;<?=$FORM->ShowResetButton("", "inputbuttonflat");?>
			</p>
			<?=$FORM->ShowFormFooter()?>
		<?
} //endif (!isFormNote());

?>