<?
IncludeModuleLangFile(__FILE__);

class CCloudStorageService_ClodoRU extends CCloudStorageService_OpenStackStorage
{
	function GetObject()
	{
		return new CCloudStorageService_ClodoRU();
	}

	function GetID()
	{
		return "clodo_storage";
	}

	function GetName()
	{
		return "Clodo.ru";
	}

	function GetSettingsHTML($arBucket, $bServiceSet, $cur_SERVICE_ID, $bVarsFromForm)
	{
		if($bVarsFromForm)
			$arSettings = $_POST["SETTINGS"][$this->GetID()];
		else
			$arSettings = unserialize($arBucket["SETTINGS"]);

		if(!is_array($arSettings))
			$arSettings = array("HOST" => "testapi.clodo.ru", "USER" => "", "KEY" => "");

		$htmlID = htmlspecialchars($this->GetID());

		$result = '
		<tr id="SETTINGS_2_'.$htmlID.'" style="display:'.($cur_SERVICE_ID == $this->GetID() || !$bServiceSet? '': 'none').'" class="settings-tr">
			<td><span class="required">*</span>'.GetMessage("CLO_STORAGE_CLODO_EDIT_HOST").':</td>
			<td><input type="hidden" name="SETTINGS['.$htmlID.'][HOST]" id="'.$htmlID.'HOST" value="'.htmlspecialchars($arSettings['HOST']).'"><input type="text" size="55" name="'.$htmlID.'INP_HOST" id="'.$htmlID.'INP_HOST" value="'.htmlspecialchars($arSettings['HOST']).'" '.($arBucket['READ_ONLY'] == 'Y'? '"disabled"': '').' onchange="BX(\''.$htmlID.'HOST\').value = this.value"></td>
		</tr>
		<tr id="SETTINGS_0_'.$htmlID.'" style="display:'.($cur_SERVICE_ID == $this->GetID() || !$bServiceSet? '': 'none').'" class="settings-tr">
			<td><span class="required">*</span>'.GetMessage("CLO_STORAGE_CLODO_EDIT_USER").':</td>
			<td><input type="hidden" name="SETTINGS['.$htmlID.'][USER]" id="'.$htmlID.'USER" value="'.htmlspecialchars($arSettings['USER']).'"><input type="text" size="55" name="'.$htmlID.'INP_" id="'.$htmlID.'INP_USER" value="'.htmlspecialchars($arSettings['USER']).'" '.($arBucket['READ_ONLY'] == 'Y'? '"disabled"': '').' onchange="BX(\''.$htmlID.'USER\').value = this.value"></td>
		</tr>
		<tr id="SETTINGS_1_'.$htmlID.'" style="display:'.($cur_SERVICE_ID == $this->GetID() || !$bServiceSet? '': 'none').'" class="settings-tr">
			<td><span class="required">*</span>'.GetMessage("CLO_STORAGE_CLODO_EDIT_KEY").':</td>
			<td><input type="hidden" name="SETTINGS['.$htmlID.'][KEY]" id="'.$htmlID.'KEY" value="'.htmlspecialchars($arSettings['KEY']).'"><input type="text" size="55" name="'.$htmlID.'INP_KEY" id="'.$htmlID.'INP_KEY" value="'.htmlspecialchars($arSettings['KEY']).'" autocomplete="off" '.($arBucket['READ_ONLY'] == 'Y'? '"disabled"': '').' onchange="BX(\''.$htmlID.'KEY\').value = this.value"></td>
		</tr>
		';
		return $result;
	}

	function CheckSettings($arBucket, &$arSettings)
	{
		if(is_array($arSettings))
			$arSettings["HOST"] = "testapi.clodo.ru";

		return parent::CheckSettings($arBucket, $arSettings);
	}
}
?>